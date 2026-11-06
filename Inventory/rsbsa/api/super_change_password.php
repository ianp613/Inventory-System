<?php
/**
 * POST (JSON) { current_password, new_password }  -> changes the super user's own password.
 * 422 { errors: { current_password | new_password } }, 429 after too many wrong current passwords.
 * Written to the super user log only.
 */
require __DIR__ . '/_bootstrap.php';

require_post();
if (!current_super()) {
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

$key = substr('password:' . SUPER_USERNAME, 0, 60);
$ip = client_ip();
$windowMin = (int) cfg('login_window_minutes', 15);
$since = date('Y-m-d H:i:s', time() - $windowMin * 60);
q('DELETE FROM login_attempts WHERE attempted_at < ?', [$since]);
$recent = (int) q_val('SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND username = ? AND attempted_at >= ?', [$ip, $key, $since]);
if ($recent >= (int) cfg('login_max_attempts', 5)) {
    fail(429, "Too many wrong passwords. Please wait $windowMin minutes and try again.", [], ['Retry-After: ' . ($windowMin * 60)]);
}

$su = ensure_super_user();
if (!password_verify($current, $su['password_hash'])) {
    q('INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (?, ?, ?)', [$ip, $key, now()]);
    fail(422, 'The current password is not correct.', ['current_password' => 'The current password is not correct.']);
}

q('UPDATE super_user SET password_hash = ?, password_changed_at = ? WHERE id = 1', [password_hash($new, PASSWORD_DEFAULT), now()]);
q('DELETE FROM login_attempts WHERE ip_address = ? AND username = ?', [$ip, $key]);

super_log('Password changed', SUPER_USERNAME, 'The super user changed their own password.');

session_regenerate_id(true);
$_SESSION['last_activity'] = time();

ok([], 'Password changed.');
