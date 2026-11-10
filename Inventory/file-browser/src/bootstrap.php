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

require __DIR__ . '/Auth.php';
require __DIR__ . '/Storage.php';
