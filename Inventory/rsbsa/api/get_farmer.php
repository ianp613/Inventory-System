<?php
/** GET ?id=  -> { data: { farmer: {...} } }   404 when the record does not exist */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_get();
require_auth();

$id = (int) ($_GET['id'] ?? 0);
$farmer = $id > 0 ? load_farmer($id) : null;
if (!$farmer) {
    fail(404, 'Farmer record not found.');
}

$name = person_name($farmer['surname'], $farmer['first_name'], $farmer['middle_name'], $farmer['suffix']) . ' (' . $farmer['rsbsa_no'] . ')';
$me = current_admin();
$recent = q_val("SELECT COUNT(*) FROM activity_log WHERE admin_id = ? AND action = 'Opened farmer record' AND details = ? AND created_at >= ?",
    [$me['id'], $name, date('Y-m-d H:i:s', time() - 60)]);
if (!$recent) {
    log_activity('Farmer', 'Opened farmer record', $name);
}

ok(['farmer' => $farmer]);
