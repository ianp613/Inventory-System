<?php
/** GET ?parcel_id=  -> { data: { parcel, farmer, entries: [ { changed_at, event, changes: [{label, old, new}], snapshot } ] } } */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_get();
require_auth();

$id = (int) ($_GET['parcel_id'] ?? 0);
$history = $id > 0 ? load_parcel_history($id) : null;
if (!$history) {
    fail(404, 'Parcel not found.');
}
ok($history);
