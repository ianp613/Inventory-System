<?php
/**
 * POST (JSON) { id, active: bool }  -> disables or enables an administrator account.
 * A disabled account cannot log in, and a session that is open when it is disabled ends with the next request.
 * Super user only. Written to the super user log only.
 */
require __DIR__ . '/_bootstrap.php';

require_post();
require_super();
$in = read_json();

$id = (int) ($in['id'] ?? 0);
$active = !empty($in['active']);
$row = q_one('SELECT id, username, name, is_active FROM admins WHERE id = ?', [$id]);
if (!$row) {
    fail(404, 'That account was not found.');
}

if ((int) $row['is_active'] !== ($active ? 1 : 0)) {
    q('UPDATE admins SET is_active = ? WHERE id = ?', [$active ? 1 : 0, $id]);
    if (!$active) {
        q('DELETE FROM login_attempts WHERE LOWER(username) = LOWER(?)', [$row['username']]);
    }
    super_log($active ? 'Enabled account' : 'Disabled account', $row['username'], 'Account of "' . $row['name'] . '".');
}

ok(['id' => $id, 'is_active' => $active], $active ? 'The account was enabled.' : 'The account was disabled.');
