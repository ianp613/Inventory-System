<?php
/**
 * POST (JSON) { id }
 * Permanently deletes a farmer together with all parcels, crop / livestock data, children,
 * duplicate flags of those parcels, and every attached file (tax declarations and supporting documents).
 * -> { success, message, data: { parcels, files, orphaned_flags } }
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_post();
require_auth();
$in = read_json();

$id = (int) ($in['id'] ?? 0);
$farmer = $id > 0 ? q_one('SELECT id, rsbsa_no, surname, first_name, middle_name, suffix FROM farmers WHERE id = ?', [$id]) : null;
if (!$farmer) {
    fail(404, 'Farmer record not found.');
}

$parcels = q_all('SELECT id, tax_file_path FROM parcels WHERE farmer_id = ?', [$id]);
$files = array_values(array_filter(array_column($parcels, 'tax_file_path')));
$own = q_one('SELECT photo_path, valid_id_path FROM farmers WHERE id = ?', [$id]);
foreach (['photo_path', 'valid_id_path'] as $col) {
    if (!empty($own[$col])) {
        $files[] = $own[$col];
    }
}

// flags raised against this farmer or against any of his / her parcels
$flagIds = array_map('intval', array_column(q_all(
    'SELECT id FROM duplicate_flags WHERE farmer_id = ? OR parcel_id IN (SELECT id FROM parcels WHERE farmer_id = ?)',
    [$id, $id]
), 'id'));
array_push($files, ...flag_document_paths($flagIds));

// flags of OTHER farmers that name this farmer as the one already holding the lot
$orphaned = (int) q_val('SELECT COUNT(*) FROM duplicate_flags WHERE registered_farmer_id = ? AND farmer_id <> ?', [$id, $id]);

$pdo = db();
$pdo->beginTransaction();
try {
    delete_flags($flagIds);
    q('DELETE FROM parcels WHERE farmer_id = ?', [$id]);   // crop areas and livestock go with them
    q('DELETE FROM children WHERE farmer_id = ?', [$id]);
    q('DELETE FROM farmers WHERE id = ?', [$id]);
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $e;
}

// the records are gone; now remove the files from the disk
$deleted = 0;
foreach ($files as $rel) {
    if (delete_stored_file($rel)) {
        $deleted++;
    }
}

$name = person_name($farmer['surname'], $farmer['first_name'], $farmer['middle_name'], $farmer['suffix']);
log_activity('Farmer', 'Deleted farmer record', "$name (" . ($farmer['rsbsa_no'] ?: 'no number') . '), ' . count($parcels) . ' parcel(s) and ' . $deleted . ' attached file(s) removed.');
ok(
    ['parcels' => count($parcels), 'files' => $deleted, 'orphaned_flags' => $orphaned],
    "Deleted $name, " . count($parcels) . ' parcel(s) and ' . $deleted . ' attached file(s).'
);
