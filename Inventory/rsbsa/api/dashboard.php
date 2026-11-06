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
    $byCommodity[$r['commodity'] === 'Livestock' ? 'Animals' : $r['commodity']] = ['farmers' => (int) $r['farmers'], 'area_ha' => ha($r['area_ha'])];
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

// Animal Monitoring: heads on Active Animals parcels, per animal group; optional ?animal_barangay=
$animalBarangay = clean_str($_GET['animal_barangay'] ?? '');
$aWhere = "p.commodity = 'Animals' AND p.parcel_status = 'Active'" . ($animalBarangay !== '' ? ' AND LOWER(p.loc_barangay) = LOWER(?)' : '');
$aArgs = $animalBarangay !== '' ? [$animalBarangay] : [];
$typeToGroup = [];
foreach (ANIMAL_CHART_GROUPS as $g => $types) {
    foreach ($types as $t) {
        $typeToGroup[$t] = $g;
    }
}
$animalGroups = [];
foreach (array_keys(ANIMAL_CHART_GROUPS) as $g) {
    $animalGroups[$g] = ['group' => $g, 'heads' => 0, 'farmers' => [], 'parts' => []];
}
foreach (q_all("SELECT pl.animal_type AS t, p.farmer_id AS fid, SUM(pl.heads) AS heads
                  FROM parcel_livestock pl JOIN parcels p ON p.id = pl.parcel_id
                 WHERE $aWhere GROUP BY pl.animal_type, p.farmer_id", $aArgs) as $r) {
    $g = $typeToGroup[$r['t']] ?? 'Other';
    $n = (int) $r['heads'];
    $animalGroups[$g]['heads'] += $n;
    if ($n > 0) {
        $animalGroups[$g]['farmers'][$r['fid']] = true;
    }
    $label = preg_replace('/^' . preg_quote($g, '/') . ' - /', '', (string) $r['t']);
    if (!str_contains($r['t'], ' - ') && isset(ANIMAL_CHART_GROUPS[$g]) && in_array($r['t'], ['Carabao', 'Cow', 'Swine', 'Goat', 'Sheep', 'Poultry', 'Other'], true)) {
        $label = $r['t'] === 'Other' ? 'Other' : $r['t'] . ' (not specified)';
    }
    $animalGroups[$g]['parts'][$label] = ($animalGroups[$g]['parts'][$label] ?? 0) + $n;
}
$allFarmers = [];
$animalRows = [];
foreach ($animalGroups as $g) {
    $allFarmers += $g['farmers'];
    $parts = [];
    foreach ($g['parts'] as $label => $n) {
        if ($n > 0) {
            $parts[] = ['label' => $label, 'heads' => $n];
        }
    }
    $animalRows[] = ['group' => $g['group'], 'heads' => $g['heads'], 'farmers' => count($g['farmers']), 'parts' => $parts];
}
// trend: heads on Active Animals parcels, added up month by month by when each parcel was registered (last 12 months)
$trendGroups = array_keys(ANIMAL_CHART_GROUPS);
$firstMonth = new DateTime('first day of this month 00:00:00');
$firstMonth->modify('-11 months');
$trend = [];
$cursor = clone $firstMonth;
for ($i = 0; $i < 12; $i++) {
    $trend[$cursor->format('Y-m')] = ['month' => $cursor->format('Y-m'), 'label' => $cursor->format('M Y'), 'groups' => array_fill_keys($trendGroups, 0)];
    $cursor->modify('+1 month');
}
$baseline = array_fill_keys($trendGroups, 0);   // heads on parcels registered before the 12 months
$startKey = $firstMonth->format('Y-m');
foreach (q_all("SELECT SUBSTR(p.created_at, 1, 7) AS m, pl.animal_type AS t, SUM(pl.heads) AS heads
                  FROM parcel_livestock pl JOIN parcels p ON p.id = pl.parcel_id
                 WHERE $aWhere GROUP BY SUBSTR(p.created_at, 1, 7), pl.animal_type", $aArgs) as $r) {
    $g = $typeToGroup[$r['t']] ?? 'Other';
    if ($r['m'] < $startKey) {
        $baseline[$g] += (int) $r['heads'];
    } elseif (isset($trend[$r['m']])) {
        $trend[$r['m']]['groups'][$g] += (int) $r['heads'];
    }
}
$running = $baseline;
foreach ($trend as $k => $m) {
    foreach ($trendGroups as $g) {
        $running[$g] += $m['groups'][$g];
    }
    $trend[$k]['groups'] = $running;
    $trend[$k]['total'] = array_sum($running);
}

$animals = [
    'trend'       => array_values($trend),
    'barangay'    => $animalBarangay,
    'total_heads' => array_sum(array_column($animalRows, 'heads')),
    'farmers'     => count($allFarmers),
    'groups'      => $animalRows,
];

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
    'animals'        => $animals,
    'animal_barangays' => array_column(q_all("SELECT DISTINCT loc_barangay AS b FROM parcels WHERE commodity = 'Animals' AND loc_barangay <> '' ORDER BY loc_barangay"), 'b'),
    'by_barangay'    => $byBarangay,
    'by_month'       => array_values($byMonth),
    'recent_updates' => $recent,
]);
