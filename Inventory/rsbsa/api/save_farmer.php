<?php
/**
 * POST multipart/form-data
 *   payload                 JSON string (see Pages.register in assets/js/script.js)
 *   tax_declaration_{i}     file for parcel i (required for a new parcel)
 *   supporting_docs_{i}[]   files for parcel i (required when the lot is already registered)
 *   photo                   2x2 picture of the farmer (JPG / PNG; required for a new farmer)
 *   valid_id_file           scan or photo of the valid ID (PDF / JPG / PNG; required for a new farmer)
 * -> { success, message, data: { id, rsbsa_no, flagged } }
 * 422 { errors: { "surname": "...", "parcels.0.area_ha": "..." } } for anything the admin must fix.
 *
 * All business rules from the RSBSA note are enforced here:
 *   - shared-lot total must not exceed the area on the tax declaration
 *   - a lot already registered to another farmer is flagged (needs supporting documents)
 *   - a spouse cannot enroll with the same lot, nor register separately (only applies when Civil Status is Married)
 *   - parcels marked Inactive or Deceased are kept but are not counted in the farmer's total area
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_post();
require_auth();

/* ---------- upload helpers ---------- */
const UPLOAD_TYPES = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];

/** Files uploaded under one field name, as a flat list (empty slots skipped). */
function uploaded_files(string $field): array
{
    if (!isset($_FILES[$field])) {
        return [];
    }
    $f = $_FILES[$field];
    $list = [];
    if (is_array($f['name'])) {
        foreach ($f['name'] as $i => $name) {
            $list[] = ['name' => $name, 'tmp_name' => $f['tmp_name'][$i], 'size' => $f['size'][$i], 'error' => $f['error'][$i]];
        }
    } else {
        $list[] = $f;
    }
    return array_values(array_filter($list, fn($x) => (int) $x['error'] !== UPLOAD_ERR_NO_FILE));
}

/** Returns an error message, or null when the file is fine. */
function upload_problem(array $f, bool $imageOnly = false): ?string
{
    $maxMb = rtrim(rtrim(number_format(cfg('max_upload_bytes') / 1048576, 1), '0'), '.');
    switch ((int) $f['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return "Each file must be $maxMb MB or smaller.";
        default:
            return 'The file could not be uploaded. Please try again.';
    }
    if ((int) $f['size'] > (int) cfg('max_upload_bytes') || (int) $f['size'] <= 0) {
        return "Each file must be $maxMb MB or smaller.";
    }
    if (!is_uploaded_file($f['tmp_name'])) {
        return 'The file could not be uploaded. Please try again.';
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset(UPLOAD_TYPES[$mime]) || ($imageOnly && $mime === 'application/pdf')) {
        return $imageOnly ? 'Only JPG or PNG pictures are allowed.' : 'Only PDF, JPG or PNG files are allowed.';
    }
    return null;
}

/** Move a checked upload into storage. Returns [relative path, display name]. */
function store_upload(array $f, string $sub, array &$created): array
{
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $rel = $sub . '/' . bin2hex(random_bytes(16)) . '.' . UPLOAD_TYPES[$mime];
    $abs = storage_dir($sub) . '/' . basename($rel);
    if (!move_uploaded_file($f['tmp_name'], $abs)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }
    $created[] = $abs;
    $name = clean_str(basename(str_replace('\\', '/', (string) $f['name'])));
    return [$rel, mb_substr($name !== '' ? $name : 'document.' . UPLOAD_TYPES[$mime], 0, 150)];
}

/* ---------- read the request ---------- */
if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    fail(413, 'The upload is too large. Attach smaller files.');
}
$in = json_decode((string) ($_POST['payload'] ?? ''), true);
if (!is_array($in)) {
    fail(400, 'Invalid request.');
}

$errors = [];

/** Clean a text value, check length / required, and return it. */
$text = function ($value, string $key, int $max, bool $required = false, string $label = 'This field') use (&$errors): string {
    $s = clean_str($value);
    if ($required && $s === '') {
        $errors[$key] = "$label is required.";
    } elseif (mb_strlen($s) > $max) {
        $errors[$key] = "$label must be $max characters or fewer.";
    }
    return $s;
};

$farmerId = isset($in['id']) && $in['id'] !== '' ? (int) $in['id'] : null;
$editing = $farmerId !== null;
if ($editing && !q_one('SELECT id FROM farmers WHERE id = ?', [$farmerId])) {
    fail(404, 'Farmer record not found.');
}

/* ---------- farmer: basic information ---------- */
$rsbsaNo   = $text($in['rsbsa_no'] ?? '', 'rsbsa_no', 40, false, 'Registration number');
$surname   = $text($in['surname'] ?? '', 'surname', 60, true, 'Surname');
$firstName = $text($in['first_name'] ?? '', 'first_name', 60, true, 'First name');
$middle    = $text($in['middle_name'] ?? '', 'middle_name', 60);
$suffix    = $text($in['suffix'] ?? '', 'suffix', 10);
$idType    = $text($in['valid_id_type'] ?? '', 'valid_id_type', 60, true, 'Valid ID');
$idNo      = $text($in['valid_id_no'] ?? '', 'valid_id_no', 60, true, 'ID number');

$birth = clean_str($in['birth_date'] ?? '');
$dt = DateTime::createFromFormat('!Y-m-d', $birth);
if ($birth === '') {
    $errors['birth_date'] = 'Date of birth is required.';
} elseif (!$dt || $dt->format('Y-m-d') !== $birth || $dt > new DateTime('today') || (int) $dt->format('Y') < 1900) {
    $errors['birth_date'] = 'Enter a valid date of birth.';
}

$addr = is_array($in['address'] ?? null) ? $in['address'] : [];
$addrZone = $text($addr['zone_sitio'] ?? '', 'address.zone_sitio', 80);
$addrBrgy = $text($addr['barangay'] ?? '', 'address.barangay', 80, true, 'Barangay');
$addrMun  = $text($addr['municipality'] ?? '', 'address.municipality', 80, true, 'Municipality');
$addrProv = $text($addr['province'] ?? '', 'address.province', 80, true, 'Province');

/* ---------- farmer: family background ---------- */
$spouseIn = is_array($in['spouse'] ?? null) ? $in['spouse'] : null;
$spSurname = $spFirst = $spMiddle = '';
if ($spouseIn !== null) {
    $spSurname = $text($spouseIn['surname'] ?? '', 'spouse.surname', 60);
    $spFirst   = $text($spouseIn['first_name'] ?? '', 'spouse.first_name', 60);
    $spMiddle  = $text($spouseIn['middle_name'] ?? '', 'spouse.middle_name', 60);
    if ($spSurname . $spFirst . $spMiddle !== '') {
        if ($spSurname === '') { $errors['spouse.surname'] = 'Spouse surname is required.'; }
        if ($spFirst === '')   { $errors['spouse.first_name'] = 'Spouse first name is required.'; }
    }
}
$civil = clean_str($in['civil_status'] ?? '');
if (!in_array($civil, CIVIL_STATUSES, true)) {
    $errors['civil_status'] = 'Select the civil status.';
}
if ($civil === 'Single') {
    $spSurname = $spFirst = $spMiddle = '';   // no spouse to record
}
// the spouse rules below do not apply once the marriage has ended (widowed, separated, annulled)
$separated = in_array($civil, ['Widowed', 'Separated', 'Annulled'], true);
$married   = !empty($in['is_married_woman']);
$maiden    = $married ? $text($in['maiden_name'] ?? '', 'maiden_name', 120, true, 'Mother maiden name') : '';

$children = [];
foreach (array_values(is_array($in['children'] ?? null) ? $in['children'] : []) as $i => $c) {
    $name = $text($c, "children.$i", 120, true, 'Child name');
    if ($name !== '') {
        $children[] = $name;
    }
}

/* ---------- 2x2 picture and valid ID (files) ---------- */
$existingFarmer = $editing ? q_one('SELECT photo_path, valid_id_path FROM farmers WHERE id = ?', [$farmerId]) : null;
$photoUp = $idUp = null;
foreach ([['photo', 'photo', true, 'Attach the 2x2 picture.'], ['valid_id_file', 'valid_id_file', false, 'Attach the valid ID.']] as [$field, $key, $imageOnly, $missing]) {
    $up = uploaded_files($field);
    if ($up) {
        $problem = upload_problem($up[0], $imageOnly);
        if ($problem) {
            $errors[$key] = $problem;
        } elseif ($field === 'photo') {
            $photoUp = $up[0];
        } else {
            $idUp = $up[0];
        }
    } elseif (!$editing) {
        $errors[$key] = $missing;
    }
}

/* ---------- unique RSBSA number ---------- */
if ($rsbsaNo !== '' && !isset($errors['rsbsa_no'])) {
    $dupe = q_val('SELECT id FROM farmers WHERE LOWER(rsbsa_no) = LOWER(?) AND id <> ?', [$rsbsaNo, $farmerId ?? 0]);
    if ($dupe) {
        $errors['rsbsa_no'] = 'This registration number is already used by another farmer.';
    }
}

/* ---------- spouse rule: a spouse who is already registered must be updated, not re-registered ---------- */
if (!$editing && !$separated && $surname !== '' && $firstName !== '') {
    $related = registered_spouses(null, $surname, $firstName, $spSurname, $spFirst);
    foreach ($related as $r) {
        if (empty($r['spouse_separated'])) {
            $errors[$spSurname !== '' ? 'spouse.surname' : 'surname'] =
                'Your spouse is already registered (RSBSA No. ' . ($r['rsbsa_no'] ?: '#' . $r['id']) . '). '
                . 'Add this parcel to the spouse\'s record using the update form, unless you are separated.';
            break;
        }
    }
}

/* ---------- parcels ---------- */
$parcelsIn = array_values(is_array($in['parcels'] ?? null) ? $in['parcels'] : []);
if (!$parcelsIn) {
    fail(422, 'Add at least one parcel.', ['parcels' => 'Add at least one parcel.']);
}
if (count($parcelsIn) > 50) {
    fail(422, 'A farmer can have at most 50 parcels.', ['parcels' => 'Too many parcels.']);
}

$existingParcels = [];
if ($editing) {
    foreach (q_all('SELECT * FROM parcels WHERE farmer_id = ?', [$farmerId]) as $r) {
        $existingParcels[(int) $r['id']] = $r;
    }
}

$applicant = ['surname' => $surname, 'first_name' => $firstName, 'spouse_surname' => $spSurname, 'spouse_first_name' => $spFirst];
$P = [];

foreach ($parcelsIn as $i => $p) {
    $p = is_array($p) ? $p : [];
    $k = "parcels.$i";

    $pid = isset($p['id']) && $p['id'] !== '' && $p['id'] !== null ? (int) $p['id'] : null;
    if ($pid !== null && !isset($existingParcels[$pid])) {
        fail(400, 'A parcel in this request does not belong to this farmer.');
    }

    $n = ['id' => $pid];
    $n['lot']        = $text($p['cadastral_lot_no'] ?? '', "$k.cadastral_lot_no", 40, true, 'Cadastral lot number');
    $loc             = is_array($p['location'] ?? null) ? $p['location'] : [];
    $n['loc_zone']   = $text($loc['zone_sitio'] ?? '', "$k.location.zone_sitio", 80);
    $n['loc_brgy']   = $text($loc['barangay'] ?? '', "$k.location.barangay", 80, true, 'Barangay');
    $n['loc_mun']    = $text($loc['municipality'] ?? '', "$k.location.municipality", 80, true, 'Municipality');
    $n['loc_prov']   = $text($loc['province'] ?? '', "$k.location.province", 80, true, 'Province');
    $n['tax_decl']   = $text($p['tax_declarant'] ?? '', "$k.tax_declarant", 120, true, 'Tax declarant');
    $n['assoc']      = $text($p['association'] ?? '', "$k.association", 120);
    $n['status']     = clean_str($p['parcel_status'] ?? '') ?: 'Active';
    if (!in_array($n['status'], PARCEL_STATUSES, true)) {
        $errors["$k.parcel_status"] = 'Select Active, Inactive or Deceased.';
    }

    $n['commodity'] = clean_str($p['commodity'] ?? '');
    if (!in_array($n['commodity'], COMMODITIES, true)) {
        $errors["$k.commodity"] = 'Select a sectoral commodity.';
    }
    $n['role'] = clean_str($p['farming_role'] ?? '');
    if (!in_array($n['role'], FARMING_ROLES, true)) {
        $errors["$k.farming_role"] = 'Select the farming status.';
    }

    // areas
    foreach (['area_ha' => 'area', 'tax_declared_area_ha' => 'declared', 'cultivated_area_ha' => 'cultivated'] as $field => $short) {
        $raw = $p[$field] ?? null;
        $n[$short] = is_numeric($raw) ? ha($raw) : null;
        if ($n[$short] === null) {
            $errors["$k.$field"] = 'Enter a number.';
        } elseif ($n[$short] < 0 || $n[$short] > 99999999) {
            $errors["$k.$field"] = 'Enter a valid area.';
        }
    }
    if ($n['area'] !== null && $n['area'] <= 0 && !isset($errors["$k.area_ha"])) {
        $errors["$k.area_ha"] = 'Enter an area greater than 0.';
    }
    if ($n['declared'] !== null && $n['declared'] <= 0 && !isset($errors["$k.tax_declared_area_ha"])) {
        $errors["$k.tax_declared_area_ha"] = 'Enter the area shown on the tax declaration.';
    }
    if (!isset($errors["$k.area_ha"]) && !isset($errors["$k.tax_declared_area_ha"]) && $n['area'] > $n['declared'] + AREA_EPSILON) {
        $errors["$k.area_ha"] = 'Area cannot exceed the tax declaration area (' . number_format($n['declared'], 2) . ' ha).';
    }
    if ($n['cultivated'] !== null && $n['area'] !== null && !isset($errors["$k.cultivated_area_ha"]) && $n['cultivated'] > $n['area'] + AREA_EPSILON) {
        $errors["$k.cultivated_area_ha"] = 'Cultivated area cannot exceed the parcel area.';
    }

    // commodity specifics
    $n['crops'] = null;
    $n['animals'] = null;
    $n['fish'] = null;
    if (in_array($n['commodity'], CROP_COMMODITIES, true)) {
        $in_crops = is_array($p['crop_areas'] ?? null) ? $p['crop_areas'] : [];
        $n['crops'] = [];
        foreach (CROP_KEYS as $key) {
            $v = $in_crops[$key] ?? 0;
            if ($v === '' || $v === null) {
                $v = 0;
            }
            if (!is_numeric($v) || (float) $v < 0) {
                $errors["$k.crop_$key"] = 'Must be 0 or more.';
                $v = 0;
            }
            $n['crops'][$key] = ha($v);
        }
    } elseif ($n['commodity'] === 'Fishery') {
        $n['fish'] = $text($p['fish_size'] ?? '', "$k.fish_size", 60, true, 'Size of fish cage / pond');
    } elseif ($n['commodity'] === 'Livestock') {
        $n['animals'] = [];
        $any = false;
        foreach (array_values(is_array($p['livestock'] ?? null) ? $p['livestock'] : []) as $a) {
            $type = clean_str($a['type'] ?? '');
            $heads = $a['heads'] ?? null;
            if (!in_array($type, ANIMAL_TYPES, true) || !is_numeric($heads) || (float) $heads < 0 || floor((float) $heads) != (float) $heads) {
                $errors["$k.livestock"] = 'Enter a valid animal type and a whole number of heads.';
                continue;
            }
            $n['animals'][] = ['type' => $type, 'heads' => (int) $heads];
            $any = $any || (int) $heads > 0;
        }
        if (!$any && !isset($errors["$k.livestock"])) {
            $errors["$k.livestock"] = 'Enter the number of heads.';
        }
    }

    // tax declaration file
    $n['tax_file'] = null;
    $uploads = uploaded_files('tax_declaration_' . $i);
    if ($uploads) {
        $problem = upload_problem($uploads[0]);
        if ($problem) {
            $errors["$k.tax_file"] = $problem;
        } else {
            $n['tax_file'] = $uploads[0];
        }
    } elseif ($pid === null || empty($existingParcels[$pid]['tax_file_path'])) {
        $errors["$k.tax_file"] = 'Attach the tax declaration.';
    }

    // supporting documents (checked now, only required later if the lot turns out to be a duplicate)
    $n['support'] = [];
    foreach (uploaded_files('supporting_docs_' . $i) as $f) {
        $problem = upload_problem($f);
        if ($problem) {
            $errors["$k.support_docs"] = $problem;
        } else {
            $n['support'][] = $f;
        }
    }

    $P[$i] = $n;
}

/* ---------- lot rules: shared area, duplicates, spouse ---------- */
$groups = [];
foreach ($P as $i => &$n) {
    $k = "parcels.$i";
    $n['holders'] = [];   // every OTHER farmer using this lot (for the shared-area total)
    $n['earlier'] = [];   // those who registered it BEFORE this parcel (these make it a duplicate)
    $n['spouse_conflict'] = false;
    if ($n['lot'] === '' || $n['loc_brgy'] === '' || $n['area'] === null || $n['declared'] === null) {
        continue;
    }

    $n['holders'] = lot_holders($n['lot'], $n['loc_brgy'], $farmerId);

    // A parcel is only a duplicate of farmers who registered the lot before it. The first registrant
    // stays clean when others register the same lot later, unless the lot number itself was changed.
    $old = $n['id'] ? $existingParcels[$n['id']] : null;
    $lotChanged = $old === null
        || mb_strtolower($old['cadastral_lot_no']) !== mb_strtolower($n['lot'])
        || mb_strtolower($old['loc_barangay']) !== mb_strtolower($n['loc_brgy']);
    $n['earlier'] = $lotChanged
        ? $n['holders']
        : array_values(array_filter($n['holders'], fn($h) => (int) $h['parcel_id'] < (int) $n['id']));
    $key = mb_strtolower($n['lot']) . '|' . mb_strtolower($n['loc_brgy']);

    if (!isset($groups[$key])) {
        $used = 0.0;
        foreach ($n['holders'] as $h) {
            $used += (float) $h['area_ha'];
        }
        $groups[$key] = [
            'declared' => $n['holders'] ? ha($n['holders'][0]['tax_declared_area_ha']) : $n['declared'],
            'sum'      => $used,
        ];
    }
    $g = &$groups[$key];

    if (abs($n['declared'] - $g['declared']) > AREA_EPSILON) {
        $errors["$k.tax_declared_area_ha"] = 'The tax declaration area for this lot is ' . number_format($g['declared'], 2) . ' ha.';
    }
    $before = $g['sum'];
    $g['sum'] += $n['area'];
    if ($g['sum'] > $g['declared'] + AREA_EPSILON && !isset($errors["$k.area_ha"])) {
        $errors["$k.area_ha"] = 'Only ' . number_format(max(0, $g['declared'] - $before), 2) . ' ha remain on the tax declaration for this lot.';
    }
    unset($g);

    if ($n['earlier']) {
        $spouse = spouse_holder($n['earlier'], $applicant);
        if ($spouse) {
            $n['spouse_conflict'] = true;
            $errors["$k.cadastral_lot_no"] = 'Your spouse is already registered with this lot. Use the update form of the registered spouse instead.';
        } else {
            $hasFlag = $n['id'] && (int) q_val('SELECT COUNT(*) FROM duplicate_flags WHERE parcel_id = ?', [$n['id']]) > 0;
            if (!$hasFlag && !$n['support'] && !isset($errors["$k.support_docs"])) {
                $errors["$k.support_docs"] = 'This lot is already registered to another farmer. Attach the supporting documents.';
            }
        }
    }
}
unset($n);

if ($errors) {
    fail(422, 'Please correct the highlighted fields.', $errors);
}

/* ---------- save everything in one transaction ---------- */
$pdo = db();
$created = [];     // files written during this request (removed again if saving fails)
$obsolete = [];    // old files to delete after a successful save
$flagged = false;
$now = now();

$notes = [];       // what changed, for the activity log
$pdo->beginTransaction();
try {
    $farmerData = [
        'surname'            => $surname,
        'first_name'         => $firstName,
        'middle_name'        => $middle,
        'suffix'             => $suffix,
        'birth_date'         => $birth,
        'valid_id_type'      => $idType,
        'valid_id_no'        => $idNo,
        'addr_zone_sitio'    => $addrZone,
        'addr_barangay'      => $addrBrgy,
        'addr_municipality'  => $addrMun,
        'addr_province'      => $addrProv,
        'spouse_surname'     => $spSurname,
        'spouse_first_name'  => $spFirst,
        'spouse_middle_name' => $spMiddle,
        'spouse_separated'   => $separated ? 1 : 0,
        'is_married_woman'   => $married ? 1 : 0,
        'maiden_name'        => $maiden,
        'civil_status'       => $civil,
        'updated_at'         => $now,
    ];
    foreach ([['photo', $photoUp, 'photos'], ['valid_id', $idUp, 'valid_ids']] as [$col, $up, $sub]) {
        if ($up) {
            [$farmerData[$col . '_path'], $farmerData[$col . '_name']] = store_upload($up, $sub, $created);
            if (!empty($existingFarmer[$col . '_path'])) {
                $obsolete[] = $existingFarmer[$col . '_path'];
            }
        }
    }

    if ($editing) {
        if ($rsbsaNo !== '') {
            $farmerData['rsbsa_no'] = $rsbsaNo;
        }
        db_update('farmers', $farmerData, $farmerId);
    } else {
        $farmerData['rsbsa_no'] = $rsbsaNo !== '' ? $rsbsaNo : null;
        $farmerData['created_at'] = $now;
        $farmerId = db_insert('farmers', $farmerData);
    }

    // auto-generate the registration number when none was given
    $currentNo = q_val('SELECT rsbsa_no FROM farmers WHERE id = ?', [$farmerId]);
    if ($currentNo === null || $currentNo === '') {
        $currentNo = sprintf('%s-%s-%06d', cfg('rsbsa_prefix', 'RSBSA'), date('Y'), $farmerId);
        q('UPDATE farmers SET rsbsa_no = ? WHERE id = ?', [$currentNo, $farmerId]);
    }

    // children
    q('DELETE FROM children WHERE farmer_id = ?', [$farmerId]);
    foreach ($children as $name) {
        db_insert('children', ['farmer_id' => $farmerId, 'name' => $name]);
    }

    // parcels
    $keptIds = [];
    foreach ($P as $i => $n) {
        $data = [
            'cadastral_lot_no'     => $n['lot'],
            'area_ha'              => $n['area'],
            'tax_declared_area_ha' => $n['declared'],
            'cultivated_area_ha'   => $n['cultivated'],
            'loc_zone_sitio'       => $n['loc_zone'],
            'loc_barangay'         => $n['loc_brgy'],
            'loc_municipality'     => $n['loc_mun'],
            'loc_province'         => $n['loc_prov'],
            'commodity'            => $n['commodity'],
            'fish_size'            => $n['fish'],
            'farming_role'         => $n['role'],
            'tax_declarant'        => $n['tax_decl'],
            'association'          => $n['assoc'],
            'parcel_status'        => $n['status'],
            'updated_at'           => $now,
        ];

        if ($n['tax_file']) {
            [$data['tax_file_path'], $data['tax_file_name']] = store_upload($n['tax_file'], 'tax_declarations', $created);
            if ($n['id'] && !empty($existingParcels[$n['id']]['tax_file_path'])) {
                $obsolete[] = $existingParcels[$n['id']]['tax_file_path'];
            }
        }

        if ($n['id']) {
            $parcelId = $n['id'];
            $old = $existingParcels[$parcelId];
            db_update('parcels', $data, $parcelId);

            // history: record what this save changed
            $changes = history_changes($old, $data);
            if (isset($data['tax_file_name'])) {
                $changes[] = ['field' => 'tax_file', 'label' => 'Tax Declaration File', 'old' => (string) $old['tax_file_name'], 'new' => $data['tax_file_name']];
            }
            if ($changes) {
                record_history($parcelId, $farmerId, 'Updated', $changes, array_merge($old, $data), $now);
                $notes[] = 'Lot ' . $data['cadastral_lot_no'] . ' changed: ' . implode(', ', array_column($changes, 'label')) . '.';
            }
        } else {
            $data['farmer_id'] = $farmerId;
            $data['duplicate_flag'] = 0;
            $data['created_at'] = $now;
            $parcelId = db_insert('parcels', $data);
            record_history($parcelId, $farmerId, 'Registered', [], $data, $now);
            $notes[] = 'Added parcel, lot ' . $data['cadastral_lot_no'] . '.';
        }
        $keptIds[] = $parcelId;

        // crop areas / livestock
        q('DELETE FROM crop_areas WHERE parcel_id = ?', [$parcelId]);
        q('DELETE FROM parcel_livestock WHERE parcel_id = ?', [$parcelId]);
        foreach ($n['crops'] ?? [] as $key => $area) {
            q('INSERT INTO crop_areas (parcel_id, crop_key, area_ha) VALUES (?, ?, ?)', [$parcelId, $key, $area]);
        }
        foreach ($n['animals'] ?? [] as $a) {
            q('INSERT INTO parcel_livestock (parcel_id, animal_type, heads) VALUES (?, ?, ?)', [$parcelId, $a['type'], $a['heads']]);
        }

        // duplicate flag
        $flag = q_one('SELECT id, status FROM duplicate_flags WHERE parcel_id = ? ORDER BY id DESC', [$parcelId]);
        if ($n['earlier']) {
            if (!$flag) {
                $flagId = db_insert('duplicate_flags', [
                    'parcel_id'            => $parcelId,
                    'farmer_id'            => $farmerId,
                    'registered_farmer_id' => (int) $n['earlier'][0]['farmer_id'],
                    'lot_no'               => $n['lot'],
                    'barangay'             => $n['loc_brgy'],
                    'area_ha'              => $n['area'],
                    'tax_declared_area_ha' => $n['declared'],
                    'status'               => 'open',
                    'remarks'              => null,
                    'flagged_at'           => $now,
                ]);
                $flagged = true;
            } else {
                $flagId = (int) $flag['id'];
                q(
                    'UPDATE duplicate_flags SET lot_no = ?, barangay = ?, registered_farmer_id = ?, area_ha = ?, tax_declared_area_ha = ? WHERE id = ?',
                    [$n['lot'], $n['loc_brgy'], (int) $n['earlier'][0]['farmer_id'], $n['area'], $n['declared'], $flagId]
                );
            }
            foreach ($n['support'] as $f) {
                [$rel, $name] = store_upload($f, 'supporting_docs', $created);
                db_insert('duplicate_documents', ['flag_id' => $flagId, 'file_path' => $rel, 'file_name' => $name, 'created_at' => $now]);
            }
            q('UPDATE parcels SET duplicate_flag = 1 WHERE id = ?', [$parcelId]);
        } else {
            // the lot is no longer shared with another farmer
            if ($flag && $flag['status'] === 'open') {
                q(
                    "UPDATE duplicate_flags SET status = 'resolved', resolved_at = ?,
                            remarks = CASE WHEN remarks IS NULL OR remarks = '' THEN ? ELSE remarks END
                      WHERE id = ?",
                    [$now, 'Auto-resolved: the lot is no longer shared with another farmer.', $flag['id']]
                );
            }
            q('UPDATE parcels SET duplicate_flag = 0 WHERE id = ?', [$parcelId]);
        }
    }

    // parcels the admin removed from the form: delete the parcel, its duplicate flags and every attached file
    foreach ($existingParcels as $pid => $row) {
        if (!in_array($pid, $keptIds, true)) {
            $notes[] = 'Removed parcel, lot ' . $row['cadastral_lot_no'] . '.';
            if ($row['tax_file_path']) {
                $obsolete[] = $row['tax_file_path'];
            }
            $flagIds = array_map('intval', array_column(q_all('SELECT id FROM duplicate_flags WHERE parcel_id = ?', [$pid]), 'id'));
            array_push($obsolete, ...flag_document_paths($flagIds));
            delete_flags($flagIds);
            q('DELETE FROM parcels WHERE id = ?', [$pid]);
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    foreach ($created as $file) {
        @unlink($file);
    }
    throw $e;
}

foreach ($obsolete as $rel) {
    delete_stored_file($rel);
}

$who = person_name($surname, $firstName, $middle, $suffix) . ' (' . $currentNo . ')';
if ($photoUp) { $notes[] = '2x2 picture ' . ($editing ? 'replaced' : 'added') . '.'; }
if ($idUp) { $notes[] = 'Valid ID file ' . ($editing ? 'replaced' : 'added') . '.'; }
if ($flagged) { $notes[] = 'Flagged as duplicate.'; }
$wasUpdate = $editing && !empty($in['id']);
log_activity('Farmer', $wasUpdate ? 'Updated farmer record' : 'Registered farmer', $who . '. ' . ($wasUpdate ? implode(' ', $notes) : count($P) . ' parcel(s). ' . ($flagged ? 'Flagged as duplicate.' : '')));

$message = $editing && !empty($in['id']) ? 'Farmer record updated.' : 'Farmer record saved.';
if ($flagged) {
    $message .= ' One or more parcels were flagged as duplicates for review.';
}

ok(['id' => $farmerId, 'rsbsa_no' => $currentNo, 'flagged' => $flagged], $message);
