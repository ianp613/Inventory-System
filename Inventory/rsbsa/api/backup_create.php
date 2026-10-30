<?php
/** POST -> makes a backup now. { success, data: { backup: { name, size_text, ... } } } */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_post();
require_auth();
backup_require_supported();

$b = backup_create('manual');
backup_prune();
log_activity('System', 'Manual backup', $b['name'] . ' (' . backup_human_size($b['size']) . ')');
ok(['backup' => $b + ['size_text' => backup_human_size($b['size'])]], 'Backup created.');
