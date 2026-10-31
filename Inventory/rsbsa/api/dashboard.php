<?php
/** GET -> summary figures for dashboard.php */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';
require __DIR__ . '/_backup.php';

require_get();
require_auth();

$totals = [
    'farmers'         => (int) q_val('SELECT COUNT(*) FROM farmers'),
    'parcels'         => (int) q_val('SELECT COUNT(*) FROM parcels'),
    'total_area_ha'   => ha(q_val("SELECT COALESCE(SUM(area_ha), 0) FROM parcels WHERE parcel_status = 'Active'")),
    'inactive_parcels' => (int) q_val("SELECT COUNT(*) FROM parcels WHERE parcel_status <> 'Active'"),
    'duplicate_flags' => (int) q_val("SELECT COUNT(*) FROM duplicate_flags WHERE status = 'open'"),
];

// every commodity is listed, even with no records yet
$byCommodity = array_fill_keys(COMMODITIES, ['farmers' => 0, 'area_ha' => 0.0]);
// optional ?barangay= limits the commodity chart to the parcels located in that barangay
$onlyBarangay = clean_str($_GET['barangay'] ?? '');
$sqlBrgy = $onlyBarangay !== '' ? ' WHERE LOWER(loc_barangay) = LOWER(?)' : '';
foreach (q_all("SELECT commodity, COUNT(DISTINCT farmer_id) AS farmers, COALESCE(SUM(CASE WHEN parcel_status = 'Active' THEN area_ha ELSE 0 END), 0) AS area_ha FROM parcels$sqlBrgy GROUP BY commodity", $onlyBarangay !== '' ? [$onlyBarangay] : []) as $r) {
    $byCommodity[$r['commodity']] = ['farmers' => (int) $r['farmers'], 'area_ha' => ha($r['area_ha'])];
}
$commodityRows = [];
foreach ($byCommodity as $name => $v) {
    $commodityRows[] = ['commodity' => $name, 'farmers' => $v['farmers'], 'area_ha' => $v['area_ha']];
}
usort($commodityRows, fn($a, $b) => $b['area_ha'] <=> $a['area_ha'] ?: strcmp($a['commodity'], $b['commodity']));

$byBarangay = array_map(
    fn($r) => ['barangay' => $r['barangay'], 'farmers' => (int) $r['farmers'], 'area_ha' => ha($r['area_ha'])],
    q_all("SELECT loc_barangay AS barangay, COUNT(DISTINCT farmer_id) AS farmers, COALESCE(SUM(CASE WHEN parcel_status = 'Active' THEN area_ha ELSE 0 END), 0) AS area_ha
             FROM parcels
            GROUP BY loc_barangay
            ORDER BY farmers DESC, area_ha DESC, loc_barangay
            LIMIT 10")
);

$recent = array_map(
    fn($r) => [
        'rsbsa_no'      => $r['rsbsa_no'],
        'name'          => person_name($r['surname'], $r['first_name'], $r['middle_name'], $r['suffix']),
        'barangay'      => $r['addr_barangay'],
        'total_area_ha' => ha($r['total_area_ha']),
        'date_updated'  => $r['updated_at'],
    ],
    q_all("SELECT f.rsbsa_no, f.surname, f.first_name, f.middle_name, f.suffix, f.addr_barangay, f.updated_at,
                  (SELECT COALESCE(SUM(p.area_ha), 0) FROM parcels p WHERE p.farmer_id = f.id AND p.parcel_status = 'Active') AS total_area_ha
             FROM farmers f
            ORDER BY f.updated_at DESC, f.id DESC
            LIMIT 10")
);

// registrations per month: the last 12 months, months without registrations show 0
$byMonth = [];
$cursor = new DateTime('first day of this month 00:00:00');
$cursor->modify('-11 months');
for ($i = 0; $i < 12; $i++) {
    $byMonth[$cursor->format('Y-m')] = ['month' => $cursor->format('Y-m'), 'label' => $cursor->format('M Y'), 'farmers' => 0];
    $cursor->modify('+1 month');
}
foreach (q_all('SELECT SUBSTR(created_at, 1, 7) AS m, COUNT(*) AS n FROM farmers WHERE created_at >= ? GROUP BY SUBSTR(created_at, 1, 7)', [array_key_first($byMonth) . '-01 00:00:00']) as $r) {
    if (isset($byMonth[$r['m']])) {
        $byMonth[$r['m']]['farmers'] = (int) $r['n'];
    }
}

backup_auto_if_due();
$lastBackup = null;
if (backup_is_sqlite() && class_exists('ZipArchive')) {
    $lastBackup = backup_list()[0] ?? null;
}

ok([
    'totals'         => $totals,
    'backup'         => ['auto' => backup_settings()['auto'], 'last_at' => $lastBackup['created_at'] ?? null, 'supported' => $lastBackup !== null || (backup_is_sqlite() && class_exists('ZipArchive'))],
    'by_commodity'   => $commodityRows,
    'commodity_barangay' => $onlyBarangay,
    'barangays'      => array_column(q_all("SELECT DISTINCT loc_barangay AS b FROM parcels WHERE loc_barangay <> '' ORDER BY loc_barangay"), 'b'),
    'by_barangay'    => $byBarangay,
    'by_month'       => array_values($byMonth),
    'recent_updates' => $recent,
]);
