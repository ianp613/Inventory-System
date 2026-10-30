<?php
/**
 * POST (JSON) { event, ... }   Records things that happen only in the browser (printing, settings).
 * Only the events listed below are accepted. -> { success }
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_post();
require_auth();
$in = read_json();
$event = clean_str($in['event'] ?? '');

$farmerName = function (int $id): string {
    $f = q_one('SELECT rsbsa_no, surname, first_name, middle_name, suffix FROM farmers WHERE id = ?', [$id]);
    return $f ? person_name($f['surname'], $f['first_name'], $f['middle_name'], $f['suffix']) . ' (' . $f['rsbsa_no'] . ')' : '';
};

switch ($event) {
    case 'print_form':
        $id = (int) ($in['farmer_id'] ?? 0);
        log_activity('Print', $id ? 'Printed registration form' : 'Printed blank registration form', $id ? $farmerName($id) : '');
        break;
    case 'print_history':
        $h = load_parcel_history((int) ($in['parcel_id'] ?? 0));
        log_activity('Print', 'Printed parcel history', $h ? $h['farmer']['name'] . ', lot ' . $h['parcel']['cadastral_lot_no'] : '');
        break;
    case 'print_duplicates':
        log_activity('Print', 'Printed duplicate flags list', clean_str($in['detail'] ?? ''));
        break;
    case 'print_activity':
        log_activity('Print', 'Printed activity log', clean_str($in['detail'] ?? ''));
        break;
    case 'theme':
        log_activity('Account', 'Changed theme', ($in['value'] ?? '') === 'dark' ? 'Dark mode.' : 'Default theme.');
        break;
    case 'auto_logout':
        $on = !empty($in['enabled']);
        log_activity('Account', 'Changed auto-logout', $on ? 'On, after ' . (int) ($in['minutes'] ?? 0) . ' minute(s).' : 'Off.');
        break;
    default:
        fail(400, 'Unknown event.');
}
ok([]);
