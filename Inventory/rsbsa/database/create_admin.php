<?php
/**
 * Create (or reset the password of) the administrator account. Run from the command line:
 *
 *     php database/create_admin.php <username> <password> ["Full Name"]
 *
 * Re-running it with the same username replaces the password.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this script from the command line.');
}

define('RSBSA_SKIP_SCHEMA_CHECK', true);
require __DIR__ . '/../api/_bootstrap.php';

$username = trim($argv[1] ?? '');
$password = $argv[2] ?? '';
$name = trim($argv[3] ?? 'Administrator');

if ($username === '' || $password === '') {
    fwrite(STDERR, "Usage: php database/create_admin.php <username> <password> [\"Full Name\"]\n");
    exit(1);
}
if (mb_strlen($password) < 8) {
    fwrite(STDERR, "The password must be at least 8 characters.\n");
    exit(1);
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
