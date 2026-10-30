<?php
/**
 * POST (JSON) { id, status: "open" | "resolved", remarks }
 * -> { success, message }       422 { errors: { remarks } } when resolving without remarks
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_post();
require_auth();
$in = read_json();

$id = (int) ($in['id'] ?? 0);
$status = clean_str($in['status'] ?? '');
$remarks = trim(str_replace("\r\n", "\n", is_string($in['remarks'] ?? null) ? $in['remarks'] : ''));

$flag = $id > 0 ? q_one('SELECT * FROM duplicate_flags WHERE id = ?', [$id]) : null;
if (!$flag) {
    fail(404, 'Duplicate flag not found.');
}
if (!in_array($status, ['open', 'resolved'], true)) {
    fail(422, 'Invalid status.', ['status' => 'Invalid status.']);
}
if (mb_strlen($remarks) > 1000) {
    fail(422, 'Remarks are too long.', ['remarks' => 'Remarks must be 1000 characters or fewer.']);
}
if ($status === 'resolved' && $remarks === '') {
    fail(422, 'Enter your remarks before resolving this flag.', ['remarks' => 'Enter your remarks before resolving this flag.']);
}

$resolvedAt = $status === 'resolved' ? ($flag['status'] === 'resolved' && $flag['resolved_at'] ? $flag['resolved_at'] : now()) : null;
q('UPDATE duplicate_flags SET status = ?, remarks = ?, resolved_at = ? WHERE id = ?', [$status, $remarks, $resolvedAt, $id]);

if ($flag['status'] !== $status) {
    $message = $status === 'resolved' ? 'Flag marked as resolved.' : 'Flag reopened.';
} else {
    $message = 'Remarks saved.';
}

$who = person_name(...array_values(q_one('SELECT surname, first_name, middle_name, suffix FROM farmers WHERE id = ?', [$flag['farmer_id']]) ?: ['', '', '', '']));
$act = $flag['status'] !== $status ? ($status === 'resolved' ? 'Resolved duplicate flag' : 'Reopened duplicate flag') : 'Saved duplicate flag remarks';
log_activity('Duplicate', $act, 'Lot ' . $flag['lot_no'] . ($flag['barangay'] !== '' ? ', ' . $flag['barangay'] : '') . ' (' . trim($who) . ').' . ($remarks !== '' ? ' Remarks: ' . mb_substr($remarks, 0, 300) : ''));

ok([], $message);
