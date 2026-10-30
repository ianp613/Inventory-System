<?php
/** POST -> { success } */
require __DIR__ . '/_bootstrap.php';

require_post();
$reason = json_decode((string) file_get_contents('php://input'), true)['reason'] ?? '';
if (current_admin()) {
    $auto = $reason === 'inactivity';
    log_activity('Login', $auto ? 'Logged out automatically' : 'Logged out', $auto ? 'No activity for the auto-logout time.' : '');
}
destroy_session();
ok([], 'Logged out.');
