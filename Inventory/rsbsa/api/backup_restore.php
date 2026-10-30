<?php
/**
 * POST multipart { password, name }  restores a saved backup, or
 * POST multipart { password, file }  restores an uploaded backup file.
 * Replaces ALL current data. A safety backup ("before-restore") is made first. The administrator is logged out afterwards.
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_post();
$admin = require_auth();
backup_require_supported();

$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
$row = q_one('SELECT password_hash FROM admins WHERE id = ?', [$admin['id']]);
if ($password === '' || !$row || !password_verify($password, $row['password_hash'])) {
    fail(422, 'Your password is not correct.', ['password' => 'Your password is not correct.']);
}

$tmpUpload = null;
if (!empty($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES['file'];
    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) {
        fail(422, 'The file is bigger than the server allows (upload_max_filesize / post_max_size in php.ini). Copy the backup into the backup folder instead, then restore it from the list.', ['file' => 'File too large for the server.']);
    }
    if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
        fail(422, 'The file could not be uploaded.', ['file' => 'Upload failed.']);
    }
    $source = $f['tmp_name'];
    $label = 'uploaded file ' . clean_str($f['name'] ?? '');
} else {
    $name = clean_str($_POST['name'] ?? '');
    $source = backup_path($name);
    $label = $name;
}

$result = backup_restore_from($source);

// the restored data may have another administrator list or password: log in again
log_activity('System', 'Restored backup', $label . ' (made ' . ($result['manifest']['created_at'] ?? 'unknown') . '). Previous data saved as ' . $result['safety']['name'] . '.', $admin);
destroy_session();
ok(['safety' => $result['safety']['name'], 'created_at' => $result['manifest']['created_at'] ?? ''], 'The backup was restored. Please log in again.');
