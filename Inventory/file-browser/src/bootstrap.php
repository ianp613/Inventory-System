<?php
declare(strict_types=1);

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

function config(): array
{
    static $cfg = null;
    if ($cfg === null) {
        $raw = @file_get_contents(__DIR__ . '/../config.json');
        $cfg = $raw ? json_decode($raw, true) : null;
        if (!is_array($cfg)) {
            http_response_code(500);
            exit('config.json is missing or invalid.');
        }
    }
    return $cfg;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = config();
        $pdo = new PDO(
            'mysql:host=' . $c['host'] . ';dbname=' . $c['database'] . ';charset=utf8mb4',
            $c['username'],
            $c['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    return $token !== null && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

/**
 * Send a file with HTTP Range support (video seeking, resumable downloads).
 * Handles one range ("bytes=a-b", "bytes=a-", "bytes=-n"); anything else gets the whole file.
 */
function send_file(string $file, string $mime, string $disposition): void
{
    $size = (int)filesize($file);
    $start = 0;
    $end = max(0, $size - 1);
    $status = 200;

    if ($size > 0 && isset($_SERVER['HTTP_RANGE'])
        && preg_match('/^bytes=(\d*)-(\d*)$/', trim((string)$_SERVER['HTTP_RANGE']), $m)
        && ($m[1] !== '' || $m[2] !== '')) {
        if ($m[1] === '') {                       // last N bytes
            $start = max(0, $size - (int)$m[2]);
        } else {
            $start = (int)$m[1];
            if ($m[2] !== '') {
                $end = min((int)$m[2], $size - 1);
            }
        }
        if ($start > $end || $start >= $size || ($m[1] === '' && (int)$m[2] === 0)) {
            http_response_code(416);
            header('Content-Range: bytes */' . $size);
            exit;
        }
        $status = 206;
    }

    session_write_close();      // never hold the session lock while streaming
    @set_time_limit(0);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($status);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: ' . $disposition);
    header('Accept-Ranges: bytes');
    header('X-Content-Type-Options: nosniff');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', (int)filemtime($file)) . ' GMT');
    header('Content-Length: ' . ($size > 0 ? $end - $start + 1 : 0));
    if ($status === 206) {
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
    }

    $fh = @fopen($file, 'rb');
    if (!$fh) {
        exit;
    }
    if ($start > 0) {
        fseek($fh, $start);
    }
    $left = $size > 0 ? $end - $start + 1 : 0;
    while ($left > 0 && !feof($fh)) {
        $buf = fread($fh, (int)min(1048576, $left));
        if ($buf === false || $buf === '') {
            break;
        }
        echo $buf;
        $left -= strlen($buf);
        flush();
        if (connection_aborted()) {
            break;
        }
    }
    fclose($fh);
    exit;
}

require __DIR__ . '/Auth.php';
require __DIR__ . '/Storage.php';
require __DIR__ . '/ZipStream.php';
