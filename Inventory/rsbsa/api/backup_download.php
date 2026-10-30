<?php
/** GET ?name=rsbsa-backup-....zip -> downloads a saved backup */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_get();
require_auth();
$name = (string) ($_GET['name'] ?? '');
$path = backup_path($name);

log_activity('System', 'Downloaded backup', $name);
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: attachment; filename="' . $name . '"');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
