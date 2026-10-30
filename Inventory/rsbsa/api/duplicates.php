<?php
/**
 * GET ?status=open|resolved|all&q=&page=1&per_page=25
 * -> { data: { rows, total, page, per_page, counts: { open, resolved } } }
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_get();
require_auth();

$status = $_GET['status'] ?? 'open';
if (!in_array($status, ['open', 'resolved', 'all'], true)) {
    $status = 'open';
}
$q = clean_str($_GET['q'] ?? '');
[$page, $perPage] = page_params();

$where = [];
$args = [];
if ($status !== 'all') {
    $where[] = 'd.status = ?';
    $args[] = $status;
}
foreach (preg_split('/[\s,]+/u', $q, -1, PREG_SPLIT_NO_EMPTY) as $token) {
    $like = like_pattern($token);
    $where[] = "(d.lot_no LIKE ? ESCAPE '!' OR d.barangay LIKE ? ESCAPE '!'
                 OR f.surname LIKE ? ESCAPE '!' OR f.first_name LIKE ? ESCAPE '!'
                 OR rf.surname LIKE ? ESCAPE '!' OR rf.first_name LIKE ? ESCAPE '!')";
    array_push($args, $like, $like, $like, $like, $like, $like);
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$from = 'FROM duplicate_flags d
         LEFT JOIN farmers f  ON f.id  = d.farmer_id
         LEFT JOIN farmers rf ON rf.id = d.registered_farmer_id';

$total = (int) q_val("SELECT COUNT(*) $from $whereSql", $args);

$rows = q_all(
    "SELECT d.*, f.rsbsa_no AS farmer_rsbsa_no,
            f.surname AS f_surname, f.first_name AS f_first, f.middle_name AS f_middle, f.suffix AS f_suffix,
            rf.surname AS r_surname, rf.first_name AS r_first, rf.middle_name AS r_middle, rf.suffix AS r_suffix
       $from
       $whereSql
      ORDER BY d.flagged_at DESC, d.id DESC
      LIMIT ? OFFSET ?",
    [...$args, $perPage, ($page - 1) * $perPage]
);

$docs = [];
if ($rows) {
    $ids = array_map(fn($r) => (int) $r['id'], $rows);
    $marks = implode(', ', array_fill(0, count($ids), '?'));
    foreach (q_all("SELECT id, flag_id, file_name FROM duplicate_documents WHERE flag_id IN ($marks) ORDER BY id", $ids) as $d) {
        $docs[(int) $d['flag_id']][] = ['name' => $d['file_name'], 'url' => file_url('support', (int) $d['id'])];
    }
}

$out = array_map(fn($r) => [
    'id'                   => (int) $r['id'],
    'lot_no'               => $r['lot_no'],
    'barangay'             => $r['barangay'],
    'farmer_id'            => $r['farmer_id'] !== null ? (int) $r['farmer_id'] : null,
    'farmer_name'          => person_name($r['f_surname'], $r['f_first'], $r['f_middle'], $r['f_suffix']),
    'farmer_rsbsa_no'      => $r['farmer_rsbsa_no'],
    'registered_farmer_id' => $r['registered_farmer_id'] !== null ? (int) $r['registered_farmer_id'] : null,
    'registered_to'        => person_name($r['r_surname'], $r['r_first'], $r['r_middle'], $r['r_suffix']),
    'area_ha'              => ha($r['area_ha']),
    'tax_declared_area_ha' => ha($r['tax_declared_area_ha']),
    'status'               => $r['status'],
    'remarks'              => (string) $r['remarks'],
    'flagged_at'           => $r['flagged_at'],
    'resolved_at'          => $r['resolved_at'],
    'documents'            => $docs[(int) $r['id']] ?? [],
], $rows);

$counts = ['open' => 0, 'resolved' => 0];
foreach (q_all('SELECT status, COUNT(*) AS n FROM duplicate_flags GROUP BY status') as $c) {
    $counts[$c['status']] = (int) $c['n'];
}

ok([
    'rows'     => $out,
    'total'    => $total,
    'page'     => $page,
    'per_page' => $perPage,
    'counts'   => $counts,
]);
