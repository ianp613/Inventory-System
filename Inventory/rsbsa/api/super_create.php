<?php
/**
 * POST (JSON) { username, name, password }  -> creates an administrator account.
 * Super user only. Written to the super user log only.
 */
require __DIR__ . '/_bootstrap.php';

require_post();
require_super();
$in = read_json();

$username = clean_str($in['username'] ?? '');
$name = clean_str($in['name'] ?? '');
$password = is_string($in['password'] ?? null) ? $in['password'] : '';

$errors = [];
if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $username)) {
    $errors['username'] = 'Use 3 to 60 letters, numbers, dots, dashes or underscores (no spaces).';
} elseif (strcasecmp($username, SUPER_USERNAME) === 0) {
    $errors['username'] = 'That username is reserved.';
} elseif (q_one('SELECT id FROM admins WHERE LOWER(username) = LOWER(?)', [$username])) {
    $errors['username'] = 'That username is already used by another account.';
}
if ($name === '') {
    $errors['name'] = 'Enter a name.';
} elseif (mb_strlen($name) > 60) {
    $errors['name'] = 'The name must be 60 characters or fewer.';
}
if (strlen($password) < 8) {
    $errors['password'] = 'The password must be at least 8 characters.';
} elseif (strlen($password) > 72) {
    $errors['password'] = 'The password must be 72 characters or fewer.';
}
if ($errors) {
    fail(422, 'Please correct the highlighted fields.', $errors);
}

$id = db_insert('admins', [
    'username'      => $username,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'name'          => $name,
    'created_at'    => now(),
    'is_active'     => 1,
]);

super_log('Added account', $username, 'Name: "' . $name . '"');

ok(['id' => $id, 'username' => $username, 'name' => $name, 'last_login' => null, 'created_at' => now()], 'The account was created.');
