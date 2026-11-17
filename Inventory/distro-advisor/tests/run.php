<?php
declare(strict_types=1);

// Run: php tests/run.php
require __DIR__ . '/../src/Planner.php';

use DistroAdvisor\Planner;

$planner = new Planner(__DIR__ . '/../data', '2026-11-17');  // fixed date so support checks are repeatable
$catalog = $planner->catalog();
$fail = 0;
$assert = function (bool $ok, string $msg) use (&$fail): void {
    if (!$ok) { $fail++; echo "FAIL: $msg\n"; }
};

$assert(count($catalog) === 39, 'catalog should contain 39 distros, got ' . count($catalog));

$base = [
    'boot_mode' => 'uefi', 'arch' => 'x86_64', 'ram_gb' => 8, 'disk_gb' => 512,
    'disk_type' => 'nvme', 'disk_device' => '/dev/nvme0n1', 'existing_os' => 'none',
    'gpu' => 'intel', 'use' => 'desktop', 'secure_boot' => true,
    'hibernate' => false, 'separate_home' => false,
];

$scenarios = [
    'uefi-default'   => [],
    'bios-msdos'     => ['boot_mode' => 'bios', 'secure_boot' => false, 'disk_type' => 'hdd', 'disk_device' => '/dev/sda', 'disk_gb' => 500],
    'bios-gpt-3tb'   => ['boot_mode' => 'bios', 'secure_boot' => false, 'disk_gb' => 3000, 'disk_device' => '/dev/sda'],
    'home+hibernate' => ['separate_home' => true, 'hibernate' => true, 'ram_gb' => 16],
    'dualboot'       => ['existing_os' => 'windows', 'free_gb' => 200],
    'small'          => ['disk_gb' => 20, 'ram_gb' => 1, 'disk_type' => 'ssd'],
    'arm'            => ['arch' => 'aarch64', 'disk_type' => 'emmc', 'ram_gb' => 4, 'disk_gb' => 64],
];

$count = 0;
foreach ($catalog as $d) {
    $versionIds = $d['versions'] ? array_column($d['versions'], 'id') : [''];
    foreach ($versionIds as $vid) foreach ($scenarios as $name => $over) {
        $in = array_replace($base, $over, ['distro' => $d['id'], 'version' => $vid]);
        $r  = $planner->plan($in);
        $count++;
        $tag = "{$d['id']}/{$vid}/$name";
        $assert($r['ok'] === true, "$tag not ok: " . json_encode($r['errors'] ?? []));
        if (!$r['ok']) { continue; }

        foreach ($r['partitions'] as $p) {
            $assert($p['size_mib'] >= 0, "$tag negative size for {$p['key']}");
        }
        if ($r['partitions']) {
            $space = ($in['existing_os'] !== 'none') ? $in['free_gb'] : $in['disk_gb'];
            $sum = array_sum(array_column($r['partitions'], 'size_mib'));
            $assert($sum <= (int) floor($space * 1024) + 1, "$tag partitions ($sum MiB) exceed space");
            $assert(in_array('/', array_column($r['partitions'], 'mount'), true), "$tag has no root partition");
        }
        if ($in['existing_os'] !== 'none') {
            $assert($r['commands'] === [], "$tag dual boot must not emit commands");
        }
        foreach ($r['commands'] as $c) {
            $assert(!preg_match('/[;&|`$]/', preg_replace('/^#.*/', '', $c)), "$tag suspicious char in command: $c");
        }
        $assert(count($r['steps']['prepare']) >= 2, "$tag missing prepare steps");
        $assert(count($r['steps']['install']) >= 1, "$tag missing install steps");
    }
}

// Input validation
$bad = $planner->plan(array_replace($base, ['distro' => 'debian', 'disk_device' => '/dev/sda; rm -rf /']));
$assert($bad['ok'] === false, 'malicious device path must be rejected');
$bad = $planner->plan(array_replace($base, ['distro' => 'nope']));
$assert($bad['ok'] === false, 'unknown distro must be rejected');
$bad = $planner->plan(array_replace($base, ['distro' => 'debian', 'ram_gb' => -1]));
$assert($bad['ok'] === false, 'negative RAM must be rejected');

// Spot checks
$r = $planner->plan(array_replace($base, ['distro' => 'opensuse']));
$keys = array_column($r['partitions'], 'key');
$assert($keys === ['esp', 'swap', 'root'], 'openSUSE UEFI default layout keys: ' . implode(',', $keys));
$assert($r['partitions'][2]['fs'] === 'btrfs', 'openSUSE root should be btrfs');
$assert($r['partitions'][1]['size_mib'] === 8192, 'swap for 8 GB RAM should be 8 GiB');

$r = $planner->plan(array_replace($base, ['distro' => 'ubuntu']));
$assert(!in_array('swap', array_column($r['partitions'], 'key'), true), 'Ubuntu uses swap file');
$r = $planner->plan(array_replace($base, ['distro' => 'ubuntu', 'hibernate' => true]));
$assert(in_array('swap', array_column($r['partitions'], 'key'), true), 'hibernate forces swap partition');

$r = $planner->plan(array_replace($base, ['distro' => 'arch']));
$assert(str_contains(implode("\n", $r['commands']), '/dev/nvme0n1p1'), 'nvme partition naming uses p suffix');

// ---- Versions ----------------------------------------------------------
$levels = fn(array $r, string $lv): string => implode(' | ', array_column(array_filter($r['checks'], fn($c) => $c['level'] === $lv), 'msg'));
$ubuntu = array_values(array_filter($catalog, fn($d) => $d['id'] === 'ubuntu'))[0];
$assert(count($ubuntu['versions']) === 4, 'ubuntu should have 4 versions');
$assert(array_column(array_filter($ubuntu['versions'], fn($v) => $v['default']), 'id') === ['26.04-lts'], 'ubuntu default version is 26.04 LTS');

$r = $planner->plan(array_replace($base, ['distro' => 'ubuntu', 'ram_gb' => 4]));
$assert($r['distro']['version']['id'] === '26.04-lts', 'blank version falls back to the default');
$assert(str_contains($levels($r, 'error'), 'below the minimum'), '26.04 LTS needs 6 GB: 4 GB must be an error');
$r = $planner->plan(array_replace($base, ['distro' => 'ubuntu', 'version' => '24.04-lts', 'ram_gb' => 4]));
$assert($levels($r, 'error') === '', '24.04 LTS runs on 4 GB RAM: ' . $levels($r, 'error'));
$assert($r['distro']['name'] === 'Ubuntu 24.04 LTS', 'full name includes the version, got ' . $r['distro']['name']);

$r = $planner->plan(array_replace($base, ['distro' => 'ubuntu', 'version' => 'server-26.04-lts', 'ram_gb' => 2, 'disk_gb' => 20, 'use' => 'server']));
$assert($levels($r, 'error') === '', 'Ubuntu Server runs on 2 GB RAM / 20 GB: ' . $levels($r, 'error'));
$assert(str_contains(implode(' ', $r['steps']['install']), 'Subiquity') || str_contains(implode(' ', $r['steps']['install']), 'text installer'), 'server version uses its own install steps');

$r = $planner->plan(array_replace($base, ['distro' => 'fedora', 'version' => '43']));
$assert(str_contains($levels($r, 'warn'), 'ends in 14 days'), 'Fedora 43 should warn it ends in 14 days: ' . $levels($r, 'warn'));
$r = $planner->plan(array_replace($base, ['distro' => 'debian', 'version' => '12']));
$assert(str_contains($levels($r, 'warn'), 'limited updates'), 'Debian 12 should be flagged limited support');
$r = $planner->plan(array_replace($base, ['distro' => 'opensuse', 'version' => 'tumbleweed']));
$assert(str_contains(implode(' ', array_column($r['checks'], 'msg')), 'rolling release'), 'Tumbleweed is rolling');
$assert(str_contains(implode(' ', $r['steps']['install']), 'Agama'), 'Tumbleweed uses the Agama steps');

$late = new Planner(__DIR__ . '/../data', '2035-06-01');
$r = $late->plan(array_replace($base, ['distro' => 'rhel', 'version' => '10']));
$assert(str_contains($levels($r, 'error'), 'ended'), 'RHEL 10 after May 2035 must show support ended');
$r = $late->plan(array_replace($base, ['distro' => 'rhel', 'version' => '8']));
$assert(str_contains($levels($r, 'error'), 'ended'), 'RHEL 8 after 2029 must show support ended');

$bad = $planner->plan(array_replace($base, ['distro' => 'ubuntu', 'version' => 'nope']));
$assert($bad['ok'] === false, 'unknown version must be rejected');
$bad = $planner->plan(array_replace($base, ['distro' => 'ubuntu', 'version' => ['x']]));
$assert($bad['ok'] === true && $bad['distro']['version']['id'] === '26.04-lts', 'non-string version is ignored');
$ok = $planner->plan(array_replace($base, ['distro' => 'arch', 'version' => 'whatever']));
$assert($ok['ok'] === true && $ok['distro']['version'] === null, 'version is ignored for distros without versions');

// Every version has the fields the UI needs.
foreach ($catalog as $d) {
    foreach ($d['versions'] as $v) {
        foreach (['id', 'label', 'short', 'support', 'name', 'archs'] as $k) {
            $assert(isset($v[$k]), "{$d['id']}/{$v['id']} missing $k");
        }
        $assert(in_array($v['support'], ['rolling', 'unknown', 'ended', 'limited', 'ending_soon', 'ok'], true), "{$d['id']}/{$v['id']} bad support state");
    }
}

echo $fail === 0 ? "OK: $count plans generated, all checks passed\n" : "$fail failure(s)\n";
exit($fail === 0 ? 0 : 1);
