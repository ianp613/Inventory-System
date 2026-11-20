<?php
/**
 * GET -> { success, data: { super: { username, default_password, last_login }, accounts: [ { id, username, name, last_login, created_at } ] } }
 * Super user only. Nothing here is written to the activity log.
 */
require __DIR__ . '/_bootstrap.php';

require_get();
$me = require_super();
$su = ensure_super_user($me['sid']);

ok([
    'super' => [
        'username'         => $me['username'],
        'default_password' => empty($su['password_changed_at']),
        'last_login'       => $su['last_login'],
    ],
    'accounts' => array_map(fn($a) => [
        'id'         => (int) $a['id'],
        'username'   => $a['username'],
        'name'       => $a['name'],
        'last_login' => $a['last_login'],
        'created_at' => $a['created_at'],
        'is_active'  => (int) $a['is_active'] === 1,
    ], q_all('SELECT id, username, name, last_login, created_at, is_active FROM admins ORDER BY id')),
]);
