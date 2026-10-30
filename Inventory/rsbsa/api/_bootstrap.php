<?php
/**
 * Shared bootstrap for every API endpoint: config, JSON responses, errors,
 * session / login checks, database helpers.
 * Requires PHP 8.1 or newer with PDO MySQL.
 */

$GLOBALS['RSBSA_CFG'] = require (getenv('RSBSA_CONFIG') ?: __DIR__ . '/config.php');

function cfg(string $path, $default = null)
{
    $v = $GLOBALS['RSBSA_CFG'];
    foreach (explode('.', $path) as $key) {
        if (!is_array($v) || !array_key_exists($key, $v)) {
            return $default;
        }
        $v = $v[$key];
    }
    return $v;
}

date_default_timezone_set((string) cfg('timezone', 'Asia/Manila'));
mb_internal_encoding('UTF-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

/* ---------------------------------------------------------------------
 * JSON responses and errors
 * ------------------------------------------------------------------- */
class ApiError extends Exception
{
    public int $status;
    public array $errors;
    public array $headers;

    public function __construct(int $status, string $message, array $errors = [], array $headers = [])
    {
        parent::__construct($message);
        $this->status = $status;
        $this->errors = $errors;
        $this->headers = $headers;
    }
}

function fail(int $status, string $message, array $errors = [], array $headers = []): never
{
    throw new ApiError($status, $message, $errors, $headers);
}

function send_json(array $payload, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
    }
    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

function ok($data = [], string $message = ''): never
{
    send_json(['success' => true, 'message' => $message, 'data' => $data]);
}

set_exception_handler(function (Throwable $e) {
    if ($e instanceof ApiError) {
        foreach ($e->headers as $h) {
            header($h);
        }
        send_json(['success' => false, 'message' => $e->getMessage(), 'errors' => (object) $e->errors], $e->status);
    }
    error_log('[RSBSA] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    send_json([
        'success' => false,
        'message' => cfg('debug') ? $e->getMessage() : 'Server error. Please try again.',
        'errors'  => (object) [],
    ], 500);
});

/* ---------------------------------------------------------------------
 * Request helpers
 * ------------------------------------------------------------------- */
function require_get(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
        fail(405, 'Method not allowed.', [], ['Allow: GET']);
    }
}

/** POST only, and only from our own pages (custom header blocks cross-site form posts). */
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        fail(405, 'Method not allowed.', [], ['Allow: POST']);
    }
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
        fail(403, 'Invalid request.');
    }
}

function read_json(): array
{
    $data = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($data)) {
        fail(400, 'Invalid request body.');
    }
    return $data;
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;
}

/* ---------------------------------------------------------------------
 * Session (single administrator)
 * ------------------------------------------------------------------- */
function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('RSBSA_SESSION');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function destroy_session(): void
{
    start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?: 'Lax',
        ]);
    }
    session_destroy();
}

function current_admin(): ?array
{
    start_session();
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    $idle = (int) cfg('session_idle_minutes', 120) * 60;
    if (isset($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > $idle) {
        destroy_session();
        return null;
    }
    $_SESSION['last_activity'] = time();
    return [
        'id'       => (int) $_SESSION['admin_id'],
        'username' => (string) ($_SESSION['admin_username'] ?? ''),
        'name'     => (string) ($_SESSION['admin_name'] ?? ''),
    ];
}

/** Every data endpoint calls this first. Releases the session lock so requests can run in parallel. */
function require_auth(): array
{
    $admin = current_admin();
    if (!$admin) {
        fail(401, 'Your session has expired. Please log in again.');
    }
    session_write_close();
    return $admin;
}

/* ---------------------------------------------------------------------
 * Database
 * ------------------------------------------------------------------- */
/**
 * SQL LIKE for SQLite that ignores upper/lower case for ALL letters (SQLite itself only does A-Z,
 * so "pena" would not match "PENA" with a tilde). Used as the like() function of the connection.
 */
function sqlite_like_fn($pattern, $value, $escape = null)
{
    static $cache = [];
    if ($pattern === null || $value === null) {
        return null;
    }
    $pattern = (string) $pattern;
    $key = $pattern . "\0" . $escape;
    if (!isset($cache[$key])) {
        $re = '';
        $chars = preg_split('//u', $pattern, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        for ($i = 0, $n = count($chars); $i < $n; $i++) {
            $c = $chars[$i];
            if ($escape !== null && $escape !== '' && $c === $escape && $i + 1 < $n) {
                $re .= preg_quote($chars[++$i], '/');
            } elseif ($c === '%') {
                $re .= '.*';
            } elseif ($c === '_') {
                $re .= '.';
            } else {
                $re .= preg_quote($c, '/');
            }
        }
        if (count($cache) > 500) {
            $cache = [];
        }
        $cache[$key] = '/^' . $re . '$/isu';
    }
    return preg_match($cache[$key], (string) $value) === 1 ? 1 : 0;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $d = (array) cfg('db', []);
    $driver = $d['driver'] ?? 'sqlite';
    $dsn = $d['dsn'] ?? null;
    $user = $d['user'] ?? null;
    $pass = $d['pass'] ?? null;

    if (!$dsn && $driver === 'sqlite') {
        $path = (string) ($d['sqlite_path'] ?? 'database/rsbsa.sqlite');
        $isAbsolute = preg_match('#^([A-Za-z]:[\\\\/]|[\\\\/])#', $path) === 1;
        if (!$isAbsolute) {
            $path = dirname(__DIR__) . '/' . $path;
        }
        if (!is_file($path) && !defined('RSBSA_ALLOW_CREATE')) {
            throw new RuntimeException('Database file not found. Run: php database/setup.php <username> <password>');
        }
        if (!is_dir(dirname($path)) || !is_writable(dirname($path)) || (is_file($path) && !is_writable($path))) {
            throw new RuntimeException('The database file and its folder must be writable by the web server: ' . $path);
        }
        $dsn = 'sqlite:' . $path;
        $user = $pass = null;
    } elseif (!$dsn) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $d['host'] ?? '127.0.0.1',
            $d['port'] ?? 3306,
            $d['name'] ?? 'rsbsa',
            $d['charset'] ?? 'utf8mb4'
        );
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);

    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 5000');
        // make lower() and LIKE understand Ñ/ñ and other non-English letters
        $lower = fn($v) => $v === null ? null : mb_strtolower((string) $v, 'UTF-8');
        if (method_exists($pdo, 'sqliteCreateFunction')) {
            $pdo->sqliteCreateFunction('lower', $lower, 1);
            $pdo->sqliteCreateFunction('like', 'sqlite_like_fn', 2);
            $pdo->sqliteCreateFunction('like', 'sqlite_like_fn', 3);
        }
    }

    // refuse to run on a database from an older version (the update script adds the new columns)
    if (!defined('RSBSA_SKIP_SCHEMA_CHECK')) {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $current = $driver === 'sqlite'
            ? (int) $pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = 'activity_log'")->fetchColumn()
            : (int) $pdo->query("SHOW TABLES LIKE 'activity_log'")->rowCount();
        if (!$current) {
            $pdo = null;
            fail(500, 'The database must be updated first. On the server run: php database/migrate.php');
        }
    }
    return $pdo;
}

/** Run a prepared statement with positional parameters (ints stay ints, so LIMIT works). */
function q(string $sql, array $args = []): PDOStatement
{
    $st = db()->prepare($sql);
    $i = 1;
    foreach ($args as $v) {
        if (is_bool($v)) {
            $v = (int) $v;
        }
        $type = is_int($v) ? PDO::PARAM_INT : (is_null($v) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $st->bindValue($i++, $v, $type);
    }
    $st->execute();
    return $st;
}

function q_all(string $sql, array $args = []): array
{
    return q($sql, $args)->fetchAll();
}

function q_one(string $sql, array $args = []): ?array
{
    $row = q($sql, $args)->fetch();
    return $row === false ? null : $row;
}

function q_val(string $sql, array $args = [])
{
    $v = q($sql, $args)->fetchColumn();
    return $v === false ? null : $v;
}

function db_insert(string $table, array $data): int
{
    $cols = implode(', ', array_keys($data));
    $marks = implode(', ', array_fill(0, count($data), '?'));
    q("INSERT INTO $table ($cols) VALUES ($marks)", array_values($data));
    return (int) db()->lastInsertId();
}

function db_update(string $table, array $data, int $id): void
{
    $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($data)));
    q("UPDATE $table SET $set WHERE id = ?", [...array_values($data), $id]);
}

/* ---------------------------------------------------------------------
 * Small helpers
 * ------------------------------------------------------------------- */
function now(): string
{
    return date('Y-m-d H:i:s');
}

/** LIKE pattern that matches anywhere in the text; use with  ESCAPE '!'  */
function like_pattern(string $s): string
{
    return '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $s) . '%';
}

function person_name($surname, $first, $middle = '', $suffix = ''): string
{
    $rest = trim(implode(' ', array_filter([$first, $middle, $suffix], fn($v) => trim((string) $v) !== '')));
    return trim(implode(', ', array_filter([trim((string) $surname), $rest], fn($v) => $v !== '')));
}

function clean_str($v): string
{
    if (!is_scalar($v)) {
        return '';
    }
    return trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string) $v));
}

function ha($v): float
{
    return round((float) $v, 4);
}

function storage_dir(string $sub = ''): string
{
    $dir = rtrim((string) cfg('storage_dir'), '/\\') . ($sub !== '' ? '/' . $sub : '');
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function page_params(int $defaultPer = 25): array
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $per = (int) ($_GET['per_page'] ?? $defaultPer);
    $per = max(1, min(100, $per ?: $defaultPer));
    return [$page, $per];
}


/* ---------------------------------------------------------------------
 * Activity log
 * ------------------------------------------------------------------- */
/**
 * Record who did what. Never throws: a logging problem must not stop the real action.
 * $user = ['id'?, 'username', 'name'] to log for someone other than the logged-in administrator
 * (failed logins, command-line scripts). Categories: Login, Farmer, Duplicate, Report, Print, Document, Account, System.
 */
function log_activity(string $category, string $action, string $details = '', ?array $user = null): void
{
    try {
        $u = $user ?? current_admin() ?? ['id' => null, 'username' => '', 'name' => ''];
        db_insert('activity_log', [
            'admin_id'     => isset($u['id']) ? (int) $u['id'] : null,
            'username'     => mb_substr((string) ($u['username'] ?? ''), 0, 60),
            'display_name' => mb_substr((string) ($u['name'] ?? ''), 0, 120),
            'category'     => $category,
            'action'       => mb_substr($action, 0, 80),
            'details'      => mb_substr($details, 0, 2000),
            'ip_address'   => PHP_SAPI === 'cli' ? 'local' : client_ip(),
            'created_at'   => now(),
        ]);
    } catch (Throwable $e) {
        // ignore
    }
}
