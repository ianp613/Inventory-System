<?php
/**
 * GET ?q=&category=&user=&from=YYYY-MM-DD&to=YYYY-MM-DD&page=1&per_page=25
 * -> { data: { rows: [ { id, created_at, username, name, category, action, details, ip } ], total, page, per_page,
 *              categories: [...], users: [ { username, name } ] } }
 * The log can only be read here; the system has no way to change or delete it.
 */
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_lib.php';

require_get();
require_auth();

[$page, $perPage] = page_params();
[$whereSql, $args] = activity_filters();

$total = (int) q_val("SELECT COUNT(*) FROM activity_log $whereSql", $args);
$rows = q_all(
    "SELECT * FROM activity_log $whereSql ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?",
    [...$args, $perPage, ($page - 1) * $perPage]
);

// users that appear in the log (for the filter), with the most recent display name
$users = array_map(
    fn($u) => ['username' => $u['username'], 'name' => $u['display_name']],
    q_all("SELECT username, MAX(display_name) AS display_name FROM activity_log WHERE username <> '' GROUP BY username ORDER BY username")
);

ok([
    'rows' => array_map(fn($r) => [
        'id'         => (int) $r['id'],
        'created_at' => $r['created_at'],
        'username'   => $r['username'],
        'name'       => $r['display_name'],
        'category'   => $r['category'],
        'action'     => $r['action'],
        'details'    => $r['details'],
        'ip'         => $r['ip_address'],
    ], $rows),
    'total'      => $total,
    'page'       => $page,
    'per_page'   => $perPage,
    'categories' => ACTIVITY_CATEGORIES,
    'users'      => $users,
]);
