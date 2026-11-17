<?php
declare(strict_types=1);

namespace DistroAdvisor;

/**
 * Rules engine: turns hardware specs + a chosen distro into a partition plan,
 * warnings and install steps. Pure PHP, no shell access, no AI: the output is
 * deterministic so the same input always gives the same plan.
 */
final class Planner
{
    private const ARCHS      = ['x86_64', 'aarch64', 'i686', 'armv7'];
    private const BOOT_MODES = ['uefi', 'bios'];
    private const DISK_TYPES = ['nvme', 'ssd', 'hdd', 'emmc'];
    private const EXISTING   = ['none', 'windows', 'linux', 'other'];
    private const GPUS       = ['intel', 'amd', 'nvidia', 'hybrid', 'other'];
    private const USES       = ['desktop', 'dev', 'server', 'minimal'];

    /** @var array<string,array> */
    private array $families;
    /** @var array<string,array> */
    private array $distros = [];
    /** @var array<string,array> distro id => list of versions */
    private array $versions = [];
    private \DateTimeImmutable $today;

    /** $today (YYYY-MM-DD) is only for tests; by default the real date is used. */
    public function __construct(string $dataDir, ?string $today = null)
    {
        $this->today    = new \DateTimeImmutable($today ?? 'today', new \DateTimeZone('UTC'));
        $this->families = $this->loadJson($dataDir . '/families.json');
        foreach ($this->loadJson($dataDir . '/distros.json') as $d) {
            if (!isset($this->families[$d['family']])) {
                throw new \RuntimeException("Distro {$d['id']} has unknown family {$d['family']}");
            }
            $this->distros[$d['id']] = $d;
        }
        $versionFile = $dataDir . '/versions.json';
        if (is_file($versionFile)) {
            foreach ($this->loadJson($versionFile) as $id => $list) {
                if (!isset($this->distros[$id])) {
                    throw new \RuntimeException("versions.json refers to unknown distro $id");
                }
                $this->versions[$id] = $list;
            }
        }
    }

    /** Distro list for the UI (grouped by family, no heavy fields). */
    public function catalog(): array
    {
        $out = [];
        foreach ($this->distros as $d) {
            $out[] = [
                'id'             => $d['id'],
                'name'           => $d['name'],
                'family'         => $d['family'],
                'family_name'    => $this->families[$d['family']]['name'],
                'status'         => $d['status'],
                'install_method' => $d['install_method'],
                'installer'      => $d['installer'],
                'archs'          => $d['archs'],
                'min_ram_gb'     => $d['min_ram_gb'],
                'rec_ram_gb'     => $d['rec_ram_gb'],
                'min_disk_gb'    => $d['min_disk_gb'],
                'rec_disk_gb'    => $d['rec_disk_gb'],
                'secure_boot'    => $d['secure_boot'],
                'website'        => $d['website'],
                'notes'          => $d['notes'] ?? [],
                'verified'       => (bool) ($d['verified'] ?? false),
                'versions'       => array_map(
                    fn(array $v) => $this->versionInfo($d, $v),
                    $this->versions[$d['id']] ?? []
                ),
            ];
        }
        return $out;
    }

    // ------------------------------------------------------------------
    // Versions
    // ------------------------------------------------------------------

    private function findVersion(string $distroId, string $versionId): ?array
    {
        $list = $this->versions[$distroId] ?? [];
        if (!$list) {
            return null;
        }
        foreach ($list as $v) {
            if ($v['id'] === $versionId) {
                return $v;
            }
        }
        foreach ($list as $v) {
            if (!empty($v['default'])) {
                return $v;
            }
        }
        return $list[0];
    }

    /** The distro entry with a version's overrides applied. */
    private function effective(array $d, ?array $v): array
    {
        if ($v === null) {
            return $d;
        }
        $e = $d;
        foreach ($v['overrides'] ?? [] as $k => $val) {
            if ($k === 'layout') {
                $base = $e['layout'] ?? [];
                if (isset($val['root_gb'])) {
                    $val['root_gb'] = array_replace($base['root_gb'] ?? [], $val['root_gb']);
                }
                $e['layout'] = array_replace($base, $val);
            } else {
                $e[$k] = $val;
            }
        }
        $e['notes'] = array_merge($v['notes'] ?? [], $d['notes'] ?? []);
        $e['name']  = $d['name'] . ' ' . $v['short'];
        return $e;
    }

    /** One of: rolling, unknown, ended, limited, ending_soon, ok. */
    private function supportState(array $v): array
    {
        if (($v['status'] ?? '') === 'rolling') {
            return ['state' => 'rolling', 'days' => null];
        }
        if (empty($v['eol'])) {
            return ['state' => 'unknown', 'days' => null];
        }
        $utc  = new \DateTimeZone('UTC');
        $eol  = new \DateTimeImmutable($v['eol'], $utc);
        $days = (int) $this->today->diff($eol)->format('%r%a');
        if ($days < 0) {
            return ['state' => 'ended', 'days' => $days];
        }
        if (!empty($v['limited_from']) && $this->today >= new \DateTimeImmutable($v['limited_from'], $utc)) {
            return ['state' => 'limited', 'days' => $days];
        }
        if ($days <= 90) {
            return ['state' => 'ending_soon', 'days' => $days];
        }
        return ['state' => 'ok', 'days' => $days];
    }

    private function versionInfo(array $d, array $v): array
    {
        $e = $this->effective($d, $v);
        $s = $this->supportState($v);
        return [
            'id'           => $v['id'],
            'label'        => $v['label'],
            'short'        => $v['short'],
            'default'      => (bool) ($v['default'] ?? false),
            'status'       => $v['status'],
            'released'     => $v['released'] ?? null,
            'eol_label'    => $v['eol_label'] ?? null,
            'support'      => $s['state'],
            'support_days' => $s['days'],
            'name'         => $e['name'],
            'archs'        => $e['archs'],
            'min_ram_gb'   => $e['min_ram_gb'],
            'rec_ram_gb'   => $e['rec_ram_gb'],
            'min_disk_gb'  => $e['min_disk_gb'],
            'rec_disk_gb'  => $e['rec_disk_gb'],
            'secure_boot'  => $e['secure_boot'],
            'installer'    => $e['installer'],
            'install_method' => $e['install_method'],
            'verified'     => (bool) ($v['verified'] ?? false),
            'checked'      => $v['checked'] ?? null,
            'sources'      => $v['sources'] ?? [],
        ];
    }

    private function checkSupport(array $d, ?array $v, callable $add): void
    {
        if ($v === null) {
            return;
        }
        $s     = $this->supportState($v);
        $name  = $d['name'];
        $until = $v['eol_label'] ?? ($v['eol'] ?? '');
        switch ($s['state']) {
            case 'rolling':
                $add('info', "$name is a rolling release: it gets continuous updates and has no end-of-life date.");
                break;
            case 'ended':
                $add('error', "Support for $name ended ($until). Choose a newer version for a new install.");
                break;
            case 'limited':
                $add('warn', "$name is past its main support period. Only limited updates (long-term or maintenance support) continue until $until.");
                break;
            case 'ending_soon':
                $add('warn', "Support for $name ends in {$s['days']} days ($until). A new install would need an upgrade soon.");
                break;
            case 'ok':
                $add('info', "$name is supported until $until.");
                break;
        }
    }

    public function plan(array $raw): array
    {
        [$in, $errors] = $this->validate($raw);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $d0     = $this->distros[$in['distro']];
        $ver    = $this->findVersion($d0['id'], $in['version']);
        $d      = $this->effective($d0, $ver);
        $f      = $this->families[$d['family']];
        $layout = $this->mergeLayout($f['layout'], $d['layout'] ?? []);
        $checks = [];
        $add    = static function (string $level, string $msg) use (&$checks): void {
            $checks[] = ['level' => $level, 'msg' => $msg];
        };

        $this->checkRequirements($in, $d, $add);
        $this->checkFirmware($in, $d, $add);
        $this->checkGeneral($in, $d, $f, $add);
        $this->checkSupport($d, $ver, $add);

        $verified = $ver !== null ? (bool) ($ver['verified'] ?? false) : (bool) ($d['verified'] ?? false);
        $method          = $d['install_method'];
        $partitionable   = in_array($method, ['guided_gui', 'guided_tui', 'manual'], true);
        $result = [
            'ok'       => true,
            'distro'   => ['id' => $d['id'], 'name' => $d['name'], 'family' => $f['name'],
                           'installer' => $d['installer'], 'method' => $method,
                           'website' => $d['website'], 'docs' => $d['docs'] ?? null,
                           'verified' => $verified, 'status' => $d['status'],
                           'version' => $ver === null ? null : [
                               'id' => $ver['id'], 'label' => $ver['label'],
                               'released' => $ver['released'] ?? null,
                               'eol_label' => $ver['eol_label'] ?? null,
                               'support' => $this->supportState($ver)['state'],
                               'checked' => $ver['checked'] ?? null,
                               'sources' => $ver['sources'] ?? [],
                           ]],
            'summary'  => [],
            'checks'   => [],
            'partitions' => [],
            'steps'    => [],
            'commands' => [],
        ];

        $prepare = $this->prepareSteps($in, $d);
        $install = [];
        $post    = $f['post_steps'] ?? [];

        if ($partitionable) {
            $space = $in['dual_boot'] ? $in['free_gb'] : $in['disk_gb'];
            $plan  = $this->buildLayout($in, $layout, $space, $add);

            $result['partitions'] = $plan['partitions'];
            $result['summary'] = [
                'boot_mode'  => strtoupper($in['boot_mode']),
                'table'      => $plan['table'],
                'space_gb'   => round($space, 1),
                'swap_style' => $plan['swap_style'],
                'swap_gb'    => $plan['swap_mib'] ? round($plan['swap_mib'] / 1024, 1) : 0,
                'root_fs'    => $layout['default_fs'],
                'lvm'        => (bool) $layout['lvm'],
            ];

            foreach ($plan['notes'] as $n) {
                $add('info', $n);
            }

            // Command-line equivalent: only for a whole-disk install and no LVM.
            if (!$in['dual_boot'] && !$layout['lvm']) {
                $result['commands'] = $this->commands($in, $plan, $method === 'manual');
            } elseif ($in['dual_boot']) {
                $add('info', 'Dual boot: no partitioning commands are generated because they could damage the existing system. Use the installer\'s free-space option, or the layout above inside the free space.');
            }

            $install = $d['install_steps'] ?? $f['install_steps'];
            if ($in['gpu'] === 'nvidia' || $in['gpu'] === 'hybrid') {
                $add('warn', 'NVIDIA graphics: ' . $f['nvidia']);
            }
        } else {
            $install = $d['steps'] ?? [];
            $result['summary'] = ['boot_mode' => strtoupper($in['boot_mode']), 'note' => 'No partition plan for this install type.'];
            $post = [];
        }

        foreach ($d['notes'] ?? [] as $n) {
            $add('info', $n);
        }
        if ($ver !== null && $verified) {
            $add('info', "Checked on {$ver['checked']}: {$ver['checked_note']}. Other details (default layout, install steps) are general guidance.");
        } elseif (!$verified) {
            $add('warn', 'This data has not been verified against the official documentation yet. Check the requirements and steps on the distribution\'s website before installing.');
        }

        // Sort: errors first, then warnings, then info.
        $order = ['error' => 0, 'warn' => 1, 'info' => 2];
        usort($checks, static fn($a, $b) => $order[$a['level']] <=> $order[$b['level']]);

        $result['checks'] = $checks;
        $result['steps']  = ['prepare' => $prepare, 'install' => $install, 'post' => $post];
        return $result;
    }

    // ------------------------------------------------------------------
    // Validation
    // ------------------------------------------------------------------

    private function validate(array $r): array
    {
        $e = [];
        $c = [];

        $c['distro'] = (string) ($r['distro'] ?? '');
        if (!isset($this->distros[$c['distro']])) {
            $e[] = 'Please choose a distribution.';
        }

        // Version: optional. Blank means the distro's default version.
        $c['version'] = '';
        if (isset($this->distros[$c['distro']]) && !empty($this->versions[$c['distro']])) {
            $want = is_string($r['version'] ?? null) ? $r['version'] : '';
            if ($want !== '' && !in_array($want, array_column($this->versions[$c['distro']], 'id'), true)) {
                $e[] = 'Unknown version for this distribution.';
            } else {
                $c['version'] = $want;
            }
        }

        $c['boot_mode']     = $this->enumVal($r, 'boot_mode', self::BOOT_MODES, 'boot mode', $e);
        $c['arch']          = $this->enumVal($r, 'arch', self::ARCHS, 'CPU architecture', $e);
        $c['disk_type']     = $this->enumVal($r, 'disk_type', self::DISK_TYPES, 'disk type', $e);
        $c['existing_os']   = $this->enumVal($r, 'existing_os', self::EXISTING, 'existing system', $e);
        $c['gpu']           = $this->enumVal($r, 'gpu', self::GPUS, 'graphics card', $e);
        $c['use']           = $this->enumVal($r, 'use', self::USES, 'use case', $e);
        $c['ram_gb']        = $this->numVal($r, 'ram_gb', 0.25, 4096, 'RAM', $e);
        $c['disk_gb']       = $this->numVal($r, 'disk_gb', 1, 100000, 'disk size', $e);
        $c['secure_boot']   = filter_var($r['secure_boot'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $c['hibernate']     = filter_var($r['hibernate'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $c['separate_home'] = filter_var($r['separate_home'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $dev = trim((string) ($r['disk_device'] ?? '/dev/sda'));
        if ($dev === '') {
            $dev = '/dev/sda';
        }
        if (!preg_match('#^/dev/[A-Za-z0-9]{2,20}$#', $dev)) {
            $e[] = 'Disk device must look like /dev/sda, /dev/nvme0n1 or /dev/mmcblk0.';
        }
        $c['disk_device'] = $dev;

        $c['dual_boot'] = ($c['existing_os'] ?? 'none') !== 'none';
        $c['free_gb']   = $c['disk_gb'] ?? 0;
        if ($c['dual_boot']) {
            $c['free_gb'] = $this->numVal($r, 'free_gb', 1, 100000, 'free space for Linux', $e);
            if ($c['free_gb'] !== null && $c['disk_gb'] !== null && $c['free_gb'] > $c['disk_gb']) {
                $e[] = 'Free space cannot be larger than the disk.';
            }
        }
        return [$c, $e];
    }

    private function enumVal(array $r, string $k, array $allowed, string $label, array &$e): ?string
    {
        $v = $r[$k] ?? null;
        if (!is_string($v) || !in_array($v, $allowed, true)) {
            $e[] = "Invalid value for $label.";
            return null;
        }
        return $v;
    }

    private function numVal(array $r, string $k, float $min, float $max, string $label, array &$e): ?float
    {
        $v = $r[$k] ?? null;
        if (!is_numeric($v) || (float) $v < $min || (float) $v > $max) {
            $e[] = "Enter a valid number for $label (between $min and $max).";
            return null;
        }
        return (float) $v;
    }

    // ------------------------------------------------------------------
    // Checks
    // ------------------------------------------------------------------

    private function checkRequirements(array $in, array $d, callable $add): void
    {
        if ($d['status'] === 'legacy') {
            $add('warn', "{$d['name']} is discontinued or no longer maintained. It is not recommended for a new install.");
        }
        if (!in_array($in['arch'], $d['archs'], true)) {
            $add('error', "{$d['name']} does not provide images for {$in['arch']} (available: " . implode(', ', $d['archs']) . ').');
        }
        $space = $in['dual_boot'] ? $in['free_gb'] : $in['disk_gb'];
        $label = $in['dual_boot'] ? 'free space' : 'disk';

        if ($d['min_ram_gb'] !== null) {
            if ($in['ram_gb'] < $d['min_ram_gb']) {
                $add('error', "RAM ({$in['ram_gb']} GB) is below the minimum for {$d['name']} ({$d['min_ram_gb']} GB).");
            } elseif ($d['rec_ram_gb'] !== null && $in['ram_gb'] < $d['rec_ram_gb']) {
                $add('warn', "RAM ({$in['ram_gb']} GB) meets the minimum but is below the recommended {$d['rec_ram_gb']} GB. Expect slowdowns with a full desktop.");
            }
        }
        if ($d['min_disk_gb'] !== null) {
            if ($space < $d['min_disk_gb']) {
                $add('error', "Available $label ({$this->fmt($space)} GB) is below the minimum for {$d['name']} ({$d['min_disk_gb']} GB).");
            } elseif ($d['rec_disk_gb'] !== null && $space < $d['rec_disk_gb']) {
                $add('warn', "Available $label ({$this->fmt($space)} GB) is below the recommended {$d['rec_disk_gb']} GB.");
            }
        }
    }

    private function checkFirmware(array $in, array $d, callable $add): void
    {
        if ($in['boot_mode'] === 'bios' && $in['arch'] === 'aarch64') {
            $add('warn', 'ARM64 machines normally boot with UEFI/U-Boot. Double-check that Legacy BIOS is really what the machine uses.');
        }
        if ($in['secure_boot'] && $in['boot_mode'] === 'bios') {
            $add('warn', 'Secure Boot only exists with UEFI. Check the boot mode setting you entered.');
        }
        if ($in['secure_boot'] && $in['boot_mode'] === 'uefi') {
            switch ($d['secure_boot']) {
                case 'no':
                    $add('warn', "{$d['name']} does not support Secure Boot out of the box. Turn Secure Boot off in the firmware settings before installing (or enroll your own keys).");
                    break;
                case 'partial':
                    $add('warn', "Secure Boot support in {$d['name']} is limited or needs manual setup. If the installer will not boot, disable Secure Boot temporarily.");
                    break;
                case 'unknown':
                    $add('warn', "Secure Boot support for {$d['name']} is unconfirmed. If the USB stick will not boot, disable Secure Boot.");
                    break;
            }
            if ($in['hibernate']) {
                $add('info', 'On some systems, Secure Boot (kernel lockdown) blocks hibernation. If hibernate fails, you may need Secure Boot off.');
            }
        }
    }

    private function checkGeneral(array $in, array $d, array $f, callable $add): void
    {
        if ($in['existing_os'] === 'windows') {
            $add('warn', 'Windows is installed: back it up, suspend BitLocker, turn off Fast Startup, then shrink the C: drive from Disk Management. Leave the freed space "Unallocated".');
        } elseif ($in['dual_boot']) {
            $add('warn', 'A system is already on this disk. Back up your data and make sure the free space you entered is really unallocated.');
        }
        if ($in['dual_boot'] && $in['boot_mode'] === 'bios') {
            $add('warn', 'Legacy BIOS with an MBR disk allows only 4 primary partitions. Check how many Windows already uses.');
        }
        if ($in['boot_mode'] === 'bios' && !$in['dual_boot'] && $in['disk_gb'] > 2000) {
            $add('info', 'The disk is larger than 2 TB, so the plan uses a GPT table with a small BIOS boot partition.');
        }
        if ($in['disk_type'] === 'hdd' && in_array($in['use'], ['desktop', 'dev'], true)) {
            $add('info', 'An HDD is slow for a desktop: boot and app start will take longer. Keep the layout simple and prefer ext4 or XFS over Btrfs snapshots.');
        }
        if ($in['disk_type'] === 'emmc') {
            $add('info', 'eMMC and SD storage wear out with heavy writes. Prefer swap on compressed RAM (zram) and avoid a large swap partition.');
        }
        if ($in['hibernate'] && $in['ram_gb'] > 32) {
            $add('info', 'Hibernate with a lot of RAM needs a very large swap area; check it fits your disk.');
        }
    }

    // ------------------------------------------------------------------
    // Layout
    // ------------------------------------------------------------------

    private function mergeLayout(array $base, array $over): array
    {
        $out = array_replace($base, $over);
        if (isset($over['root_gb'])) {
            $out['root_gb'] = array_replace($base['root_gb'], $over['root_gb']);
        }
        return $out;
    }

    private function swapMib(float $ramGb, bool $hibernate, float $spaceGb): int
    {
        if ($hibernate) {
            $gb = $ramGb + sqrt($ramGb);
        } elseif ($ramGb <= 2) {
            $gb = $ramGb * 2;
        } elseif ($ramGb <= 8) {
            $gb = $ramGb;
        } elseif ($ramGb <= 64) {
            $gb = max(4.0, $ramGb * 0.5);
        } else {
            $gb = 4.0;
        }
        if (!$hibernate) {
            $gb = min($gb, max(1.0, $spaceGb * 0.25));
        }
        return (int) (ceil($gb * 1024 / 256) * 256);
    }

    private function buildLayout(array $in, array $layout, float $spaceGb, callable $add): array
    {
        $totalMib = (int) floor($spaceGb * 1024);
        $parts    = [];
        $notes    = [];
        $used     = 0;
        $uefi     = $in['boot_mode'] === 'uefi';

        // Partition table
        if ($uefi) {
            $table = 'gpt';
        } elseif ($in['dual_boot']) {
            $table = 'existing';
        } else {
            $table = $in['disk_gb'] > 2000 ? 'gpt' : 'msdos';
        }

        // Boot-related partitions
        if (!$uefi && $table === 'gpt') {
            $parts[] = $this->part('bios_grub', 'BIOS boot', null, 'none', 1, ['bios_grub'], true,
                'Tiny partition used by GRUB on GPT disks with Legacy BIOS.');
            $used += 1;
        }
        if ($uefi) {
            if ($in['dual_boot']) {
                $notes[] = 'Dual boot with UEFI: reuse the existing EFI System Partition. Do NOT format it, only mount it at ' . $layout['esp_mount'] . '.';
                $parts[] = $this->part('esp', 'EFI System (existing)', $layout['esp_mount'], 'fat32', 0, ['esp'], false,
                    'Already created by the existing system. Reuse it, do not format.');
            } else {
                $parts[] = $this->part('esp', 'EFI System', $layout['esp_mount'], 'fat32', (int) $layout['esp_mib'], ['esp'], true,
                    'Holds the boot loader. Format as FAT32.');
                $used += (int) $layout['esp_mib'];
            }
        }
        if (!empty($layout['boot_mib'])) {
            $parts[] = $this->part('boot', '/boot', '/boot', $layout['boot_fs'] ?? 'ext4', (int) $layout['boot_mib'], [], true,
                'Kernels and boot files, kept outside LVM.');
            $used += (int) $layout['boot_mib'];
        }

        // Swap
        $swapStyle = $layout['swap_style'];
        $swapMib   = 0;
        if ($in['hibernate'] && $swapStyle !== 'partition') {
            $swapStyle = 'partition';
            $notes[]   = 'Hibernate needs swap at least as large as the used RAM, so a real swap partition is planned instead of a swap file or zram.';
        }
        if ($swapStyle === 'partition') {
            $swapMib = $this->swapMib($in['ram_gb'], $in['hibernate'], $spaceGb);
            $parts[] = $this->part('swap', 'Swap', 'swap', 'swap', $swapMib, [], true,
                $in['hibernate'] ? 'Sized for hibernation (RAM plus a margin).' : 'Sized from RAM, capped at a quarter of the space.');
            $used += $swapMib;
        } elseif ($swapStyle === 'file') {
            $swapMib = $this->swapMib($in['ram_gb'], false, $spaceGb);
            $notes[] = 'This distribution creates a swap file inside the root filesystem (about ' . $this->fmt($swapMib / 1024) . ' GB). No swap partition is needed.';
        } else {
            $notes[] = 'This distribution uses compressed RAM swap (zram) by default. No swap partition is needed.';
        }
        if ($swapStyle !== 'zram' && in_array($in['disk_type'], ['ssd', 'nvme'], true) && $in['ram_gb'] >= 16 && !$in['hibernate']) {
            $notes[] = 'With 16 GB or more RAM you can also skip disk swap and use zram instead.';
        }

        // Root / home
        $remaining = $totalMib - $used;
        $rootGb    = (float) $layout['root_gb'][$in['use']];
        $fsRoot    = $layout['default_fs'];
        $group     = $layout['lvm'] ? 'LVM volume group' : null;
        $wantHome  = $in['separate_home'];

        if ($wantHome && $remaining < (int) (($rootGb + 10) * 1024)) {
            $wantHome = false;
            $add('warn', 'Not enough space for a separate /home next to a root partition of ' . $this->fmt($rootGb) . ' GB, so /home stays inside root.');
        }
        if ($remaining < 4 * 1024) {
            $add('error', 'Almost no space is left for the root filesystem after the other partitions (' . $this->fmt(max(0, $remaining) / 1024) . ' GB).');
        }

        if ($wantHome) {
            $rootMib = (int) ($rootGb * 1024);
            $parts[] = $this->part('root', 'Root', '/', $fsRoot, $rootMib, [], true,
                'System files and programs.', $group);
            $parts[] = $this->part('home', 'Home', '/home', $layout['home_fs'], max(0, $remaining - $rootMib), [], true,
                'Your files and settings. Survives a reinstall if you do not format it.', $group, true);
        } else {
            $parts[] = $this->part('root', 'Root', '/', $fsRoot, max(0, $remaining), [], true,
                'System files, programs and your files (no separate /home).', $group, true);
            if ($in['use'] === 'desktop' && $spaceGb >= 128 && !$in['separate_home'] && $fsRoot !== 'btrfs') {
                $notes[] = 'Tip: with this much space a separate /home makes reinstalling easier. Tick "Separate /home" to see that layout.';
            }
        }

        // Filesystem advice
        if ($fsRoot === 'btrfs') {
            $notes[] = 'Btrfs root: the installer normally creates subvolumes and snapshots automatically.';
            $rootActual = $wantHome ? $rootGb : max(0, $remaining) / 1024;
            if ($rootActual < 40) {
                $notes[] = 'If you enable Btrfs snapshots, a root partition under about 40 GB can fill up quickly. Disable snapshots or choose ext4/XFS for a small root.';
            }
        }
        if ($layout['lvm']) {
            $notes[] = 'The installer places root' . ($wantHome ? ', home' : '') . ($swapMib && $swapStyle === 'partition' ? ' and swap' : '') . ' as logical volumes inside one LVM volume group. Keep /boot and the EFI partition outside LVM.';
        }

        // Resolve the "rest of space" entries to concrete sizes already done above.
        $sum = 0;
        foreach ($parts as $p) {
            $sum += $p['size_mib'];
        }

        return [
            'table'      => $table,
            'partitions' => $parts,
            'swap_mib'   => $swapStyle === 'partition' ? $swapMib : 0,
            'swap_style' => $swapStyle,
            'notes'      => $notes,
            'total_mib'  => $totalMib,
            'used_mib'   => $sum,
        ];
    }

    private function part(string $key, string $label, ?string $mount, string $fs, int $sizeMib, array $flags, bool $create, string $note, ?string $group = null, bool $rest = false): array
    {
        return [
            'key' => $key, 'label' => $label, 'mount' => $mount, 'fs' => $fs,
            'size_mib' => $sizeMib, 'size_gb' => round($sizeMib / 1024, 2),
            'flags' => $flags, 'create' => $create, 'note' => $note,
            'group' => $group, 'rest' => $rest,
        ];
    }

    // ------------------------------------------------------------------
    // Commands (whole-disk only)
    // ------------------------------------------------------------------

    private function partDev(string $dev, int $n): string
    {
        return preg_match('/(nvme\d+n\d+|mmcblk\d+)$/', $dev) ? "{$dev}p{$n}" : "{$dev}{$n}";
    }

    private function commands(array $in, array $plan, bool $withMount): array
    {
        $dev   = $in['disk_device'];
        $table = $plan['table'];
        $cmds  = [
            "# WARNING: this ERASES everything on $dev.",
            "# Confirm the right disk first with:  lsblk -o NAME,SIZE,MODEL,MOUNTPOINT",
            "parted -s $dev mklabel $table",
        ];
        $start = 1;
        $n     = 0;
        $dev_of = [];
        foreach ($plan['partitions'] as $p) {
            if (!$p['create']) {
                continue;
            }
            $n++;
            $dev_of[$p['key']] = $this->partDev($dev, $n);
            $end = $p['rest'] ? '100%' : ($start + $p['size_mib']) . 'MiB';
            $name = $p['key'];
            if ($p['key'] === 'bios_grub') {
                $cmds[] = "parted -s $dev mkpart $name {$start}MiB $end";
                $cmds[] = "parted -s $dev set $n bios_grub on";
            } else {
                $ptype = match ($p['fs']) {
                    'fat32' => 'fat32',
                    'swap'  => 'linux-swap',
                    'xfs'   => 'xfs',
                    'btrfs' => 'btrfs',
                    default => 'ext4',
                };
                $cmds[] = $table === 'msdos'
                    ? "parted -s $dev mkpart primary $ptype {$start}MiB $end"
                    : "parted -s $dev mkpart $name $ptype {$start}MiB $end";
                if (in_array('esp', $p['flags'], true)) {
                    $cmds[] = "parted -s $dev set $n esp on";
                }
                if ($table === 'msdos' && $p['key'] === 'root') {
                    $cmds[] = "parted -s $dev set $n boot on";
                }
            }
            if (!$p['rest']) {
                $start += $p['size_mib'];
            }
        }

        $cmds[] = '';
        $cmds[] = '# Create the filesystems';
        foreach ($plan['partitions'] as $p) {
            if (!$p['create'] || !isset($dev_of[$p['key']])) {
                continue;
            }
            $d = $dev_of[$p['key']];
            $line = match ($p['fs']) {
                'fat32' => "mkfs.fat -F 32 $d",
                'swap'  => "mkswap $d",
                'xfs'   => "mkfs.xfs $d",
                'btrfs' => "mkfs.btrfs $d",
                'ext4'  => "mkfs.ext4 $d",
                default => null,
            };
            if ($line) {
                $cmds[] = $line;
            }
        }

        if ($withMount) {
            $cmds[] = '';
            $cmds[] = '# Mount everything under /mnt (root first)';
            $mounts = [];
            foreach ($plan['partitions'] as $p) {
                if (!$p['create'] || !isset($dev_of[$p['key']]) || $p['mount'] === null || $p['fs'] === 'none') {
                    continue;
                }
                $mounts[] = [$p['mount'], $dev_of[$p['key']], $p['fs']];
            }
            usort($mounts, static fn($a, $b) => strlen($a[0]) <=> strlen($b[0]));
            foreach ($mounts as [$mp, $d, $fs]) {
                if ($fs === 'swap') {
                    $cmds[] = "swapon $d";
                } elseif ($mp === '/') {
                    $cmds[] = "mount $d /mnt";
                } else {
                    $cmds[] = "mkdir -p /mnt$mp";
                    $cmds[] = "mount $d /mnt$mp";
                }
            }
        }
        return $cmds;
    }

    // ------------------------------------------------------------------
    // Steps
    // ------------------------------------------------------------------

    private function prepareSteps(array $in, array $d): array
    {
        $steps = ['Back up everything you care about to another drive. Partitioning mistakes can destroy data.'];
        if ($d['website']) {
            $steps[] = "Download the {$in['arch']} image of {$d['name']} from {$d['website']} and verify its checksum or signature.";
        } else {
            $steps[] = "Find the official {$d['name']} download page and verify the image's checksum or signature.";
        }
        if (in_array($d['install_method'], ['image'], true)) {
            return $steps;
        }
        $steps[] = 'Write the image to a USB stick (at least 8 GB): Rufus or Ventoy on Windows, balenaEtcher anywhere, or "dd" on Linux. This erases the stick.';
        $fw = 'Open the firmware (BIOS/UEFI) setup and set the boot mode to ' . ($in['boot_mode'] === 'uefi' ? 'UEFI' : 'Legacy/CSM') . '.';
        if ($in['boot_mode'] === 'uefi' && in_array($d['secure_boot'], ['no', 'partial', 'unknown'], true)) {
            $fw .= ' If the stick does not boot, turn Secure Boot off.';
        }
        $steps[] = $fw . ' Then boot from the USB stick (usually F12, F10, Esc or F2 at power-on).';
        return $steps;
    }

    // ------------------------------------------------------------------
    // Utils
    // ------------------------------------------------------------------

    private function fmt(float $n): string
    {
        return rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.');
    }

    private function loadJson(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException("Cannot read $path");
        }
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return $data;
    }
}
