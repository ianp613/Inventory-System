<?php
/** POST { name } -> deletes one saved backup file */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_post();
require_auth();
$name = clean_str(read_json()['name'] ?? '');
$path = backup_path($name);
if (!@unlink($path)) {
    fail(500, 'The backup file could not be deleted.');
}
log_activity('System', 'Deleted backup', $name);
ok([], 'Backup deleted.');
