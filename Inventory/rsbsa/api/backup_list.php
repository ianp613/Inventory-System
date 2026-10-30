<?php
/** GET -> { settings, backups[], folder, can_backup, reason, upload_limit_mb } for the Backup section of settings.php */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_get();
require_auth();

$supported = backup_is_sqlite() && class_exists('ZipArchive');
$reason = !backup_is_sqlite() ? 'Backup from this page works with the default SQLite database only.'
    : (!class_exists('ZipArchive') ? 'The PHP "zip" extension is not enabled.' : '');

$toBytes = function (string $v): int {
    $n = (int) $v;
    switch (strtolower(substr(trim($v), -1))) {
        case 'g': $n *= 1024;
        case 'm': $n *= 1024;
        case 'k': $n *= 1024;
    }
    return $n;
};
$limit = min($toBytes((string) ini_get('upload_max_filesize')) ?: PHP_INT_MAX, $toBytes((string) ini_get('post_max_size')) ?: PHP_INT_MAX);

$s = backup_settings();
ok([
    'can_backup'      => $supported,
    'reason'          => $reason,
    'settings'        => ['auto' => $s['auto'], 'frequency' => $s['frequency'], 'keep' => $s['keep']],
    'backups'         => $supported ? array_map(fn($b) => $b + ['size_text' => backup_human_size($b['size'])], backup_list()) : [],
    'folder'          => $supported ? backup_dir() : '',
    'upload_limit_mb' => $limit === PHP_INT_MAX ? null : (int) floor($limit / 1048576),
]);
