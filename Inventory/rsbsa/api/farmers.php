<?php
/**
 * GET ?q=&commodity=&role=&barangay=&flag=open|resolved&sort=&dir=asc|desc&page=1&per_page=25
 * -> { data: { rows, total, page, per_page, barangays } }
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_get();
require_auth();

$q         = clean_str($_GET['q'] ?? '');
$commodity = clean_str($_GET['commodity'] ?? '');
$role      = clean_str($_GET['role'] ?? '');
$barangay  = clean_str($_GET['barangay'] ?? '');
$flagFilter = clean_str($_GET['flag'] ?? '');            // '' = everyone, 'open', 'resolved'
if (($_GET['flagged'] ?? '0') === '1' && $flagFilter === '') {
    $flagFilter = 'open';                                // older callers: flagged=1 means open flags
}
$dir       = strtolower((string) ($_GET['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
[$page, $perPage] = page_params();

$sortMap = [
    'rsbsa_no'      => ['f.rsbsa_no'],
    'name'          => ['f.surname', 'f.first_name', 'f.middle_name'],
    'total_area_ha' => ['total_area_ha'],
    'date_updated'  => ['f.updated_at'],
];
$sort = $_GET['sort'] ?? 'date_updated';
$sortCols = $sortMap[$sort] ?? $sortMap['date_updated'];
$orderBy = implode(', ', array_map(fn($c) => "$c $dir", $sortCols)) . ', f.id ASC';

/* ---------- filters ---------- */
$where = [];
$args = [];

// every word must match somewhere: name, RSBSA no., residential barangay, lot no. or farm barangay
foreach (preg_split('/[\s,]+/u', $q, -1, PREG_SPLIT_NO_EMPTY) as $token) {
    $like = like_pattern($token);
    $where[] = "(f.surname LIKE ? ESCAPE '!' OR f.first_name LIKE ? ESCAPE '!' OR f.middle_name LIKE ? ESCAPE '!'
                 OR f.rsbsa_no LIKE ? ESCAPE '!' OR f.addr_barangay LIKE ? ESCAPE '!'
                 OR EXISTS (SELECT 1 FROM parcels p WHERE p.farmer_id = f.id
                            AND (p.cadastral_lot_no LIKE ? ESCAPE '!' OR p.loc_barangay LIKE ? ESCAPE '!')))";
    array_push($args, $like, $like, $like, $like, $like, $like, $like);
}

// commodity, status and duplicate flag must all match the SAME parcel
$parcelConds = [];
$parcelArgs = [];
if (in_array($commodity, COMMODITIES, true)) {
    $parcelConds[] = 'p.commodity = ?';
    $parcelArgs[] = $commodity;
}
if (in_array($role, FARMING_ROLES, true)) {
    $parcelConds[] = 'p.farming_role = ?';
    $parcelArgs[] = $role;
}
if ($flagFilter === 'open' || $flagFilter === 'resolved') {
    $parcelConds[] = 'EXISTS (SELECT 1 FROM duplicate_flags d WHERE d.parcel_id = p.id AND d.status = ?)';
    $parcelArgs[] = $flagFilter;
}
if ($parcelConds) {
    $where[] = 'EXISTS (SELECT 1 FROM parcels p WHERE p.farmer_id = f.id AND ' . implode(' AND ', $parcelConds) . ')';
    array_push($args, ...$parcelArgs);
}

if ($barangay !== '') {
    $where[] = '(f.addr_barangay = ? OR EXISTS (SELECT 1 FROM parcels p WHERE p.farmer_id = f.id AND p.loc_barangay = ?))';
    array_push($args, $barangay, $barangay);
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ---------- count + page of rows ---------- */
$total = (int) q_val("SELECT COUNT(*) FROM farmers f $whereSql", $args);

$rows = q_all(
    "SELECT f.id, f.rsbsa_no, f.surname, f.first_name, f.middle_name, f.suffix, f.addr_barangay, f.updated_at,
            (SELECT COUNT(*) FROM parcels p WHERE p.farmer_id = f.id) AS parcel_count,
            (SELECT COALESCE(SUM(p.area_ha), 0) FROM parcels p WHERE p.farmer_id = f.id AND p.parcel_status = 'Active') AS total_area_ha,
            (SELECT COUNT(*) FROM duplicate_flags d WHERE d.farmer_id = f.id AND d.status = 'open') AS open_flags,
            (SELECT COUNT(*) FROM duplicate_flags d WHERE d.farmer_id = f.id AND d.status = 'resolved') AS resolved_flags
       FROM farmers f
       $whereSql
      ORDER BY $orderBy
      LIMIT ? OFFSET ?",
    [...$args, $perPage, ($page - 1) * $perPage]
);

$commodities = [];
if ($rows) {
    $ids = array_map(fn($r) => (int) $r['id'], $rows);
    $marks = implode(', ', array_fill(0, count($ids), '?'));
    foreach (q_all("SELECT DISTINCT farmer_id, commodity FROM parcels WHERE farmer_id IN ($marks) ORDER BY commodity", $ids) as $c) {
        $commodities[(int) $c['farmer_id']][] = $c['commodity'];
    }
}

$out = array_map(fn($r) => [
    'id'            => (int) $r['id'],
    'rsbsa_no'      => $r['rsbsa_no'],
    'name'          => person_name($r['surname'], $r['first_name'], $r['middle_name'], $r['suffix']),
    'barangay'      => $r['addr_barangay'],
    'commodities'   => $commodities[(int) $r['id']] ?? [],
    'parcels'       => (int) $r['parcel_count'],
    'total_area_ha' => ha($r['total_area_ha']),
    'date_updated'  => $r['updated_at'],
    'flagged'       => (int) $r['open_flags'] > 0,                       // an open duplicate flag
    'flag_state'    => (int) $r['open_flags'] > 0 ? 'open' : ((int) $r['resolved_flags'] > 0 ? 'resolved' : null),
], $rows);

// options for the barangay filter: residential and farm barangays
$barangays = array_values(array_filter(array_column(
    q_all("SELECT addr_barangay AS b FROM farmers UNION SELECT loc_barangay FROM parcels ORDER BY b"),
    'b'
), fn($b) => trim((string) $b) !== ''));

ok([
    'rows'      => $out,
    'total'     => $total,
    'page'      => $page,
    'per_page'  => $perPage,
    'barangays' => $barangays,
]);
