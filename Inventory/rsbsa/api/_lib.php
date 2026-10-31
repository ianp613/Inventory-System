<?php
/**
 * Business rules shared by several endpoints (lot / spouse checks, farmer loading).
 */

const COMMODITIES      = ['Rice', 'Vegetable', 'Root Crops', 'Banana', 'Coconut', 'Livestock', 'Fishery'];
const CROP_COMMODITIES = ['Rice', 'Vegetable', 'Root Crops', 'Banana', 'Coconut'];
const CROP_KEYS        = ['rice', 'vegetable', 'root_crops', 'banana', 'coconut', 'fruit_bearing'];
const FARMING_ROLES    = ['Owner', 'Tenant', 'Leasee', 'Actual Tiller', 'Farm Worker'];
const ANIMAL_TYPES     = ['Cow', 'Carabao', 'Sheep', 'Goat', 'Swine', 'Poultry', 'Other'];
const CIVIL_STATUSES = ['Single', 'Married', 'Widowed', 'Separated', 'Annulled'];
/** Parcel remark. Only 'Active' parcels count in a farmer's total area. */
const PARCEL_STATUSES = ['Active', 'Inactive', 'Deceased'];
const AREA_EPSILON     = 0.00005;

function norm_name($s): string
{
    return mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', (string) $s)));
}

/** True when both surname and first name are filled in and equal (case-insensitive). */
function same_person($surname1, $first1, $surname2, $first2): bool
{
    $a = norm_name($surname1);
    $b = norm_name($first1);
    return $a !== '' && $b !== '' && $a === norm_name($surname2) && $b === norm_name($first2);
}

/**
 * Parcels of OTHER farmers that use the same cadastral lot (and barangay, when given).
 * Each row includes the owner's name and spouse details.
 */
function lot_holders(string $lot, string $barangay, ?int $excludeFarmerId): array
{
    $sql = 'SELECT p.id AS parcel_id, p.farmer_id, p.area_ha, p.tax_declared_area_ha,
                   f.rsbsa_no, f.surname, f.first_name, f.middle_name, f.suffix,
                   f.spouse_surname, f.spouse_first_name, f.spouse_separated
              FROM parcels p
              JOIN farmers f ON f.id = p.farmer_id
             WHERE LOWER(p.cadastral_lot_no) = LOWER(?)';
    $args = [$lot];
    if ($barangay !== '') {
        $sql .= ' AND LOWER(p.loc_barangay) = LOWER(?)';
        $args[] = $barangay;
    }
    if ($excludeFarmerId !== null) {
        $sql .= ' AND p.farmer_id <> ?';
        $args[] = $excludeFarmerId;
    }
    return q_all($sql . ' ORDER BY p.id', $args);
}

/**
 * Is any holder of the lot the applicant's spouse (and not separated)?
 * $applicant: surname, first_name, spouse_surname, spouse_first_name
 */
function spouse_holder(array $holders, array $applicant): ?array
{
    foreach ($holders as $h) {
        if (!empty($h['spouse_separated'])) {
            continue;
        }
        $applicantSaysSpouse = same_person($applicant['spouse_surname'] ?? '', $applicant['spouse_first_name'] ?? '', $h['surname'], $h['first_name']);
        $holderSaysSpouse    = same_person($h['spouse_surname'], $h['spouse_first_name'], $applicant['surname'] ?? '', $applicant['first_name'] ?? '');
        if ($applicantSaysSpouse || $holderSaysSpouse) {
            return $h;
        }
    }
    return null;
}

/** Registered farmers that are (by name) the spouse of the applicant, or that list the applicant as their spouse. */
function registered_spouses(?int $selfId, string $surname, string $first, string $spouseSurname, string $spouseFirst): array
{
    $sql = 'SELECT id, rsbsa_no, surname, first_name, spouse_surname, spouse_first_name, spouse_separated
              FROM farmers
             WHERE ((LOWER(TRIM(surname)) = ? AND LOWER(TRIM(first_name)) = ?)
                 OR (LOWER(TRIM(spouse_surname)) = ? AND LOWER(TRIM(spouse_first_name)) = ?))';
    $args = [norm_name($spouseSurname), norm_name($spouseFirst), norm_name($surname), norm_name($first)];
    if ($selfId !== null) {
        $sql .= ' AND id <> ?';
        $args[] = $selfId;
    }
    $rows = q_all($sql, $args);
    return array_values(array_filter($rows, function ($r) use ($surname, $first, $spouseSurname, $spouseFirst) {
        return same_person($spouseSurname, $spouseFirst, $r['surname'], $r['first_name'])
            || same_person($r['spouse_surname'], $r['spouse_first_name'], $surname, $first);
    }));
}

/** Download link for an uploaded file, relative to the page that shows it (pages sit beside api/). */
function file_url(string $type, int $id): string
{
    return 'api/file.php?type=' . $type . '&id=' . $id;
}

/** One farmer in the same shape the registration form sends. */
function load_farmer(int $id): ?array
{
    $f = q_one('SELECT * FROM farmers WHERE id = ?', [$id]);
    if (!$f) {
        return null;
    }

    $hasSpouse = trim($f['spouse_surname'] . $f['spouse_first_name'] . $f['spouse_middle_name']) !== '';

    $parcels = [];
    foreach (q_all('SELECT * FROM parcels WHERE farmer_id = ? ORDER BY id', [$id]) as $p) {
        $pid = (int) $p['id'];

        $crops = null;
        if (in_array($p['commodity'], CROP_COMMODITIES, true)) {
            $crops = array_fill_keys(CROP_KEYS, 0.0);
            foreach (q_all('SELECT crop_key, area_ha FROM crop_areas WHERE parcel_id = ?', [$pid]) as $c) {
                if (array_key_exists($c['crop_key'], $crops)) {
                    $crops[$c['crop_key']] = ha($c['area_ha']);
                }
            }
        }

        $livestock = null;
        if ($p['commodity'] === 'Livestock') {
            $livestock = array_map(
                fn($a) => ['type' => $a['animal_type'], 'heads' => (int) $a['heads']],
                q_all('SELECT animal_type, heads FROM parcel_livestock WHERE parcel_id = ? ORDER BY id', [$pid])
            );
        }

        $parcels[] = [
            'id'                   => $pid,
            'cadastral_lot_no'     => $p['cadastral_lot_no'],
            'area_ha'              => ha($p['area_ha']),
            'tax_declared_area_ha' => ha($p['tax_declared_area_ha']),
            'cultivated_area_ha'   => ha($p['cultivated_area_ha']),
            'location'             => [
                'zone_sitio'   => $p['loc_zone_sitio'],
                'barangay'     => $p['loc_barangay'],
                'municipality' => $p['loc_municipality'],
                'province'     => $p['loc_province'],
            ],
            'commodity'            => $p['commodity'],
            'crop_areas'           => $crops,
            'livestock'            => $livestock,
            'fish_size'            => $p['commodity'] === 'Fishery' ? $p['fish_size'] : null,
            'farming_role'         => $p['farming_role'],
            'tax_declarant'        => $p['tax_declarant'],
            'association'          => $p['association'],
            'parcel_status'        => $p['parcel_status'],
            'duplicate_flag'       => (bool) $p['duplicate_flag'],
            'flag_status'          => $p['duplicate_flag'] ? (q_val('SELECT status FROM duplicate_flags WHERE parcel_id = ? ORDER BY id DESC LIMIT 1', [$pid]) ?: 'open') : null,
            'tax_file_name'        => $p['tax_file_name'],
            'tax_file_url'         => $p['tax_file_path'] ? file_url('tax', $pid) : null,
        ];
    }

    return [
        'id'               => (int) $f['id'],
        'rsbsa_no'         => $f['rsbsa_no'],
        'surname'          => $f['surname'],
        'first_name'       => $f['first_name'],
        'middle_name'      => $f['middle_name'],
        'suffix'           => $f['suffix'],
        'birth_date'       => $f['birth_date'],
        'valid_id_type'    => $f['valid_id_type'],
        'valid_id_no'      => $f['valid_id_no'],
        'address'          => [
            'zone_sitio'   => $f['addr_zone_sitio'],
            'barangay'     => $f['addr_barangay'],
            'municipality' => $f['addr_municipality'],
            'province'     => $f['addr_province'],
        ],
        'spouse'           => $hasSpouse ? [
            'surname'     => $f['spouse_surname'],
            'first_name'  => $f['spouse_first_name'],
            'middle_name' => $f['spouse_middle_name'],
        ] : null,
        'spouse_separated' => (bool) $f['spouse_separated'],
        'civil_status'     => $f['civil_status'],
        'photo_name'       => $f['photo_name'],
        'photo_url'        => $f['photo_path'] ? file_url('photo', (int) $f['id']) : null,
        'valid_id_file_name' => $f['valid_id_name'],
        'valid_id_file_url'  => $f['valid_id_path'] ? file_url('valid_id', (int) $f['id']) : null,
        'is_married_woman' => (bool) $f['is_married_woman'],
        'maiden_name'      => $f['maiden_name'],
        'children'         => array_column(q_all('SELECT name FROM children WHERE farmer_id = ? ORDER BY id', [$id]), 'name'),
        'date_updated'     => $f['updated_at'],
        'total_area_ha'    => ha(array_sum(array_map(fn($p) => $p['parcel_status'] === 'Active' ? $p['area_ha'] : 0, $parcels))),
        'parcels'          => $parcels,
    ];
}

/**
 * Delete one uploaded file ($rel is the path stored in the database, relative to the storage folder).
 * Only files that really sit inside the storage folder can be removed. Returns true when a file was deleted.
 */
function delete_stored_file(?string $rel): bool
{
    if ($rel === null || $rel === '') {
        return false;
    }
    $root = realpath(storage_dir());
    $file = realpath($root . '/' . $rel);
    if ($root === false || $file === false || strncmp($file, $root . DIRECTORY_SEPARATOR, strlen($root) + 1) !== 0 || !is_file($file)) {
        return false;
    }
    return @unlink($file);
}

/** Supporting-document files (paths) of the given duplicate flags. */
function flag_document_paths(array $flagIds): array
{
    if (!$flagIds) {
        return [];
    }
    $marks = implode(', ', array_fill(0, count($flagIds), '?'));
    return array_column(q_all("SELECT file_path FROM duplicate_documents WHERE flag_id IN ($marks)", array_values($flagIds)), 'file_path');
}

/** Delete duplicate flags (and their document rows). The caller deletes the files after the transaction commits. */
function delete_flags(array $flagIds): void
{
    if (!$flagIds) {
        return;
    }
    $marks = implode(', ', array_fill(0, count($flagIds), '?'));
    q("DELETE FROM duplicate_documents WHERE flag_id IN ($marks)", array_values($flagIds));
    q("DELETE FROM duplicate_flags WHERE id IN ($marks)", array_values($flagIds));
}


/* ---------- parcel history ---------- */

/** Parcel columns that are tracked in the history: column => label */
const HISTORY_FIELDS = [
    'cadastral_lot_no'     => 'Cadastral Lot No.',
    'tax_declarant'        => 'Tax Declarant / Owner',
    'parcel_status'        => 'Parcel Remark',
    'farming_role'         => 'Farming Status',
    'area_ha'              => 'Area (ha)',
    'tax_declared_area_ha' => 'Area per Tax Declaration (ha)',
    'cultivated_area_ha'   => 'Cultivated Area (ha)',
    'commodity'            => 'Sectoral Commodity',
    'association'          => 'Name of Association',
    'loc_zone_sitio'       => 'Farm Location - Zone / Sitio',
    'loc_barangay'         => 'Farm Location - Barangay',
    'loc_municipality'     => 'Farm Location - Municipality',
    'loc_province'         => 'Farm Location - Province',
];
const HISTORY_NUMERIC = ['area_ha', 'tax_declared_area_ha', 'cultivated_area_ha'];

function history_value(string $col, $v): string
{
    if ($col === 'parcel_status') {
        return $v === 'Inactive' ? 'In-active' : (string) $v;
    }
    if (in_array($col, HISTORY_NUMERIC, true)) {
        return number_format((float) $v, 4, '.', '');
    }
    return (string) $v;
}

/** The main values of a parcel (stored with each history entry). */
function history_snapshot(array $p): array
{
    return [
        'cadastral_lot_no' => $p['cadastral_lot_no'], 'tax_declarant' => $p['tax_declarant'], 'parcel_status' => $p['parcel_status'],
        'area_ha' => ha($p['area_ha']), 'tax_declared_area_ha' => ha($p['tax_declared_area_ha']),
        'farming_role' => $p['farming_role'], 'commodity' => $p['commodity'], 'association' => $p['association'],
        'location' => implode(', ', array_filter([$p['loc_zone_sitio'], $p['loc_barangay'], $p['loc_municipality'], $p['loc_province']], 'strlen')),
    ];
}

/** What changed between the stored parcel row and the new values: [ {field, label, old, new}, ... ] */
function history_changes(array $old, array $new): array
{
    $out = [];
    foreach (HISTORY_FIELDS as $col => $label) {
        $a = $old[$col] ?? '';
        $b = $new[$col] ?? '';
        $same = in_array($col, HISTORY_NUMERIC, true) ? abs((float) $a - (float) $b) <= AREA_EPSILON : (string) $a === (string) $b;
        if (!$same) {
            $out[] = ['field' => $col, 'label' => $label, 'old' => history_value($col, $a), 'new' => history_value($col, $b)];
        }
    }
    return $out;
}

/** Add one history entry for a parcel ($row = the parcel as it is after the change). */
function record_history(int $parcelId, int $farmerId, string $event, array $changes, array $row, string $when): void
{
    db_insert('parcel_history', [
        'parcel_id'  => $parcelId,
        'farmer_id'  => $farmerId,
        'event'      => $event,
        'changes'    => json_encode($changes, JSON_UNESCAPED_UNICODE),
        'snapshot'   => json_encode(history_snapshot($row), JSON_UNESCAPED_UNICODE),
        'changed_at' => $when,
    ]);
}

/** Full history of one parcel for the History window / exports (newest first), or null when it does not exist. */
function load_parcel_history(int $parcelId): ?array
{
    $p = q_one('SELECT p.*, f.rsbsa_no, f.surname, f.first_name, f.middle_name, f.suffix
                  FROM parcels p JOIN farmers f ON f.id = p.farmer_id WHERE p.id = ?', [$parcelId]);
    if (!$p) {
        return null;
    }
    $entries = [];
    foreach (q_all('SELECT * FROM parcel_history WHERE parcel_id = ? ORDER BY id DESC', [$parcelId]) as $h) {
        $entries[] = [
            'id'         => (int) $h['id'],
            'changed_at' => $h['changed_at'],
            'event'      => $h['event'],
            'changes'    => json_decode((string) $h['changes'], true) ?: [],
            'snapshot'   => json_decode((string) $h['snapshot'], true) ?: [],
        ];
    }
    return [
        'parcel' => [
            'id' => (int) $p['id'], 'cadastral_lot_no' => $p['cadastral_lot_no'], 'tax_declarant' => $p['tax_declarant'],
            'parcel_status' => $p['parcel_status'], 'area_ha' => ha($p['area_ha']), 'commodity' => $p['commodity'],
            'barangay' => $p['loc_barangay'],
        ],
        'farmer' => [
            'id' => (int) $p['farmer_id'], 'rsbsa_no' => $p['rsbsa_no'],
            'name' => person_name($p['surname'], $p['first_name'], $p['middle_name'], $p['suffix']),
        ],
        'entries' => $entries,
    ];
}


/* ---------- activity log ---------- */
const ACTIVITY_CATEGORIES = ['Login', 'Farmer', 'Duplicate', 'Report', 'Print', 'Document', 'Account', 'System'];

/**
 * Filters of the Activity Logs page, from the query string: q, category, user, from, to.
 * Returns [where sql, args, readable filter list].
 */
function activity_filters(): array
{
    $where = [];
    $args = [];
    $labels = [];

    $category = clean_str($_GET['category'] ?? '');
    if (in_array($category, ACTIVITY_CATEGORIES, true)) {
        $where[] = 'category = ?';
        $args[] = $category;
        $labels[] = "Category: $category";
    }
    $user = clean_str($_GET['user'] ?? '');
    if ($user !== '') {
        $where[] = 'LOWER(username) = LOWER(?)';
        $args[] = $user;
        $labels[] = "User: $user";
    }
    foreach (['from' => ['>=', ' 00:00:00', 'From'], 'to' => ['<=', ' 23:59:59', 'To']] as $key => [$op, $time, $label]) {
        $v = clean_str($_GET[$key] ?? '');
        if ($v !== '') {
            $d = DateTime::createFromFormat('!Y-m-d', $v);
            if (!$d || $d->format('Y-m-d') !== $v) {
                fail(422, "The $key date is not valid.");
            }
            $where[] = "created_at $op ?";
            $args[] = $v . $time;
            $labels[] = "$label $v";
        }
    }
    $q = clean_str($_GET['q'] ?? '');
    foreach (preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) as $token) {
        $like = like_pattern($token);
        $where[] = "(username LIKE ? ESCAPE '!' OR display_name LIKE ? ESCAPE '!' OR action LIKE ? ESCAPE '!' OR details LIKE ? ESCAPE '!')";
        array_push($args, $like, $like, $like, $like);
    }
    if ($q !== '') {
        $labels[] = "Search: $q";
    }
    if (($from = clean_str($_GET['from'] ?? '')) !== '' && ($to = clean_str($_GET['to'] ?? '')) !== '' && $from > $to) {
        fail(422, 'The end date must be on or after the start date.');
    }
    return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $args, $labels];
}
