<?php
/**
 * POST { new_password } -> { success }
 * Only the rsbsa_dev super user (row id 2) may call this. It resets the password of the main
 * super user (rsbsa_super_user, row id 1) only - never its username or display name, which are
 * fixed everywhere else in the system. Nothing here is written to any log: this account leaves no trace.
 */
require __DIR__ . '/_bootstrap.php';

require_post();
$me = require_super();
if (($me['sid'] ?? 0) !== 2) {
    fail(403, 'Not allowed.');
}

$in = json_decode((string) file_get_contents('php://input'), true) ?: [];
$new = (string) ($in['new_password'] ?? '');
if (mb_strlen($new) < 8) {
    fail(422, 'Please correct the highlighted fields.', ['new_password' => 'The new password must be at least 8 characters.']);
}
if (mb_strlen($new) > 72) {
    fail(422, 'Please correct the highlighted fields.', ['new_password' => 'The new password must be 72 characters or fewer.']);
}

ensure_super_user(1);
q('UPDATE super_user SET password_hash = ?, password_changed_at = ? WHERE id = 1', [password_hash($new, PASSWORD_DEFAULT), now()]);

ok([], 'Password changed.');
