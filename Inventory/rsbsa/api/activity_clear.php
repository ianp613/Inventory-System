<?php
/**
 * POST (JSON) { period: "24h"|"7d"|"30d"|"1y"|"all", mode: "within"|"older", password, preview? }
 *   preview: true  -> { count }                       only counts the entries (no password needed)
 *   otherwise      -> deletes them after checking the administrator's password; { deleted }
 * mode "within" = entries made in the last <period>; "older" = entries made before that. "all" ignores the mode.
 * The deletion itself is written to the log afterwards, so clearing the log always leaves a trace.
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_post();
$admin = require_auth();
$in = read_json();

$periods = ['24h' => ['-24 hours', 'the last 24 hours'], '7d' => ['-7 days', 'the last 7 days'], '30d' => ['-30 days', 'the last 30 days'],
            '1y' => ['-1 year', 'the last year'], 'all' => [null, 'all time']];
$period = clean_str($in['period'] ?? '');
$mode = clean_str($in['mode'] ?? 'within');
if (!isset($periods[$period])) {
    fail(422, 'Choose which log entries to delete.', ['period' => 'Choose a period.']);
}
if (!in_array($mode, ['within', 'older'], true)) {
    fail(422, 'Invalid selection.', ['mode' => 'Invalid selection.']);
}

$where = '';
$args = [];
if ($period !== 'all') {
    $cutoff = date('Y-m-d H:i:s', strtotime($periods[$period][0]));
    $where = ' WHERE created_at ' . ($mode === 'within' ? '>=' : '<') . ' ?';
    $args[] = $cutoff;
}
$what = $period === 'all' ? 'all entries' : ($mode === 'within' ? 'entries from ' : 'entries older than ') . $periods[$period][1];

$count = (int) q_val('SELECT COUNT(*) FROM activity_log' . $where, $args);
if (!empty($in['preview'])) {
    ok(['count' => $count, 'description' => $what]);
}

$password = is_string($in['password'] ?? null) ? $in['password'] : '';
$row = q_one('SELECT password_hash FROM admins WHERE id = ?', [$admin['id']]);
if ($password === '' || !$row || !password_verify($password, $row['password_hash'])) {
    log_activity('Account', 'Wrong password when clearing the activity log', 'Tried to delete ' . $what . '.');
    fail(422, 'Your password is not correct.', ['password' => 'Your password is not correct.']);
}

q('DELETE FROM activity_log' . $where, $args);
log_activity('System', 'Cleared activity log', 'Deleted ' . $count . ' ' . ($count === 1 ? 'entry' : 'entries') . ' (' . $what . ').');
ok(['deleted' => $count], $count === 1 ? '1 log entry was deleted.' : $count . ' log entries were deleted.');
