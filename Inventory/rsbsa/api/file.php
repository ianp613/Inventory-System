<?php
/**
 * GET ?type=tax|support|photo|valid_id&id=   Streams an uploaded document to a logged-in administrator.
 * Files live in the storage folder, which is not reachable directly from the web.
 */
require __DIR__ . '/_bootstrap.php';

require_get();
require_auth();

$type = $_GET['type'] ?? '';
$id = (int) ($_GET['id'] ?? 0);

if ($type === 'photo') {
    $row = q_one('SELECT photo_path AS path, photo_name AS name FROM farmers WHERE id = ?', [$id]);
} elseif ($type === 'valid_id') {
    $row = q_one('SELECT valid_id_path AS path, valid_id_name AS name FROM farmers WHERE id = ?', [$id]);
} elseif ($type === 'tax') {
    $row = q_one('SELECT tax_file_path AS path, tax_file_name AS name FROM parcels WHERE id = ?', [$id]);
} elseif ($type === 'support') {
    $row = q_one('SELECT file_path AS path, file_name AS name FROM duplicate_documents WHERE id = ?', [$id]);
} else {
    $row = null;
}

if (!$row || !$row['path']) {
    fail(404, 'File not found.');
}

// only ever serve files that really sit inside the storage folder
$root = realpath(storage_dir());
$file = realpath(storage_dir() . '/' . $row['path']);
if ($root === false || $file === false || strncmp($file, $root . DIRECTORY_SEPARATOR, strlen($root) + 1) !== 0 || !is_file($file)) {
    fail(404, 'File not found.');
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file) ?: 'application/octet-stream';
if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
    $mime = 'application/octet-stream';
}

$name = preg_replace('/[^\p{L}\p{N}._ -]+/u', '_', (string) $row['name']) ?: 'document';

if ($type !== 'photo') {
    $label = ['tax' => 'tax declaration', 'support' => 'supporting document', 'valid_id' => 'valid ID'][$type];
    $owner = [
        'tax'      => 'SELECT f.surname, f.first_name, f.middle_name, f.suffix, p.cadastral_lot_no FROM parcels p JOIN farmers f ON f.id = p.farmer_id WHERE p.id = ?',
        'support'  => 'SELECT f.surname, f.first_name, f.middle_name, f.suffix, d.lot_no AS cadastral_lot_no FROM duplicate_documents x JOIN duplicate_flags d ON d.id = x.flag_id JOIN farmers f ON f.id = d.farmer_id WHERE x.id = ?',
        'valid_id' => "SELECT surname, first_name, middle_name, suffix, '' AS cadastral_lot_no FROM farmers WHERE id = ?",
    ][$type];
    $o = q_one($owner, [$id]);
    log_activity('Document', 'Opened ' . $label, $o ? trim(person_name($o['surname'], $o['first_name'], $o['middle_name'], $o['suffix']) . ($o['cadastral_lot_no'] !== '' ? ', lot ' . $o['cadastral_lot_no'] : '')) : '');
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($file));
header("Content-Disposition: inline; filename*=UTF-8''" . rawurlencode($name));
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'none'; sandbox");
header('Cache-Control: private, max-age=0, no-cache');
readfile($file);
exit;
