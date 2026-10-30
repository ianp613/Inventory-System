<?php
/**
 * POST (JSON) { name }
 * Changes the display name of the logged-in administrator (the login username stays the same).
 * -> { success, message, data: { user: { id, username, name } } }
 * 422 { errors: { name } }
 */
require __DIR__ . '/_bootstrap.php';

require_post();
$me = current_admin();
if (!$me) {
    fail(401, 'Your session has expired. Please log in again.');
}
$in = read_json();

$name = clean_str($in['name'] ?? '');
if ($name === '') {
    fail(422, 'Please correct the highlighted fields.', ['name' => 'Enter your name.']);
}
if (mb_strlen($name) > 60) {
    fail(422, 'Please correct the highlighted fields.', ['name' => 'The name must be 60 characters or fewer.']);
}

q('UPDATE admins SET name = ? WHERE id = ?', [$name, $me['id']]);
log_activity('Account', 'Changed display name', 'From "' . $me['name'] . '" to "' . $name . '".');
$_SESSION['admin_name'] = $name;

ok(['user' => ['id' => $me['id'], 'username' => $me['username'], 'name' => $name]], 'Your name was changed.');
