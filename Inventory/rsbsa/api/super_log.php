<?php
/**
 * GET ?page=1&per_page=20 -> { success, data: { total, page, per_page, entries: [ { id, created_at, action, target, details, ip_address } ] } }
 * Super user only. Newest first. The log cannot be edited or cleared from the system.
 */
require __DIR__ . '/_bootstrap.php';

require_get();
require_super();
ensure_super_user();

$perPage = max(5, min(100, (int) ($_GET['per_page'] ?? 20)));
$total = (int) q_val('SELECT COUNT(*) FROM super_log');
$pages = max(1, (int) ceil($total / $perPage));
$page = max(1, min($pages, (int) ($_GET['page'] ?? 1)));
$offset = ($page - 1) * $perPage;

ok([
    'total'    => $total,
    'page'     => $page,
    'pages'    => $pages,
    'per_page' => $perPage,
    'entries'  => q_all("SELECT id, created_at, action, target, details, ip_address FROM super_log ORDER BY id DESC LIMIT $perPage OFFSET $offset"),
]);
