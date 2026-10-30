<?php
/**
 * POST (JSON) { current_password, new_password }
 * -> { success, message }
 * 422 { errors: { current_password | new_password } }, 429 after too many wrong current passwords.
 * (Not a 401, because the front end treats 401 as "logged out".)
 */
require __DIR__ . '/_bootstrap.php';

require_post();
$me = current_admin();
if (!$me) {
    fail(401, 'Your session has expired. Please log in again.');
}
$in = read_json();

$current = is_string($in['current_password'] ?? null) ? $in['current_password'] : '';
$new = is_string($in['new_password'] ?? null) ? $in['new_password'] : '';

$errors = [];
if ($current === '') {
    $errors['current_password'] = 'Enter your current password.';
}
if (strlen($new) < 8) {
    $errors['new_password'] = 'The new password must be at least 8 characters.';
} elseif (strlen($new) > 72) {
    $errors['new_password'] = 'The new password must be 72 characters or fewer.';
} elseif ($new === $current && $current !== '') {
    $errors['new_password'] = 'The new password must be different from the current one.';
}
if ($errors) {
    fail(422, 'Please correct the highlighted fields.', $errors);
}

// wrong current passwords are rate limited like failed logins
$key = substr('password:' . $me['username'], 0, 60);   // fits the login_attempts.username column
$ip = client_ip();
$windowMin = (int) cfg('login_window_minutes', 15);
$since = date('Y-m-d H:i:s', time() - $windowMin * 60);
q('DELETE FROM login_attempts WHERE attempted_at < ?', [$since]);
$recent = (int) q_val('SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND username = ? AND attempted_at >= ?', [$ip, $key, $since]);
if ($recent >= (int) cfg('login_max_attempts', 5)) {
    log_activity('Account', 'Password change blocked', 'Too many wrong current passwords.');
    fail(429, "Too many wrong passwords. Please wait $windowMin minutes and try again.", [], ['Retry-After: ' . ($windowMin * 60)]);
}

$row = q_one('SELECT id, password_hash FROM admins WHERE id = ?', [$me['id']]);
if (!$row || !password_verify($current, $row['password_hash'])) {
    q('INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (?, ?, ?)', [$ip, $key, now()]);
    log_activity('Account', 'Password change failed', 'The current password entered was wrong.');
    fail(422, 'The current password is not correct.', ['current_password' => 'The current password is not correct.']);
}

q('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $row['id']]);
q('DELETE FROM login_attempts WHERE ip_address = ? AND username = ?', [$ip, $key]);

// start a fresh session id after a credential change
session_regenerate_id(true);
$_SESSION['last_activity'] = time();

log_activity('Account', 'Changed password');

ok([], 'Password changed.');
