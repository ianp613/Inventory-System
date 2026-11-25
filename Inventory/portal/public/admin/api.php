<?php
declare(strict_types=1);
require __DIR__ . '/../../app/lib.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
admin_session_start();

$action = (string)($_GET['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'];
$in = [];
if ($method === 'POST') {
    $in = str_contains((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json')
        ? (json_decode((string)file_get_contents('php://input'), true) ?: [])
        : $_POST;
}

try {
    /* ---------- public actions ---------- */
    if ($action === 'login') {
        $u = one('SELECT * FROM admins WHERE username=?', [trim((string)($in['username'] ?? ''))]);
        if (!$u || !password_verify((string)($in['password'] ?? ''), $u['password_hash'])) {
            usleep(800000);
            json_out(['error' => 'Wrong username or password'], 401);
        }
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$u['id'];
        $_SESSION['admin_user'] = $u['username'];
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
        json_out(['ok' => true]);
    }
    if (!admin_logged_in()) {
        json_out(['error' => 'Unauthorized'], 401);
    }
    if ($method === 'POST') {
        $tok = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf'], $tok)) {
            json_out(['error' => 'Invalid CSRF token. Reload the page.'], 403);
        }
    }

    switch ($action) {
        case 'logout':
            $_SESSION = [];
            session_destroy();
            json_out(['ok' => true]);

        case 'me':
            $u = one('SELECT * FROM admins WHERE id=?', [$_SESSION['admin_id']]);
            json_out(['username' => $u['username'], 'default_password' => password_verify(DEFAULT_ADMIN_PASS, $u['password_hash'])]);

        case 'change_password':
            $u = one('SELECT * FROM admins WHERE id=?', [$_SESSION['admin_id']]);
            if (!password_verify((string)($in['current'] ?? ''), $u['password_hash'])) {
                json_out(['error' => 'Current password is wrong'], 422);
            }
            $new = (string)($in['new'] ?? '');
            if (strlen($new) < 8) json_out(['error' => 'New password must be at least 8 characters'], 422);
            $newUser = trim((string)($in['username'] ?? $u['username']));
            if ($newUser === '') $newUser = $u['username'];
            q('UPDATE admins SET password_hash=?, username=? WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), $newUser, $u['id']]);
            $_SESSION['admin_user'] = $newUser;
            json_out(['ok' => true]);

        /* ================= dashboard ================= */
        case 'dashboard':
            expire_sessions();
            $now = time();
            $cd = cooldown_seconds();
            $vr = voucher_rows();
            $vc = ['total' => count($vr), 'unused' => 0, 'in_use' => 0, 'partial' => 0, 'used' => 0];
            foreach ($vr as $v) $vc[$v['status']]++;

            $cooldown = 0;
            if ($cd > 0) {
                $cooldown = (int)val("SELECT COUNT(*) FROM sessions s WHERE s.id=(SELECT MAX(id) FROM sessions WHERE mac=s.mac)
                    AND s.status!='active' AND s.cooldown_reset=0 AND COALESCE(s.ended_at,s.expires_at)+? > ?", [$cd, $now]);
            }
            $today0 = strtotime('today');
            $days = [];
            for ($i = 13; $i >= 0; $i--) $days[date('Y-m-d', strtotime("-$i day", $today0))] = 0;
            foreach (all('SELECT started_at FROM sessions WHERE started_at>=?', [$today0 - 13 * 86400]) as $r) {
                $k = date('Y-m-d', (int)$r['started_at']);
                if (isset($days[$k])) $days[$k]++;
            }
            $nets = [];
            foreach (networks() as $n) {
                $nets[] = network_public($n) + [
                    'active_sessions' => (int)val("SELECT COUNT(*) FROM sessions WHERE network_id=? AND status='active' AND expires_at>?", [$n['id'], $now]),
                    'redeemed_24h' => (int)val('SELECT COUNT(*) FROM sessions WHERE network_id=? AND started_at>?', [$n['id'], $now - 86400]),
                ];
            }
            json_out([
                'active_sessions' => (int)val("SELECT COUNT(*) FROM sessions WHERE status='active' AND expires_at>?", [$now]),
                'in_cooldown' => $cooldown,
                'redeemed_today' => (int)val('SELECT COUNT(*) FROM sessions WHERE started_at>=?', [$today0]),
                'redeemed_total' => (int)val('SELECT COUNT(*) FROM sessions'),
                'unique_devices' => (int)val('SELECT COUNT(DISTINCT mac) FROM sessions'),
                'vouchers' => $vc,
                'days' => $days,
                'networks' => $nets,
                'activity' => all('SELECT a.*, n.name AS network_name FROM activity a LEFT JOIN networks n ON n.id=a.network_id ORDER BY a.id DESC LIMIT 15'),
            ]);

        case 'network_status':
            $net = network_by_id((int)($_GET['id'] ?? 0)) ?? json_out(['error' => 'Unknown network'], 404);
            $t0 = microtime(true);
            try {
                $u = unifi_for($net);
                $cl = $u->clients();
                $wireless = array_filter($cl, fn($c) => empty($c['is_wired']));
                $guests = array_filter($wireless, fn($c) => !empty($c['is_guest']));
                json_out(['ok' => true, 'clients' => count($wireless), 'guests' => count($guests),
                    'aps' => count($u->accessPoints()), 'ms' => (int)((microtime(true) - $t0) * 1000)]);
            } catch (Throwable $e) {
                json_out(['ok' => false, 'error' => $e->getMessage()]);
            }

        /* ================= vouchers ================= */
        case 'vouchers':
            $rows = voucher_rows($_GET);
            $per = max(5, min(200, (int)($_GET['per'] ?? 25)));
            $page = max(1, (int)($_GET['page'] ?? 1));
            $total = count($rows);
            $pages = max(1, (int)ceil($total / $per));
            $page = min($page, $pages);
            $slice = array_slice($rows, ($page - 1) * $per, $per);
            json_out([
                'rows' => $slice, 'total' => $total, 'page' => $page, 'pages' => $pages,
                'names' => array_column(all('SELECT DISTINCT name FROM vouchers ORDER BY name COLLATE NOCASE'), 'name'),
            ]);

        case 'voucher_create':
            $name = trim((string)($in['name'] ?? ''));
            $amount = (int)($in['amount'] ?? 1);
            $type = (string)($in['type'] ?? 'single');
            $time = (int)($in['usage_time'] ?? 0);
            $unit = (string)($in['unit'] ?? 'minutes');
            $maxUses = (int)($in['max_uses'] ?? 0);
            $netId = !empty($in['network_id']) ? (int)$in['network_id'] : null;
            if ($name === '' || mb_strlen($name) > 80) json_out(['error' => 'Voucher name is required (max 80 characters)'], 422);
            if ($amount < 1 || $amount > 1000) json_out(['error' => 'Amount must be between 1 and 1000'], 422);
            if (!in_array($type, ['single', 'multiple', 'unlimited'], true)) json_out(['error' => 'Invalid type'], 422);
            $mult = ['minutes' => 1, 'hours' => 60, 'days' => 1440][$unit] ?? null;
            if (!$mult || $time < 1) json_out(['error' => 'Usage time must be 1 or more'], 422);
            $minutes = $time * $mult;
            if ($minutes > 525600) json_out(['error' => 'Usage time cannot exceed 1 year'], 422);
            if ($type === 'multiple' && ($maxUses < 2 || $maxUses > 100000)) json_out(['error' => 'Multiple-use needs a max uses value of 2 or more'], 422);
            if ($netId && !network_by_id($netId)) json_out(['error' => 'Unknown network'], 422);

            $created = [];
            db()->exec('BEGIN IMMEDIATE');
            try {
                $ins = db()->prepare('INSERT INTO vouchers(code,name,type,max_uses,duration_minutes,network_id,created_at) VALUES(?,?,?,?,?,?,?)');
                while (count($created) < $amount) {
                    $code = gen_code();
                    if (val('SELECT 1 FROM vouchers WHERE code=?', [$code])) continue;
                    $ins->execute([$code, $name, $type, $type === 'multiple' ? $maxUses : null, $minutes, $netId, time()]);
                    $created[] = ['id' => (int)db()->lastInsertId(), 'code' => $code, 'code_fmt' => format_code($code)];
                }
                db()->exec('COMMIT');
            } catch (Throwable $e) {
                db()->exec('ROLLBACK');
                throw $e;
            }
            log_activity($netId, null, 'vouchers_created', "$amount x $name ($type, {$minutes}m)");
            json_out(['ok' => true, 'created' => $created, 'name' => $name, 'type' => $type, 'minutes' => $minutes]);

        case 'voucher_delete':
            $ids = array_values(array_filter(array_map('intval', (array)($in['ids'] ?? []))));
            if (!$ids) json_out(['error' => 'Nothing selected'], 422);
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $n = q("DELETE FROM vouchers WHERE id IN ($ph)", $ids)->rowCount();
            log_activity(null, null, 'vouchers_deleted', "$n voucher(s)");
            json_out(['ok' => true, 'deleted' => $n]);

        case 'voucher_detail':
            $id = (int)($_GET['id'] ?? 0);
            $v = one('SELECT * FROM vouchers WHERE id=?', [$id]) ?? json_out(['error' => 'Not found'], 404);
            json_out(['voucher' => $v + ['code_fmt' => format_code($v['code'])],
                'sessions' => all('SELECT s.*, n.name AS network_name FROM sessions s LEFT JOIN networks n ON n.id=s.network_id WHERE s.voucher_id=? ORDER BY s.id DESC', [$id])]);

        case 'vouchers_export':
            $rows = voucher_rows($_GET);
            header_remove('Content-Type');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="vouchers-' . date('Ymd-His') . '.csv"');
            $o = fopen('php://output', 'w');
            fputcsv($o, ['Code', 'Name', 'Type', 'Duration (min)', 'Max uses', 'Uses', 'Status', 'Network', 'Created']);
            foreach ($rows as $r) {
                fputcsv($o, [$r['code_fmt'], $r['name'], $r['type'], $r['duration_minutes'], $r['max_uses'], $r['uses'], $r['status'], $r['network_name'] ?: 'All', date('Y-m-d H:i', (int)$r['created_at'])]);
            }
            exit;

        /* ================= users ================= */
        case 'users':
            $net = network_by_id((int)($_GET['network_id'] ?? 0)) ?? json_out(['error' => 'Unknown network'], 404);
            $now = time();
            $rows = [];
            $err = null;
            $seen = [];
            try {
                $clients = unifi_for($net)->clients();
            } catch (Throwable $e) {
                $clients = [];
                $err = $e->getMessage();
            }
            foreach ($clients as $c) {
                if (!empty($c['is_wired'])) continue;
                $mac = normalize_mac((string)($c['mac'] ?? ''));
                if (!$mac) continue;
                $seen[$mac] = true;
                $st = mac_state($mac);
                $sess = $st['session'] ?? null;
                if ($st['state'] === 'active') {
                    q('UPDATE sessions SET ip=?, hostname=?, ssid=?, ap_mac=?, last_seen=? WHERE id=?',
                        [$c['ip'] ?? null, $c['hostname'] ?? $c['name'] ?? null, $c['essid'] ?? null, $c['ap_mac'] ?? null, $now, $sess['id']]);
                }
                $rows[] = [
                    'network_id' => (int)$net['id'], 'network' => $net['name'], 'online' => true,
                    'name' => $c['name'] ?? $c['hostname'] ?? (!empty($c['is_guest']) ? 'Guest User' : 'Unknown'),
                    'mac' => $mac, 'ssid' => $c['essid'] ?? '', 'satisfaction' => $c['satisfaction'] ?? null,
                    'ip' => $c['ip'] ?? '', 'uptime' => (int)($c['uptime'] ?? 0), 'is_guest' => !empty($c['is_guest']),
                    'authorized' => !empty($c['authorized']), 'signal' => $c['signal'] ?? null,
                    'state' => $st['state'], 'voucher' => $sess ? format_code((string)$sess['voucher_code']) : null,
                    'voucher_name' => $sess['voucher_name'] ?? null,
                    'time_left' => $st['state'] === 'active' ? $st['remaining'] : null,
                    'cooldown_left' => $st['state'] === 'cooldown' ? $st['remaining'] : null,
                    'has_session' => (bool)$sess || (bool)($st['last'] ?? null),
                    'revoked' => $sess && $sess['status'] === 'revoked' && (int)$sess['expires_at'] > $now,
                ];
            }
            // Devices with a running voucher / cooldown that are not connected right now.
            foreach (all('SELECT s.* FROM sessions s WHERE s.network_id=? AND s.id=(SELECT MAX(id) FROM sessions WHERE mac=s.mac)', [$net['id']]) as $s) {
                if (isset($seen[$s['mac']])) continue;
                $st = mac_state($s['mac']);
                if ($st['state'] === 'free') continue;
                $rows[] = [
                    'network_id' => (int)$net['id'], 'network' => $net['name'], 'online' => false,
                    'name' => $s['hostname'] ?: 'Guest User', 'mac' => $s['mac'], 'ssid' => $s['ssid'] ?? '',
                    'satisfaction' => null, 'ip' => $s['ip'] ?? '', 'uptime' => 0, 'is_guest' => true, 'authorized' => false, 'signal' => null,
                    'state' => $st['state'], 'voucher' => format_code((string)$s['voucher_code']), 'voucher_name' => $s['voucher_name'],
                    'time_left' => $st['state'] === 'active' ? $st['remaining'] : null,
                    'cooldown_left' => $st['state'] === 'cooldown' ? $st['remaining'] : null,
                    'has_session' => true, 'revoked' => $s['status'] === 'revoked' && (int)$s['expires_at'] > $now,
                ];
            }
            json_out(['rows' => $rows, 'error' => $err, 'network' => $net['name']]);

        case 'user_unauthorize':
            $net = network_by_id((int)($in['network_id'] ?? 0)) ?? json_out(['error' => 'Unknown network'], 404);
            $mac = normalize_mac((string)($in['mac'] ?? '')) ?: json_out(['error' => 'Bad MAC'], 422);
            $u = unifi_for($net);
            try {
                $u->unauthorize($mac);
                $u->kick($mac);
            } catch (Throwable $e) {
                json_out(['error' => 'Controller error: ' . $e->getMessage()], 502);
            }
            $n = q("UPDATE sessions SET status='revoked', ended_at=?, ended_reason='admin' WHERE mac=? AND status='active'", [time(), $mac])->rowCount();
            log_activity((int)$net['id'], $mac, 'unauthorized', $n ? 'Session ended by admin' : 'Disconnected by admin');
            json_out(['ok' => true]);

        case 'user_reset_cooldown':
            $mac = normalize_mac((string)($in['mac'] ?? '')) ?: json_out(['error' => 'Bad MAC'], 422);
            q('UPDATE sessions SET cooldown_reset=1 WHERE mac=?', [$mac]);
            log_activity(!empty($in['network_id']) ? (int)$in['network_id'] : null, $mac, 'cooldown_reset', 'Cooldown cleared by admin');
            json_out(['ok' => true]);

        case 'user_reconnect':
            $net = network_by_id((int)($in['network_id'] ?? 0)) ?? json_out(['error' => 'Unknown network'], 404);
            $mac = normalize_mac((string)($in['mac'] ?? '')) ?: json_out(['error' => 'Bad MAC'], 422);
            $s = one('SELECT * FROM sessions WHERE mac=? ORDER BY id DESC LIMIT 1', [$mac]);
            $u = unifi_for($net);
            $mins = 0;
            try {
                if ($s && (int)$s['expires_at'] > time()) {
                    // Voucher time is left: (re-)authorize for what remains, restoring an unauthorized session.
                    $mins = (int)ceil(((int)$s['expires_at'] - time()) / 60);
                    $u->authorize($mac, $mins, $s['ap_mac'] ?: null);
                    q("UPDATE sessions SET status='active', ended_at=NULL, ended_reason=NULL, network_id=? WHERE id=?", [$net['id'], $s['id']]);
                    try { $u->kick($mac); } catch (Throwable $e) { /* device may be offline */ }
                } else {
                    $u->kick($mac); // no time left: just force the device to reconnect
                }
            } catch (Throwable $e) {
                json_out(['error' => 'Controller error: ' . $e->getMessage()], 502);
            }
            log_activity((int)$net['id'], $mac, 'reconnected', $mins ? "Re-authorized for {$mins} min" : 'Forced to reconnect');
            json_out(['ok' => true, 'minutes' => $mins]);

        /* ================= settings ================= */
        case 'settings_get':
            json_out(['settings' => settings(true), 'schema' => array_map(fn($d) => ['default' => $d[0], 'type' => $d[1], 'arg' => $d[2]], setting_schema())]);

        case 'settings_save':
            try {
                save_settings((array)($in['values'] ?? []));
            } catch (InvalidArgumentException $e) {
                json_out(['error' => $e->getMessage()], 422);
            }
            json_out(['ok' => true, 'settings' => settings(true)]);

        case 'upload':
            $kind = ($in['kind'] ?? '') === 'bg' ? 'bg' : 'logo';
            $f = $_FILES['file'] ?? null;
            if (!$f || $f['error'] !== UPLOAD_ERR_OK) json_out(['error' => 'Upload failed'], 422);
            if ($f['size'] > 4 * 1024 * 1024) json_out(['error' => 'Image must be 4 MB or smaller'], 422);
            $info = @getimagesize($f['tmp_name']);
            $ext = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'][$info[2] ?? 0] ?? null;
            if (!$ext) json_out(['error' => 'Only PNG, JPG, GIF or WebP images are allowed'], 422);
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
            $name = bin2hex(random_bytes(10)) . '.' . $ext;
            if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $name)) json_out(['error' => 'Could not save the file'], 500);
            json_out(['ok' => true, 'file' => $name, 'url' => '../uploads/' . $name, 'kind' => $kind]);

        /* ================= networks ================= */
        case 'networks':
            json_out(['networks' => array_map('network_public', networks())]);

        case 'network_save':
            $id = (int)($in['id'] ?? 0);
            $f = [
                'name' => trim((string)($in['name'] ?? '')), 'site_id' => trim((string)($in['site_id'] ?? '')),
                'host' => trim((string)($in['host'] ?? '')), 'port' => (int)($in['port'] ?? 8443),
                'username' => trim((string)($in['username'] ?? '')), 'scheme' => ($in['scheme'] ?? 'https') === 'http' ? 'http' : 'https',
                'enabled' => !empty($in['enabled']) ? 1 : 0,
            ];
            foreach (['name', 'site_id', 'host', 'username'] as $k) if ($f[$k] === '') json_out(['error' => "$k is required"], 422);
            if ($f['port'] < 1 || $f['port'] > 65535) json_out(['error' => 'Invalid port'], 422);
            $pw = (string)($in['password'] ?? '');
            if ($id) {
                q('UPDATE networks SET name=?,site_id=?,host=?,port=?,username=?,scheme=?,enabled=? WHERE id=?',
                    [$f['name'], $f['site_id'], $f['host'], $f['port'], $f['username'], $f['scheme'], $f['enabled'], $id]);
                if ($pw !== '') q('UPDATE networks SET password=? WHERE id=?', [$pw, $id]);
            } else {
                if ($pw === '') json_out(['error' => 'password is required'], 422);
                q('INSERT INTO networks(name,site_id,host,port,username,password,scheme,enabled,created_at) VALUES(?,?,?,?,?,?,?,?,?)',
                    [$f['name'], $f['site_id'], $f['host'], $f['port'], $f['username'], $pw, $f['scheme'], $f['enabled'], time()]);
            }
            json_out(['ok' => true]);

        case 'network_delete':
            $id = (int)($in['id'] ?? 0);
            q('DELETE FROM networks WHERE id=?', [$id]);
            q('UPDATE vouchers SET network_id=NULL WHERE network_id=?', [$id]);
            json_out(['ok' => true]);

        case 'activity':
            json_out(['rows' => all('SELECT a.*, n.name AS network_name FROM activity a LEFT JOIN networks n ON n.id=a.network_id ORDER BY a.id DESC LIMIT 100')]);
    }
    json_out(['error' => 'Unknown action'], 404);
} catch (Throwable $e) {
    error_log('[admin] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    json_out(['error' => 'Server error: ' . $e->getMessage()], 500);
}
