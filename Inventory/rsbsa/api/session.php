<?php
/** GET -> { success, data: { authenticated: bool, user: {...} | null } }   (never an error when logged out) */
require __DIR__ . '/_bootstrap.php';

require_get();
$admin = current_admin();
session_write_close();

ok(['authenticated' => $admin !== null, 'user' => $admin]);
