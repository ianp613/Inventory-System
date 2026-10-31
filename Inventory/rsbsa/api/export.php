<?php
/**
 * GET ?report=farmers|duplicates|parcel_history&format=xlsx|csv&...filters
 *   farmers    : commodity, role, barangay, updated_from (YYYY-MM-DD), updated_to (YYYY-MM-DD)
 *   duplicates : status = all | open | resolved
 *   parcel_history : parcel_id (history of one parcel)
 *   activity   : q, category, user, from, to (same filters as the Activity Logs page)
 * Sends the file as a download. Errors are JSON (the page shows the message).
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';
require __DIR__ . '/_xlsx.php';

require_get();
require_auth();
set_time_limit(120);

$report = $_GET['report'] ?? '';
$format = $_GET['format'] ?? 'xlsx';
if (!in_array($report, ['farmers', 'duplicates', 'parcel_history', 'activity'], true)) {
    fail(400, 'Unknown report.');
}
if (!in_array($format, ['xlsx', 'csv', 'json'], true)) {   // json = the on-screen preview
    fail(400, 'Unknown file format.');
}

function valid_date(string $s): bool
{
    $d = DateTime::createFromFormat('!Y-m-d', $s);
    return $d && $d->format('Y-m-d') === $s;
}

/* =====================================================================
 * Build the table: $title, $subtitle, $headers, $rows, $widths
 * =================================================================== */
if ($report === 'farmers') {
    $commodity = clean_str($_GET['commodity'] ?? '');
    $role      = clean_str($_GET['role'] ?? '');
    $barangay  = clean_str($_GET['barangay'] ?? '');
    $from      = clean_str($_GET['updated_from'] ?? '');
    $to        = clean_str($_GET['updated_to'] ?? '');

    $where = [];
    $args = [];
    $filters = [];
    if (in_array($commodity, COMMODITIES, true)) {
        $where[] = 'p.commodity = ?';
        $args[] = $commodity;
        $filters[] = "Commodity: $commodity";
    }
    if (in_array($role, FARMING_ROLES, true)) {
        $where[] = 'p.farming_role = ?';
        $args[] = $role;
        $filters[] = "Status: $role";
    }
    if ($barangay !== '') {
        $where[] = '(f.addr_barangay = ? OR p.loc_barangay = ?)';
        array_push($args, $barangay, $barangay);
        $filters[] = "Barangay: $barangay";
    }
    if ($from !== '') {
        if (!valid_date($from)) {
            fail(422, 'The start date is not valid.');
        }
        $where[] = 'f.updated_at >= ?';
        $args[] = $from . ' 00:00:00';
        $filters[] = "Updated from $from";
    }
    if ($to !== '') {
        if (!valid_date($to)) {
            fail(422, 'The end date is not valid.');
        }
        $where[] = 'f.updated_at <= ?';
        $args[] = $to . ' 23:59:59';
        $filters[] = "Updated to $to";
    }
    if ($from !== '' && $to !== '' && $from > $to) {
        fail(422, 'The end date must be on or after the start date.');
    }

    $data = q_all(
        'SELECT f.*, p.id AS parcel_id, p.cadastral_lot_no, p.area_ha, p.tax_declared_area_ha, p.cultivated_area_ha,
                p.loc_zone_sitio, p.loc_barangay, p.loc_municipality, p.loc_province, p.commodity, p.fish_size,
                p.farming_role, p.tax_declarant, p.association AS parcel_association, p.parcel_status, p.duplicate_flag,
                (SELECT d.status FROM duplicate_flags d WHERE d.parcel_id = p.id ORDER BY d.id DESC LIMIT 1) AS flag_status
           FROM farmers f
           LEFT JOIN parcels p ON p.farmer_id = f.id
          ' . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . '
          ORDER BY f.surname, f.first_name, f.id, p.id',
        $args
    );

    $children = [];
    foreach (q_all('SELECT farmer_id, name FROM children ORDER BY id') as $c) {
        $children[(int) $c['farmer_id']][] = $c['name'];
    }
    $crops = [];
    foreach (q_all('SELECT parcel_id, crop_key, area_ha FROM crop_areas') as $c) {
        $crops[(int) $c['parcel_id']][$c['crop_key']] = ha($c['area_ha']);
    }
    $animals = [];
    foreach (q_all('SELECT parcel_id, animal_type, heads FROM parcel_livestock ORDER BY id') as $a) {
        $animals[(int) $a['parcel_id']][] = $a['animal_type'] . ': ' . (int) $a['heads'];
    }

    $title = 'Farmer Masterlist';
    $subtitle = ($filters ? implode(' | ', $filters) . ' | ' : '') . count($data) . ' row(s)';
    $headers = ['RSBSA No.', 'Surname', 'First Name', 'Middle Name', 'Suffix', 'Date of Birth', 'Valid ID', 'ID Number',
        'Residential Address', 'Civil Status', 'Spouse', 'Mother Maiden Name', 'Children',
        'Parcel No.', 'Cadastral Lot No.', 'Area (ha)', 'Tax Decl. Area (ha)', 'Farm Location', 'Sectoral Commodity',
        'Cultivated Area (ha)', 'Fish Cage / Pond Size', 'No. of Heads',
        'Rice (ha)', 'Vegetable (ha)', 'Root Crops (ha)', 'Banana (ha)', 'Coconut (ha)', 'Fruit Bearing (ha)',
        'Farming Status', 'Tax Declarant / Owner', 'Association', 'Parcel Status', 'Duplicate Flag', 'Date Updated'];
    $widths = [16, 16, 16, 14, 8, 12, 18, 16, 36, 14, 24, 20, 30, 8, 14, 11, 13, 36, 14, 13, 16, 22, 10, 11, 11, 10, 10, 13, 14, 24, 20, 12, 10, 18];

    $rows = [];
    $parcelNo = [];
    foreach ($data as $r) {
        $fid = (int) $r['id'];
        $pid = $r['parcel_id'] !== null ? (int) $r['parcel_id'] : null;
        $parcelNo[$fid] = ($parcelNo[$fid] ?? 0) + ($pid ? 1 : 0);
        $hasCrops = $pid && isset($crops[$pid]);
        $place = $pid ? implode(', ', array_filter([$r['loc_zone_sitio'], $r['loc_barangay'], $r['loc_municipality'], $r['loc_province']], 'strlen')) : '';

        $row = [
            $r['rsbsa_no'], $r['surname'], $r['first_name'], $r['middle_name'], $r['suffix'], $r['birth_date'],
            $r['valid_id_type'], $r['valid_id_no'],
            implode(', ', array_filter([$r['addr_zone_sitio'], $r['addr_barangay'], $r['addr_municipality'], $r['addr_province']], 'strlen')),
            $r['civil_status'],
            person_name($r['spouse_surname'], $r['spouse_first_name'], $r['spouse_middle_name']),
            $r['is_married_woman'] ? $r['maiden_name'] : '',
            implode('; ', $children[$fid] ?? []),
            $pid ? $parcelNo[$fid] : '',
            $pid ? $r['cadastral_lot_no'] : '',
            $pid ? ha($r['area_ha']) : '',
            $pid ? ha($r['tax_declared_area_ha']) : '',
            $place,
            $pid ? $r['commodity'] : '',
            $pid ? ha($r['cultivated_area_ha']) : '',
            $pid && $r['commodity'] === 'Fishery' ? $r['fish_size'] : '',
            $pid ? implode('; ', $animals[$pid] ?? []) : '',
        ];
        foreach (CROP_KEYS as $key) {
            $row[] = $hasCrops ? ($crops[$pid][$key] ?? 0.0) : '';
        }
        array_push($row, $pid ? $r['farming_role'] : '', $pid ? $r['tax_declarant'] : '', $pid ? $r['parcel_association'] : '', $pid ? $r['parcel_status'] : '', $pid && $r['duplicate_flag'] ? (($r['flag_status'] ?? 'open') === 'resolved' ? 'Resolved' : 'Yes') : '', $r['updated_at']);
        $rows[] = $row;
    }
    $baseName = 'rsbsa-farmer-masterlist';
} elseif ($report === 'activity') {
    [$whereSql, $args, $filters] = activity_filters();
    $data = q_all("SELECT * FROM activity_log $whereSql ORDER BY created_at DESC, id DESC", $args);
    $title = 'Activity Log';
    $subtitle = ($filters ? implode(' | ', $filters) . ' | ' : '') . count($data) . ' record(s)';
    $headers = ['Date and Time', 'User', 'Username', 'Category', 'Activity', 'Details', 'IP Address'];
    $widths = [20, 24, 16, 12, 30, 80, 14];
    $rows = [];
    foreach ($data as $r) {
        $rows[] = [$r['created_at'], $r['display_name'], $r['username'], $r['category'], $r['action'], $r['details'], $r['ip_address']];
    }
    $baseName = 'rsbsa-activity-log';
} elseif ($report === 'parcel_history') {
    $h = load_parcel_history((int) ($_GET['parcel_id'] ?? 0));
    if (!$h) {
        fail(404, 'Parcel not found.');
    }
    $p = $h['parcel'];
    $title = 'Parcel History';
    $subtitle = $h['farmer']['name'] . ' (' . $h['farmer']['rsbsa_no'] . ') | Lot ' . $p['cadastral_lot_no'] . ', ' . $p['barangay'];
    $headers = ['Date', 'Event', 'Field Changed', 'Previous Value', 'New Value',
        'Cadastral Lot No.', 'Tax Declarant / Owner', 'Parcel Remark', 'Area (ha)', 'Farming Status', 'Commodity', 'Association', 'Farm Location'];
    $widths = [18, 14, 28, 26, 26, 14, 26, 12, 10, 14, 14, 22, 40];

    $rows = [];
    // oldest first reads best in a spreadsheet
    foreach (array_reverse($h['entries']) as $e) {
        $s = $e['snapshot'];
        $tail = [
            $s['cadastral_lot_no'] ?? '', $s['tax_declarant'] ?? '', history_value('parcel_status', $s['parcel_status'] ?? ''),
            isset($s['area_ha']) ? ha($s['area_ha']) : '', $s['farming_role'] ?? '', $s['commodity'] ?? '', $s['association'] ?? '', $s['location'] ?? '',
        ];
        if (!$e['changes']) {
            $rows[] = array_merge([$e['changed_at'], $e['event'], '', '', ''], $tail);
        }
        foreach ($e['changes'] as $c) {
            $rows[] = array_merge([$e['changed_at'], $e['event'], $c['label'], $c['old'], $c['new']], $tail);
        }
    }
    $baseName = 'rsbsa-parcel-history-lot-' . (preg_replace('/[^A-Za-z0-9_-]+/', '_', $p['cadastral_lot_no']) ?: $p['id']);
} else {
    $status = $_GET['status'] ?? 'all';
    if (!in_array($status, ['all', 'open', 'resolved'], true)) {
        $status = 'all';
    }

    $data = q_all(
        'SELECT d.*, f.rsbsa_no AS farmer_rsbsa_no,
                f.surname AS f_surname, f.first_name AS f_first, f.middle_name AS f_middle, f.suffix AS f_suffix,
                rf.rsbsa_no AS r_rsbsa_no,
                rf.surname AS r_surname, rf.first_name AS r_first, rf.middle_name AS r_middle, rf.suffix AS r_suffix
           FROM duplicate_flags d
           LEFT JOIN farmers f  ON f.id  = d.farmer_id
           LEFT JOIN farmers rf ON rf.id = d.registered_farmer_id
          ' . ($status !== 'all' ? 'WHERE d.status = ?' : '') . '
          ORDER BY d.flagged_at DESC, d.id DESC',
        $status !== 'all' ? [$status] : []
    );

    $title = 'Duplication Report';
    $subtitle = ['all' => 'All flags', 'open' => 'Open flags', 'resolved' => 'Resolved flags'][$status] . ' | ' . count($data) . ' record(s)';
    $headers = ['Cadastral Lot No.', 'Barangay', 'Flagged Farmer', 'RSBSA No. (Flagged)', 'Already Registered To', 'RSBSA No. (Registered)',
        'Area (ha)', 'Tax Decl. Area (ha)', 'Flagged On', 'Status', 'Remarks of Duplication', 'Resolved On'];
    $widths = [16, 16, 28, 18, 28, 18, 11, 14, 18, 11, 48, 18];

    $rows = [];
    foreach ($data as $r) {
        $rows[] = [
            $r['lot_no'], $r['barangay'],
            person_name($r['f_surname'], $r['f_first'], $r['f_middle'], $r['f_suffix']), $r['farmer_rsbsa_no'],
            person_name($r['r_surname'], $r['r_first'], $r['r_middle'], $r['r_suffix']), $r['r_rsbsa_no'],
            ha($r['area_ha']), ha($r['tax_declared_area_ha']),
            $r['flagged_at'], ucfirst($r['status']), (string) $r['remarks'], $r['resolved_at'],
        ];
    }
    $baseName = 'rsbsa-duplication-report';
}

if ($format === 'json') {
    // preview: the same table that would be downloaded, first 100 rows only
    $limit = 100;
    log_activity('Report', 'Previewed ' . ['farmers' => 'farmer masterlist', 'duplicates' => 'duplication report', 'parcel_history' => 'parcel history', 'activity' => 'activity log'][$report], $subtitle);
    ok([
        'title'     => $title,
        'subtitle'  => $subtitle,
        'headers'   => $headers,
        'rows'      => array_slice($rows, 0, $limit),
        'total'     => count($rows),
        'truncated' => count($rows) > $limit,
    ]);
}

log_activity('Report', 'Exported ' . ['farmers' => 'farmer masterlist', 'duplicates' => 'duplication report', 'parcel_history' => 'parcel history', 'activity' => 'activity log'][$report],
    strtoupper($format) . ' file. ' . $subtitle);

$stamp = date('Y-m-d');
$fileName = "$baseName-$stamp.$format";
$generated = 'Generated ' . date('F j, Y g:i A');

/* =====================================================================
 * Send it
 * =================================================================== */
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Ñ correctly

    // a cell that starts with = + - @ would be run as a formula by Excel
    $safe = function ($v) {
        if (is_string($v) && $v !== '' && strpbrk($v[0], "=+-@\t\r") !== false && !is_numeric($v)) {
            return "'" . $v;
        }
        return $v;
    };
    fputcsv($out, $headers);
    foreach ($rows as $row) {
        fputcsv($out, array_map($safe, $row));
    }
    fclose($out);
    exit;
}

$x = new SimpleXlsx($title);
$x->addRow(['Palo Registry System for Basic Sectors in Agriculture (RSBSA)'], SimpleXlsx::S_TITLE);
$x->addRow([$title . ' | ' . $subtitle . ' | ' . $generated], SimpleXlsx::S_SUBTITLE);
$x->addBlankRow();
$x->setHeaderRow($x->addRow($headers, SimpleXlsx::S_HEADER));
foreach ($rows as $row) {
    $x->addRow($row);
}
$x->setColumnWidths($widths);
$tmp = $x->save();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($tmp));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
readfile($tmp);
@unlink($tmp);
exit;
