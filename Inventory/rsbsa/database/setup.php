<?php
/**
 * One-time setup (command line):
 *
 *     php database/setup.php <username> <password> ["Full Name"]
 *
 * Creates the SQLite database file with all tables (safe to run again; existing data is kept)
 * and the administrator account. The password must be at least 8 characters.
 * To reset the password later, run database/create_admin.php with the same username.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this script from the command line.');
}

define('RSBSA_ALLOW_CREATE', true);
define('RSBSA_SKIP_SCHEMA_CHECK', true);
require __DIR__ . '/../api/_bootstrap.php';
require __DIR__ . '/_migrate.php';

$username = trim($argv[1] ?? '');
$password = $argv[2] ?? '';
$name = trim($argv[3] ?? 'Administrator');

if ($username === '' || $password === '') {
    fwrite(STDERR, "Usage: php database/setup.php <username> <password> [\"Full Name\"]\n");
    exit(1);
}
if (mb_strlen($password) < 8) {
    fwrite(STDERR, "The password must be at least 8 characters.\n");
    exit(1);
}

foreach (['pdo_sqlite', 'fileinfo', 'mbstring', 'zip'] as $ext) {
    if (!extension_loaded($ext)) {
        fwrite(STDERR, "Warning: the PHP extension \"$ext\" is not enabled" . ($ext === 'zip' ? " (needed for Excel export).\n" : ".\n"));
    }
}

try {
    $pdo = db();
} catch (Throwable $e) {
    fwrite(STDERR, 'Could not open the database: ' . $e->getMessage() . "\n");
    exit(1);
}

if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
    rsbsa_migrate($pdo);   // creates the tables, or adds what an older database is missing
    echo "Database ready.\n";
} else {
    rsbsa_migrate($pdo);
    echo "Using MySQL: import database/schema.mysql.sql yourself; only the account is created here.\n";
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$existing = q_one('SELECT id FROM admins WHERE LOWER(username) = LOWER(?)', [$username]);
if ($existing) {
    q('UPDATE admins SET password_hash = ?, name = ? WHERE id = ?', [$hash, $name, $existing['id']]);
    echo "Password updated for \"$username\".\n";
    log_activity('System', 'Password reset from the command line', "Account \"$username\".", ['id' => (int) $existing['id'], 'username' => $username, 'name' => 'Command line']);
} else {
    q('INSERT INTO admins (username, password_hash, name, created_at) VALUES (?, ?, ?, ?)', [$username, $hash, $name, now()]);
    echo "Administrator \"$username\" created.\n";
    log_activity('System', 'Administrator account created', "Account \"$username\".", ['id' => null, 'username' => $username, 'name' => 'Command line']);
}
q('DELETE FROM login_attempts');

foreach (['tax_declarations', 'supporting_docs', 'photos', 'valid_ids'] as $dir) {
    storage_dir($dir);
}
echo "Done. Open index.php in your browser and log in.\n";
