<?php
/**
 * POST (JSON) { username, password }
 * -> { success, data: { user: { id, username, name } } }
 * 401 wrong credentials, 422 missing fields, 429 too many failed attempts.
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_backup.php';

require_post();
$in = read_json();

$username = clean_str($in['username'] ?? '');
$password = is_string($in['password'] ?? null) ? $in['password'] : '';

$errors = [];
if ($username === '') {
    $errors['username'] = 'Username is required.';
}
if ($password === '') {
    $errors['password'] = 'Password is required.';
}
if ($errors) {
    fail(422, 'Please enter your username and password.', $errors);
}
$username = mb_substr($username, 0, 60);

$ip = client_ip();
$maxAttempts = (int) cfg('login_max_attempts', 5);
$windowMin = (int) cfg('login_window_minutes', 15);
$since = date('Y-m-d H:i:s', time() - $windowMin * 60);

// forget old attempts, then count the recent ones for this IP + username
q('DELETE FROM login_attempts WHERE attempted_at < ?', [$since]);
$recent = (int) q_val(
    'SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND LOWER(username) = LOWER(?) AND attempted_at >= ?',
    [$ip, $username, $since]
);
$isSuper = strcasecmp($username, SUPER_USERNAME) === 0;   // the super user is never written to the activity log
if ($recent >= $maxAttempts) {
    $isSuper ? super_log('Login blocked', SUPER_USERNAME, 'Too many wrong passwords.') : log_activity('Login', 'Login blocked', "Too many failed attempts for username \"$username\".", ['id' => null, 'username' => $username, 'name' => '']);
    fail(
        429,
        "Too many failed attempts. Please wait $windowMin minutes and try again.",
        [],
        ['Retry-After: ' . ($windowMin * 60)]
    );
}

if ($isSuper) {
    $su = ensure_super_user();
    $dummy = '$2y$10$nldsI6O.fbJp6EZWHVsmrutxaN6zMQyazSA1u132LTo/UKfNxfrYK';
    if (!password_verify($password, $su['password_hash'] ?? $dummy)) {
        q('INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (?, ?, ?)', [$ip, $username, now()]);
        super_log('Failed login', SUPER_USERNAME, 'Wrong password.');
        fail(401, 'Invalid username or password.');
    }
    q('DELETE FROM login_attempts WHERE ip_address = ? AND LOWER(username) = LOWER(?)', [$ip, $username]);
    if (password_needs_rehash($su['password_hash'], PASSWORD_DEFAULT)) {
        q('UPDATE super_user SET password_hash = ? WHERE id = 1', [password_hash($password, PASSWORD_DEFAULT)]);
    }
    q('UPDATE super_user SET last_login = ? WHERE id = 1', [now()]);
    start_session();
    session_regenerate_id(true);
    unset($_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_name']);
    $_SESSION['super_id'] = 1;
    $_SESSION['last_activity'] = time();
    super_log('Logged in', SUPER_USERNAME);
    ok(['user' => ['id' => 0, 'username' => SUPER_USERNAME, 'name' => 'Super User', 'role' => 'super']], 'Login successful.');
}

$admin = q_one('SELECT id, username, name, password_hash, is_active FROM admins WHERE LOWER(username) = LOWER(?)', [$username]);

// always run password_verify so a wrong username takes as long as a wrong password
$dummyHash = '$2y$10$nldsI6O.fbJp6EZWHVsmrutxaN6zMQyazSA1u132LTo/UKfNxfrYK';
$valid = password_verify($password, $admin['password_hash'] ?? $dummyHash) && $admin !== null;

if (!$valid) {
    q('INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (?, ?, ?)', [$ip, $username, now()]);
    log_activity('Login', 'Failed login', 'Wrong ' . ($admin ? 'password' : 'username') . " for \"$username\".", ['id' => $admin ? (int) $admin['id'] : null, 'username' => $username, 'name' => $admin['name'] ?? '']);
    fail(401, 'Invalid username or password.');
}

q('DELETE FROM login_attempts WHERE ip_address = ? AND LOWER(username) = LOWER(?)', [$ip, $username]);

if (!(int) $admin['is_active']) {   // correct password, but the super user has disabled this account
    log_activity('Login', 'Login refused', 'The account is disabled.', ['id' => (int) $admin['id'], 'username' => $admin['username'], 'name' => $admin['name']]);
    fail(403, 'This account has been disabled. Please contact the system developer.');
}

if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
    q('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
}
q('UPDATE admins SET last_login = ? WHERE id = ?', [now(), $admin['id']]);

start_session();
session_regenerate_id(true);
unset($_SESSION['super_id']);
$_SESSION['admin_id'] = (int) $admin['id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['admin_name'] = $admin['name'];
$_SESSION['last_activity'] = time();

log_activity('Login', 'Logged in', '', ['id' => (int) $admin['id'], 'username' => $admin['username'], 'name' => $admin['name']]);

backup_auto_if_due();   // makes the scheduled backup now if one is due

ok(['user' => ['id' => (int) $admin['id'], 'username' => $admin['username'], 'name' => $admin['name'], 'role' => 'admin']], 'Login successful.');
