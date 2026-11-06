<?php
/**
 * POST (JSON) { id, new_password }  -> sets a new password for an administrator account.
 * Also lifts a "too many failed attempts" lock on that account. Super user only. Written to the super user log only.
 */
require __DIR__ . '/_bootstrap.php';

require_post();
require_super();
$in = read_json();

$id = (int) ($in['id'] ?? 0);
$new = is_string($in['new_password'] ?? null) ? $in['new_password'] : '';
if (strlen($new) < 8) {
    fail(422, 'Please correct the highlighted fields.', ['new_password' => 'The new password must be at least 8 characters.']);
}
if (strlen($new) > 72) {
    fail(422, 'Please correct the highlighted fields.', ['new_password' => 'The new password must be 72 characters or fewer.']);
}
$row = q_one('SELECT id, username FROM admins WHERE id = ?', [$id]);
if (!$row) {
    fail(404, 'That account was not found.');
}
q('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $id]);
q('DELETE FROM login_attempts WHERE LOWER(username) = LOWER(?) OR username = ?', [$row['username'], substr('password:' . $row['username'], 0, 60)]);

super_log('Reset password', $row['username'], 'Password of "' . $row['name'] . '" was reset.');

ok([], 'The password was reset.');
