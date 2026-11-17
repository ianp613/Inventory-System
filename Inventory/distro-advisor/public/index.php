<?php
declare(strict_types=1);

require __DIR__ . '/../src/Planner.php';

use DistroAdvisor\Planner;

// Change this to your name or organization.
const COPYRIGHT_OWNER = 'DDPO Tech';

$planner = new Planner(__DIR__ . '/../data');
$catalog = $planner->catalog();

// Group distros by family for the <select>.
$groups = [];
foreach ($catalog as $d) {
    $groups[$d['family_name']][] = $d;
}
foreach ($groups as &$g) {
    usort($g, static fn($a, $b) => strcasecmp($a['name'], $b['name']));
}
unset($g);

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Linux Install Advisor</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="top">
  <h1>Linux Install Advisor</h1>
  <p>Pick a distribution, enter your computer's specs, and get a partition layout and install steps.</p>
</header>

<main class="layout">
  <form id="form" class="card" autocomplete="off">
    <h2>1. Distribution</h2>
    <label>Distribution
      <select name="distro" id="distro" required>
        <option value="">Choose one…</option>
        <?php foreach ($groups as $family => $list): ?>
          <optgroup label="<?= h($family) ?>">
            <?php foreach ($list as $d): ?>
              <option value="<?= h($d['id']) ?>"><?= h($d['name']) ?><?= $d['status'] === 'legacy' ? ' (discontinued)' : '' ?></option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>
    </label>
    <label id="version-wrap" hidden>Version
      <select name="version" id="version"></select>
    </label>
    <div id="distro-info" class="info" hidden></div>

    <h2>2. Your computer</h2>
    <div class="grid">
      <label>Boot mode
        <select name="boot_mode">
          <option value="uefi">UEFI</option>
          <option value="bios">Legacy BIOS / CSM</option>
        </select>
      </label>
      <label>CPU architecture
        <select name="arch">
          <option value="x86_64">x86_64 (64-bit Intel/AMD)</option>
          <option value="aarch64">ARM 64-bit (aarch64)</option>
          <option value="i686">32-bit x86 (i686)</option>
          <option value="armv7">ARM 32-bit (armv7)</option>
        </select>
      </label>
      <label>RAM (GB)
        <input type="number" name="ram_gb" min="0.25" step="0.25" value="8" required>
      </label>
      <label>Disk size (GB)
        <input type="number" name="disk_gb" min="1" step="1" value="512" required>
      </label>
      <label>Disk type
        <select name="disk_type">
          <option value="nvme">NVMe SSD</option>
          <option value="ssd">SATA SSD</option>
          <option value="hdd">Hard disk (HDD)</option>
          <option value="emmc">eMMC / SD card</option>
        </select>
      </label>
      <label>Disk device name
        <input type="text" name="disk_device" value="/dev/sda" pattern="/dev/[A-Za-z0-9]{2,20}" title="For example /dev/sda or /dev/nvme0n1">
      </label>
      <label>Graphics card
        <select name="gpu">
          <option value="intel">Intel</option>
          <option value="amd">AMD</option>
          <option value="nvidia">NVIDIA</option>
          <option value="hybrid">Hybrid (Intel/AMD + NVIDIA)</option>
          <option value="other">Other / not sure</option>
        </select>
      </label>
      <label>What will you use it for?
        <select name="use">
          <option value="desktop">Desktop / daily use</option>
          <option value="dev">Development / many tools</option>
          <option value="server">Server</option>
          <option value="minimal">Minimal / small system</option>
        </select>
      </label>
      <label>Already installed on this disk
        <select name="existing_os" id="existing_os">
          <option value="none">Nothing (wipe the disk)</option>
          <option value="windows">Windows (dual boot)</option>
          <option value="linux">Another Linux (dual boot)</option>
          <option value="other">Something else (dual boot)</option>
        </select>
      </label>
      <label id="free-wrap" hidden>Free space for Linux (GB)
        <input type="number" name="free_gb" min="1" step="1" value="100">
      </label>
    </div>

    <div class="checks">
      <label class="inline"><input type="checkbox" name="secure_boot"> Secure Boot is enabled</label>
      <label class="inline"><input type="checkbox" name="hibernate"> I want hibernate</label>
      <label class="inline"><input type="checkbox" name="separate_home"> Separate /home partition</label>
    </div>

    <details class="help">
      <summary>How do I find these?</summary>
      <ul>
        <li><b>Boot mode:</b> on Windows run <code>msinfo32</code> and read "BIOS Mode". On Linux run <code>[ -d /sys/firmware/efi ] &amp;&amp; echo UEFI || echo BIOS</code>.</li>
        <li><b>RAM, CPU, GPU:</b> Windows Task Manager &rarr; Performance. Linux: <code>free -h</code>, <code>uname -m</code>, <code>lspci | grep -i vga</code>.</li>
        <li><b>Disk and device name:</b> Linux: <code>lsblk</code>. On Windows use Disk Management for the size; the device name is only used in the optional commands, so leave the default if unsure.</li>
        <li><b>Secure Boot:</b> Windows <code>msinfo32</code> &rarr; "Secure Boot State".</li>
      </ul>
    </details>

    <button type="submit" id="go">Get my install plan</button>
    <div id="form-errors" class="alert error" hidden></div>
  </form>

  <section id="result" class="result" aria-live="polite" hidden></section>
</main>

<footer>
  <p>Layouts and requirements are general guidance, not a guarantee. Always check the distribution's official documentation and double-check the target disk before partitioning.</p>
  <p class="copyright">&copy; <?= date('Y') ?> <?= h(COPYRIGHT_OWNER) ?>. All rights reserved. Vibe coded with Claude AI.</p>
</footer>

<script>window.DISTROS = <?= json_encode($catalog, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="assets/app.js"></script>
</body>
</html>
