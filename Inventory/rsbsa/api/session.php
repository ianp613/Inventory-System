<?php
/** GET -> { success, data: { authenticated: bool, user: {..., role: 'admin'|'super'} | null } }   (never an error when logged out) */
require __DIR__ . '/_bootstrap.php';

require_get();
$user = current_admin();
if ($user) {
    $user['role'] = 'admin';
} else {
    $user = current_super();
}
session_write_close();

ok(['authenticated' => $user !== null, 'user' => $user]);
