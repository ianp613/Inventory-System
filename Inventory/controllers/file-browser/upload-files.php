<?php
/**
 * upload.php — Handles multi-file and folder uploads
 *
 * POST params (multipart/form-data):
 *   file   — the uploaded file (binary)
 *   path   — relative path preserving folder structure (e.g. "my-folder/image.png")
 *   folder — current browser folder (e.g. "/Documents/")
 *
 * Returns JSON: { success: true, path: "..." }
 *            or { success: false, error: "..." }
 */

header('Content-Type: application/json');

// ── Config ───────────────────────────────────────────────────────────────────
define('MAX_SIZE',    50 * 1024 * 1024);    // 50 MB per file
define('ALLOWED_EXT', []);                   // empty = allow all

// ── CORS ─────────────────────────────────────────────────────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, null, 'Method not allowed');
}

// ── Session & privileges (mirrors your other controllers) ─────────────────────
session_start();

$conf = null;

if (!empty($_SESSION['ff_privileges'])) {
    if ($_SESSION['ff_privileges'] === 'Administrator') {
        $conf = json_decode(file_get_contents('../../file-browser.conf'));
    } else {
        $conf_temp = json_decode(file_get_contents('../../file-browser.conf'));
        if (!empty($_SESSION['ff_g_member'])) {
            $conf = json_decode('{
                "browser_name" : "' . $_SESSION['ff_g_name'] . '",
                "root_name"    : "' . $_SESSION['ff_g_name'] . '",
                "location"     : "' . $conf_temp->location . '/InvSys_' . $_SESSION['ff_g_name'] . '"
            }');
        } else {
            respond(false, null, 'Access denied');
        }
    }
} else {
    respond(false, null, 'Unauthenticated');
}

// ── Resolve base directory (same pattern as your other controllers) ───────────
$folder = isset($_POST['folder']) ? $_POST['folder'] : '/';

if (!is_dir($conf->location . $folder)) {
    mkdir($conf->location . $folder, 0755, true);
}

$baseDir = realpath($conf->location . $folder);

if ($baseDir === false) {
    respond(false, null, 'Could not resolve base directory');
}

// ── Validate file input ───────────────────────────────────────────────────────
if (empty($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
    respond(false, null, 'No file received');
}

$file    = $_FILES['file'];
$rawPath = isset($_POST['path']) ? trim($_POST['path']) : $file['name'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    respond(false, null, upload_error_message($file['error']));
}

if ($file['size'] > MAX_SIZE) {
    respond(false, null, 'File exceeds maximum size (' . format_bytes(MAX_SIZE) . ')');
}

// ── Sanitise the relative path (preserves sub-folder structure from upload) ───
$safePath = sanitise_path($rawPath);
if ($safePath === false) {
    respond(false, null, 'Invalid file path');
}

$fileExt = strtolower(pathinfo($safePath, PATHINFO_EXTENSION));

if (!empty(ALLOWED_EXT) && !in_array($fileExt, ALLOWED_EXT, true)) {
    respond(false, null, 'File type ".' . htmlspecialchars($fileExt) . '" is not allowed');
}

// ── Build full destination path inside the resolved base dir ──────────────────
$destAbs = $baseDir . DIRECTORY_SEPARATOR . $safePath;
$destDir = dirname($destAbs);

// Guard: destination must still be inside baseDir (prevent traversal)
if (strpos(realpath_safe($destDir, $baseDir), $baseDir) !== 0) {
    respond(false, null, 'Path traversal detected');
}

// Create sub-directories if uploading a folder structure
if (!is_dir($destDir)) {
    if (!mkdir($destDir, 0755, true)) {
        respond(false, null, 'Could not create directory');
    }
}

// Avoid silent overwrites
$destAbs = unique_path($destAbs);

if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
    respond(false, null, 'Failed to save file');
}

// Return path relative to baseDir for the client
$relPath = ltrim(str_replace($baseDir, '', $destAbs), DIRECTORY_SEPARATOR);
respond(true, $relPath);


// ── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Sanitise a relative upload path.
 * Blocks traversal, null bytes, hidden segments.
 */
function sanitise_path(string $raw): string|false
{
    $path = str_replace("\0", '', $raw);
    $path = str_replace('\\', '/', $path);

    $segments = explode('/', $path);
    $safe = [];
    foreach ($segments as $seg) {
        $seg = trim($seg);
        if ($seg === '' || $seg === '.' || $seg === '..' || $seg[0] === '.') {
            continue;
        }
        $seg    = preg_replace('/[^a-zA-Z0-9_\-. ]/', '_', $seg);
        $safe[] = $seg;
    }

    return empty($safe) ? false : implode(DIRECTORY_SEPARATOR, $safe);
}

/**
 * realpath() won't work on a path that doesn't exist yet.
 * This resolves as far as possible, then appends the rest.
 */
function realpath_safe(string $path, string $fallback): string
{
    $real = realpath($path);
    return $real !== false ? $real : $fallback . DIRECTORY_SEPARATOR . basename($path);
}

/**
 * Append a counter to avoid overwriting: image.png -> image_1.png
 */
function unique_path(string $path): string
{
    if (!file_exists($path)) return $path;

    $dir  = dirname($path);
    $name = pathinfo($path, PATHINFO_FILENAME);
    $ext  = pathinfo($path, PATHINFO_EXTENSION);
    $i    = 1;

    do {
        $new = $dir . DIRECTORY_SEPARATOR . $name . '_' . $i . ($ext ? '.' . $ext : '');
        $i++;
    } while (file_exists($new));

    return $new;
}

function upload_error_message(int $code): string
{
    $msgs = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension',
    ];
    return $msgs[$code] ?? 'Unknown upload error (code ' . $code . ')';
}

function format_bytes(int $bytes): string
{
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576)    return round($bytes / 1048576,    1) . ' MB';
    if ($bytes >= 1024)       return round($bytes / 1024,       0) . ' KB';
    return $bytes . ' B';
}

function respond(bool $success, ?string $path, string $error = ''): never
{
    echo json_encode($success
        ? ['success' => true,  'path'  => $path]
        : ['success' => false, 'error' => $error]
    );
    exit;
}
