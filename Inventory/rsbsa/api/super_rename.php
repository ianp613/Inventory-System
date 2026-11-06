<?php
/**
 * POST (JSON) { id, name, username? }  -> changes the display name and/or the username of an administrator account.
 * The username may use letters, digits, dot, dash and underscore (3-60 characters) and must be unused.
 * The administrator uses the new username the next time they log in. Super user only. Written to the super user log only.
 */
require __DIR__ . '/_bootstrap.php';

require_post();
require_super();
$in = read_json();

$id = (int) ($in['id'] ?? 0);
$name = clean_str($in['name'] ?? '');
$row = q_one('SELECT id, username, name FROM admins WHERE id = ?', [$id]);
if (!$row) {
    fail(404, 'That account was not found.');
}

$errors = [];
if ($name === '') {
    $errors['name'] = 'Enter a name.';
} elseif (mb_strlen($name) > 60) {
    $errors['name'] = 'The name must be 60 characters or fewer.';
}

$username = array_key_exists('username', $in) ? clean_str($in['username']) : $row['username'];
if ($username !== $row['username']) {
    if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $username)) {
        $errors['username'] = 'Use 3 to 60 letters, numbers, dots, dashes or underscores (no spaces).';
    } elseif (strcasecmp($username, SUPER_USERNAME) === 0) {
        $errors['username'] = 'That username is reserved.';
    } elseif (q_one('SELECT id FROM admins WHERE LOWER(username) = LOWER(?) AND id <> ?', [$username, $id])) {
        $errors['username'] = 'That username is already used by another account.';
    }
}
if ($errors) {
    fail(422, 'Please correct the highlighted fields.', $errors);
}

q('UPDATE admins SET name = ?, username = ? WHERE id = ?', [$name, $username, $id]);
$changes = [];
if ($name !== $row['name']) {
    $changes[] = 'Name: "' . $row['name'] . '" to "' . $name . '"';
}
if ($username !== $row['username']) {
    $changes[] = 'Username: "' . $row['username'] . '" to "' . $username . '"';
}
if ($changes) {
    super_log('Edited account', $username, implode('; ', $changes));
}
if ($username !== $row['username']) {
    // a lock from failed attempts under the old username no longer applies
    q('DELETE FROM login_attempts WHERE LOWER(username) = LOWER(?) OR username = ?', [$row['username'], substr('password:' . $row['username'], 0, 60)]);
}

ok(['id' => $id, 'name' => $name, 'username' => $username], 'The account was updated.');
