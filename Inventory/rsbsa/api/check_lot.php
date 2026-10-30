<?php
/**
 * GET ?lot=&barangay=&farmer_id=&surname=&first_name=&spouse_surname=&spouse_first_name=
 * Tells the registration form whether a lot is already registered to another farmer.
 * save_farmer.php repeats every one of these checks, so this endpoint is only a early warning.
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_get();
require_auth();

$lot = clean_str($_GET['lot'] ?? '');
if ($lot === '') {
    ok(['registered' => false, 'spouse_conflict' => false]);
}

// the same lot number in another barangay is a different piece of land, so the farm barangay is needed to check
$barangay = clean_str($_GET['barangay'] ?? '');
if ($barangay === '') {
    ok(['registered' => false, 'spouse_conflict' => false, 'needs_barangay' => true]);
}

$farmerId = (int) ($_GET['farmer_id'] ?? 0);
$holders = lot_holders($lot, $barangay, $farmerId > 0 ? $farmerId : null);

if (!$holders) {
    ok(['registered' => false, 'spouse_conflict' => false]);
}

$declared = ha($holders[0]['tax_declared_area_ha']);
$used = 0.0;
$owners = [];
foreach ($holders as $h) {
    $used += (float) $h['area_ha'];
    $owners[$h['farmer_id']] = person_name($h['surname'], $h['first_name'], $h['middle_name'], $h['suffix'])
        . ($h['rsbsa_no'] ? ' (' . $h['rsbsa_no'] . ')' : '');
}

// the spouse rules do not apply once the marriage has ended
$ended = in_array($_GET['civil_status'] ?? '', ['Widowed', 'Separated', 'Annulled'], true);
$spouse = $ended ? null : spouse_holder($holders, [
    'surname'           => clean_str($_GET['surname'] ?? ''),
    'first_name'        => clean_str($_GET['first_name'] ?? ''),
    'spouse_surname'    => clean_str($_GET['spouse_surname'] ?? ''),
    'spouse_first_name' => clean_str($_GET['spouse_first_name'] ?? ''),
]);

ok([
    'registered'           => true,
    'spouse_conflict'      => $spouse !== null,
    'tax_declared_area_ha' => $declared,
    'remaining_area_ha'    => max(0.0, ha($declared - $used)),
    'owners'               => array_values($owners),
    'message'              => $spouse !== null
        ? 'Your spouse (' . person_name($spouse['surname'], $spouse['first_name']) . ') is already registered with this lot. '
          . 'Use the update form of the registered spouse instead.'
        : '',
]);
