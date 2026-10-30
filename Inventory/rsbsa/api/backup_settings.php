<?php
/** POST { auto: bool, frequency: daily|weekly, keep: 1-60 } -> saves the automatic backup settings */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_post();
require_auth();
$in = read_json();

$freq = (string) ($in['frequency'] ?? 'daily');
if (!isset(BACKUP_FREQUENCIES[$freq])) {
    fail(422, 'Choose daily or weekly.', ['frequency' => 'Choose daily or weekly.']);
}
$keep = (int) ($in['keep'] ?? 14);
if ($keep < 1 || $keep > 60) {
    fail(422, 'Keep between 1 and 60 automatic backups.', ['keep' => 'Keep between 1 and 60 automatic backups.']);
}
$s = backup_settings();
$s['auto'] = !empty($in['auto']);
$s['frequency'] = $freq;
$s['keep'] = $keep;
$s['last_auto_try'] = 0;
backup_save_settings($s);
$pruned = backup_prune();

log_activity('System', 'Changed automatic backup', $s['auto'] ? ucfirst($freq) . ", keeping the last $keep." : 'Off.');
ok(['settings' => ['auto' => $s['auto'], 'frequency' => $freq, 'keep' => $keep], 'pruned' => $pruned], 'Automatic backup settings saved.');
