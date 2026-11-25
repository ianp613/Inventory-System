<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('DATA_DIR', getenv('PORTAL_DATA_DIR') ?: APP_ROOT . '/data');
define('PUBLIC_DIR', APP_ROOT . '/public');
define('UPLOAD_DIR', PUBLIC_DIR . '/uploads');
define('DB_PATH', DATA_DIR . '/portal.sqlite');
define('DEFAULT_ADMIN_USER', 'admin');
define('DEFAULT_ADMIN_PASS', 'admin123');

date_default_timezone_set(getenv('PORTAL_TZ') ?: 'Asia/Manila');
require_once __DIR__ . '/Unifi.php';

/* ====================================================================== database */

function db(): PDO
{
    static $pdo = null;
    if ($pdo) {
        return $pdo;
    }
    if (!is_dir(DATA_DIR . '/cache')) {
        mkdir(DATA_DIR . '/cache', 0775, true);
    }
    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA busy_timeout=8000; PRAGMA journal_mode=WAL; PRAGMA synchronous=NORMAL;');
    if ((int)$pdo->query('PRAGMA user_version')->fetchColumn() < 1) {
        migrate($pdo);
    }
    return $pdo;
}

function migrate(PDO $pdo): void
{
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS networks(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL, site_id TEXT NOT NULL, host TEXT NOT NULL,
        port INTEGER NOT NULL DEFAULT 8443, username TEXT NOT NULL, password TEXT NOT NULL,
        scheme TEXT NOT NULL DEFAULT 'https', enabled INTEGER NOT NULL DEFAULT 1,
        created_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS settings(k TEXT PRIMARY KEY, v TEXT);
    CREATE TABLE IF NOT EXISTS vouchers(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT NOT NULL UNIQUE, name TEXT NOT NULL,
        type TEXT NOT NULL CHECK(type IN ('single','multiple','unlimited')),
        max_uses INTEGER, duration_minutes INTEGER NOT NULL, network_id INTEGER,
        created_at INTEGER NOT NULL
    );
    CREATE INDEX IF NOT EXISTS idx_vouchers_name ON vouchers(name);
    CREATE TABLE IF NOT EXISTS sessions(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        voucher_id INTEGER, voucher_code TEXT, voucher_name TEXT,
        network_id INTEGER, mac TEXT NOT NULL, ip TEXT, hostname TEXT, ssid TEXT, ap_mac TEXT,
        started_at INTEGER NOT NULL, expires_at INTEGER NOT NULL,
        status TEXT NOT NULL DEFAULT 'active',   -- active | expired | revoked
        ended_at INTEGER, ended_reason TEXT,
        cooldown_reset INTEGER NOT NULL DEFAULT 0, last_seen INTEGER
    );
    CREATE INDEX IF NOT EXISTS idx_sessions_mac ON sessions(mac);
    CREATE INDEX IF NOT EXISTS idx_sessions_voucher ON sessions(voucher_id);
    CREATE INDEX IF NOT EXISTS idx_sessions_status ON sessions(status, expires_at);
    CREATE TABLE IF NOT EXISTS attempts(k TEXT NOT NULL, ts INTEGER NOT NULL, ok INTEGER NOT NULL DEFAULT 0);
    CREATE INDEX IF NOT EXISTS idx_attempts ON attempts(k, ts);
    CREATE TABLE IF NOT EXISTS activity(
        id INTEGER PRIMARY KEY AUTOINCREMENT, ts INTEGER NOT NULL,
        network_id INTEGER, mac TEXT, action TEXT NOT NULL, detail TEXT
    );
    CREATE TABLE IF NOT EXISTS admins(
        id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL
    );
    ");

    $pdo->prepare("INSERT OR IGNORE INTO settings(k,v) VALUES('app_secret',?)")->execute([bin2hex(random_bytes(32))]);
    $pdo->prepare('INSERT OR IGNORE INTO admins(username,password_hash) VALUES(?,?)')
        ->execute([DEFAULT_ADMIN_USER, password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT)]);

    if ((int)$pdo->query('SELECT COUNT(*) FROM networks')->fetchColumn() === 0) {
        $file = APP_ROOT . '/app/config/controllers.json';
        $j = is_file($file) ? json_decode((string)file_get_contents($file), true) : null;
        $ins = $pdo->prepare('INSERT INTO networks(name,site_id,host,port,username,password,scheme,enabled,created_at) VALUES(?,?,?,?,?,?,?,1,?)');
        foreach (($j['unifi'] ?? []) as $n) {
            $ins->execute([$n['name'], $n['site_id'], $n['host'], (int)($n['port'] ?? 8443),
                $n['username'], $n['password'], $n['scheme'] ?? 'https', time()]);
        }
    }
    $pdo->exec('PRAGMA user_version=1');
}

function q(string $sql, array $p = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($p);
    return $st;
}
function one(string $sql, array $p = []): ?array
{
    $r = q($sql, $p)->fetch();
    return $r === false ? null : $r;
}
function all(string $sql, array $p = []): array
{
    return q($sql, $p)->fetchAll();
}
function val(string $sql, array $p = [])
{
    return q($sql, $p)->fetchColumn();
}

/* ====================================================================== settings */

/** key => [default, type, arg]  (types: str,text,color,int,bool,enum,url,file) */
function setting_schema(): array
{
    return [
        // ---- captive portal look & feel
        'portal_page_title'       => ['Free WiFi', 'str', 120],
        'portal_title'            => ['DDC Free Wifi 🛜', 'str', 160],
        'portal_subtitle'         => ['', 'str', 300],
        'portal_logo'             => ['', 'file', 0],
        'portal_logo_size'        => [150, 'int', [24, 300]],
        'portal_bg_type'          => ['color', 'enum', ['color', 'gradient', 'image']],
        'portal_bg_color'         => ['#8fb8c1', 'color', 0],
        'portal_bg_color2'        => ['#0ea5e9', 'color', 0],
        'portal_bg_image'         => ['', 'file', 0],
        'portal_bg_overlay'       => [30, 'int', [0, 90]],
        'portal_card_bg'          => ['#ffffff', 'color', 0],
        'portal_card_opacity'     => [0, 'int', [0, 100]],
        'portal_text_color'       => ['#111827', 'color', 0],
        'portal_muted_color'      => ['#6b7280', 'color', 0],
        'portal_primary_color'    => ['#157482', 'color', 0],
        'portal_button_text_color' => ['#ffffff', 'color', 0],
        'portal_radius'           => [16, 'int', [0, 40]],
        'portal_font'             => ['system', 'enum', ['system', 'serif', 'rounded', 'mono']],
        'portal_input_label'      => ['Enter Access Code', 'str', 60],
        'portal_button_text'      => ['Get Wifi Access', 'str', 40],
        'portal_terms'            => ['', 'text', 1000],
        'portal_footer'           => ['Powered By: DDC Wifi Team', 'str', 300],
        'portal_footer_color'     => ['#5f6b73', 'color', 0],
        'portal_success_title'    => ["You're connected!", 'str', 120],
        'portal_success_message'  => ['Enjoy your WiFi. Your time ends in {time}.', 'str', 300],
        'portal_cooldown_title'   => ['Please wait', 'str', 120],
        'portal_cooldown_message' => ['Your free time has been used up. You can enter a new voucher in {time}.', 'str', 300],
        'portal_redirect_mode'    => ['original', 'enum', ['original', 'custom', 'none']],
        'portal_redirect_url'     => ['', 'url', 0],
        'portal_custom_css'       => ['', 'text', 4000],
        // ---- user rules
        'cooldown_minutes'        => [30, 'int', [0, 525600]],
        'max_attempts'            => [5, 'int', [0, 100]],
        'lockout_minutes'         => [5, 'int', [1, 1440]],
    ];
}

function cast_setting($v, string $type)
{
    return match ($type) {
        'int', 'bool' => (int)$v,
        default => (string)$v,
    };
}

function settings(bool $fresh = false): array
{
    static $cache = null;
    if ($cache === null || $fresh) {
        $rows = db()->query('SELECT k,v FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
        $cache = [];
        foreach (setting_schema() as $k => [$def, $type]) {
            $cache[$k] = array_key_exists($k, $rows) ? cast_setting($rows[$k], $type) : $def;
        }
    }
    return $cache;
}

function clean_setting(string $k, $v)
{
    $schema = setting_schema();
    [$def, $type, $arg] = $schema[$k];
    switch ($type) {
        case 'str':
        case 'text':
            return mb_substr(trim((string)$v), 0, (int)$arg);
        case 'color':
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', (string)$v)) {
                throw new InvalidArgumentException("Invalid color for $k");
            }
            return strtolower((string)$v);
        case 'int':
            return max($arg[0], min($arg[1], (int)$v));
        case 'bool':
            return filter_var($v, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        case 'enum':
            if (!in_array($v, $arg, true)) {
                throw new InvalidArgumentException("Invalid value for $k");
            }
            return $v;
        case 'url':
            $v = trim((string)$v);
            if ($v !== '' && (!filter_var($v, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $v))) {
                throw new InvalidArgumentException('Redirect URL must start with http:// or https://');
            }
            return $v;
        case 'file':
            $v = (string)$v;
            if ($v !== '' && (!preg_match('/^[a-f0-9]{16,}\.(png|jpe?g|gif|webp)$/', $v) || !is_file(UPLOAD_DIR . '/' . $v))) {
                throw new InvalidArgumentException("Unknown uploaded file for $k");
            }
            return $v;
    }
    throw new InvalidArgumentException("Unsupported setting $k");
}

function save_settings(array $kv): void
{
    $schema = setting_schema();
    $st = db()->prepare('INSERT INTO settings(k,v) VALUES(?,?) ON CONFLICT(k) DO UPDATE SET v=excluded.v');
    db()->exec('BEGIN');
    try {
        foreach ($kv as $k => $v) {
            if (isset($schema[$k])) {
                $st->execute([$k, (string)clean_setting($k, $v)]);
            }
        }
        db()->exec('COMMIT');
    } catch (Throwable $e) {
        db()->exec('ROLLBACK');
        throw $e;
    }
    settings(true);
}

/* ====================================================================== small helpers */

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_out($data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function normalize_mac(string $m): string
{
    $m = strtolower(preg_replace('/[^0-9a-fA-F]/', '', $m));
    return strlen($m) === 12 ? implode(':', str_split($m, 2)) : '';
}
function normalize_code(string $c): string
{
    return preg_replace('/\D/', '', $c);
}
function format_code(string $c): string
{
    return ctype_digit($c) ? $c : implode('-', str_split($c, 4));
}
function gen_code(): string
{
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}
function client_ip(): string
{
    return (string)($_SERVER['REMOTE_ADDR'] ?? '');
}

function fmt_duration(int $sec): string
{
    $sec = max(0, $sec);
    $d = intdiv($sec, 86400);
    $h = intdiv($sec % 86400, 3600);
    $m = intdiv($sec % 3600, 60);
    $s = $sec % 60;
    if ($d) return "{$d}d {$h}h";
    if ($h) return "{$h}h {$m}m";
    if ($m) return $m . 'm' . ($sec < 600 ? " {$s}s" : '');
    return "{$s}s";
}

function log_activity(?int $networkId, ?string $mac, string $action, string $detail = ''): void
{
    q('INSERT INTO activity(ts,network_id,mac,action,detail) VALUES(?,?,?,?,?)', [time(), $networkId, $mac, $action, $detail]);
}

/* ====================================================================== networks */

function networks(bool $onlyEnabled = false): array
{
    return all('SELECT * FROM networks' . ($onlyEnabled ? ' WHERE enabled=1' : '') . ' ORDER BY name COLLATE NOCASE');
}
function network_by_id(int $id): ?array
{
    return one('SELECT * FROM networks WHERE id=?', [$id]);
}
function network_by_site(string $site): ?array
{
    return one('SELECT * FROM networks WHERE site_id=? AND enabled=1 ORDER BY id LIMIT 1', [$site]);
}
function network_public(array $n): array
{
    unset($n['password']);
    return $n;
}
function unifi_for(array $net): Unifi
{
    return new Unifi($net);
}

/* ====================================================================== sessions / cooldown */

function cooldown_seconds(): int
{
    return settings()['cooldown_minutes'] * 60;
}

/**
 * State of a device: free | active | cooldown.
 * The clock of a session is wall-clock time: it runs whether or not the device is connected.
 */
function mac_state(string $mac): array
{
    $now = time();
    $s = one('SELECT * FROM sessions WHERE mac=? ORDER BY id DESC LIMIT 1', [$mac]);
    if (!$s) {
        return ['state' => 'free'];
    }
    if ($s['status'] === 'active') {
        if ((int)$s['expires_at'] > $now) {
            return ['state' => 'active', 'session' => $s, 'remaining' => (int)$s['expires_at'] - $now];
        }
        q("UPDATE sessions SET status='expired', ended_at=expires_at, ended_reason='time' WHERE id=?", [$s['id']]);
        $s['status'] = 'expired';
        $s['ended_at'] = $s['expires_at'];
    }
    $endedAt = (int)($s['ended_at'] ?: $s['expires_at']);
    $cd = cooldown_seconds();
    if (!(int)$s['cooldown_reset'] && $cd > 0 && $now < $endedAt + $cd) {
        return ['state' => 'cooldown', 'session' => $s, 'cooldown_until' => $endedAt + $cd, 'remaining' => $endedAt + $cd - $now];
    }
    return ['state' => 'free', 'last' => $s];
}

function expire_sessions(): int
{
    return q("UPDATE sessions SET status='expired', ended_at=expires_at, ended_reason='time' WHERE status='active' AND expires_at<=?", [time()])->rowCount();
}

/* ====================================================================== vouchers */

function voucher_rows(array $f = []): array
{
    $now = time();
    $rows = all("SELECT v.*, n.name AS network_name,
        (SELECT COUNT(*) FROM sessions s WHERE s.voucher_id=v.id) AS uses,
        (SELECT COUNT(*) FROM sessions s WHERE s.voucher_id=v.id AND s.status='active' AND s.expires_at>?) AS active_uses
        FROM vouchers v LEFT JOIN networks n ON n.id=v.network_id ORDER BY v.id DESC", [$now]);
    $out = [];
    $qs = strtoupper(trim((string)($f['q'] ?? '')));
    $qn = normalize_code($qs);
    foreach ($rows as $r) {
        $r['status'] = voucher_status($r);
        if (!empty($f['status']) && $r['status'] !== $f['status']) continue;
        if (!empty($f['type']) && $r['type'] !== $f['type']) continue;
        if (isset($f['name']) && $f['name'] !== '' && $r['name'] !== $f['name']) continue;
        if ($qs !== '' && stripos($r['name'], $qs) === false && ($qn === '' || strpos($r['code'], $qn) === false)) continue;
        $r['code_fmt'] = format_code($r['code']);
        $out[] = $r;
    }
    return $out;
}

function voucher_status(array $v): string
{
    $uses = (int)$v['uses'];
    if ((int)$v['active_uses'] > 0) return 'in_use';
    if ($uses === 0) return 'unused';
    $exhausted = $v['type'] === 'single' ? $uses >= 1 : ($v['type'] === 'multiple' ? $uses >= (int)$v['max_uses'] : false);
    return $exhausted ? 'used' : 'partial';
}

/* ====================================================================== signed tokens (portal context) */

function app_secret(): string
{
    static $s = null;
    return $s ??= (string)val("SELECT v FROM settings WHERE k='app_secret'");
}
function b64u_enc(string $s): string
{
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}
function b64u_dec(string $s): string
{
    return (string)base64_decode(strtr($s, '-_', '+/'));
}
function sign(string $payload): string
{
    return b64u_enc(hash_hmac('sha256', $payload, app_secret(), true));
}
function make_ctx(array $d): string
{
    $p = b64u_enc(json_encode($d));
    return $p . '.' . sign($p);
}
function read_ctx(string $t, int $maxAge = 21600): ?array
{
    $parts = explode('.', $t);
    if (count($parts) !== 2 || !hash_equals(sign($parts[0]), $parts[1])) return null;
    $d = json_decode(b64u_dec($parts[0]), true);
    if (!is_array($d) || empty($d['m']) || empty($d['s']) || time() - (int)($d['t'] ?? 0) > $maxAge) return null;
    return $d;
}

/* ====================================================================== brute-force protection */

function attempt_keys(string $mac): array
{
    return [$mac, 'ip:' . client_ip()];
}
function lockout_remaining(array $keys): int
{
    $s = settings();
    if ($s['max_attempts'] <= 0) return 0;
    $win = $s['lockout_minutes'] * 60;
    $worst = 0;
    foreach ($keys as $k) {
        $r = one('SELECT COUNT(*) c, MAX(ts) m FROM attempts WHERE k=? AND ok=0 AND ts>?', [$k, time() - $win]);
        if ((int)$r['c'] >= $s['max_attempts']) {
            $worst = max($worst, (int)$r['m'] + $win - time());
        }
    }
    return $worst;
}
function record_attempt(array $keys, bool $ok): void
{
    foreach ($keys as $k) {
        if ($ok) {
            q('DELETE FROM attempts WHERE k=?', [$k]);
        } else {
            q('INSERT INTO attempts(k,ts,ok) VALUES(?,?,0)', [$k, time()]);
        }
    }
}

/* ====================================================================== portal flow */

function fail(string $msg, string $code = 'invalid'): array
{
    return ['ok' => false, 'error' => $msg, 'code' => $code];
}

/** Validates a voucher and creates the session row atomically. Does not talk to UniFi. */
function redeem_voucher(array $net, array $ctx, string $rawCode): array
{
    $code = normalize_code($rawCode);
    if (strlen($code) !== 6) {
        return fail('Please enter the 6-digit voucher code.');
    }
    $pdo = db();
    $now = time();
    $pdo->exec('BEGIN IMMEDIATE');
    try {
        $rollback = function (string $msg, string $c) use ($pdo): array {
            $pdo->exec('ROLLBACK');
            return fail($msg, $c);
        };
        $v = one('SELECT * FROM vouchers WHERE code=?', [$code]);
        if (!$v) return $rollback('Invalid voucher code. Please check it and try again.', 'invalid');
        if ($v['network_id'] && (int)$v['network_id'] !== (int)$net['id']) {
            return $rollback('This voucher cannot be used on this network.', 'network');
        }
        $uses = (int)val('SELECT COUNT(*) FROM sessions WHERE voucher_id=?', [$v['id']]);
        $macUsed = (int)val('SELECT COUNT(*) FROM sessions WHERE voucher_id=? AND mac=?', [$v['id'], $ctx['m']]);
        if ($v['type'] === 'single' && $uses >= 1) {
            return $rollback('This voucher has already been used.', 'used');
        }
        if ($v['type'] === 'multiple') {
            if ($macUsed) return $rollback('You have already used this voucher on this device.', 'used');
            if ($uses >= (int)$v['max_uses']) return $rollback('This voucher has reached its usage limit.', 'used');
        }
        q('INSERT INTO sessions(voucher_id,voucher_code,voucher_name,network_id,mac,ip,ssid,ap_mac,started_at,expires_at,status,last_seen)
           VALUES(?,?,?,?,?,?,?,?,?,?,?,?)', [
            $v['id'], $v['code'], $v['name'], $net['id'], $ctx['m'], client_ip(), $ctx['ss'] ?? null, $ctx['a'] ?? null,
            $now, $now + (int)$v['duration_minutes'] * 60, 'active', $now,
        ]);
        $sid = (int)$pdo->lastInsertId();
        $pdo->exec('COMMIT');
    } catch (Throwable $e) {
        try { $pdo->exec('ROLLBACK'); } catch (Throwable) {}
        throw $e;
    }
    return ['ok' => true, 'session' => one('SELECT * FROM sessions WHERE id=?', [$sid])];
}

/** Authorize (or re-authorize) a device on the controller for the time that is left on its session. */
function grant_access(array $net, array $ctx, array $session): array
{
    $remaining = max(1, (int)$session['expires_at'] - time());
    try {
        unifi_for($net)->authorize($ctx['m'], (int)ceil($remaining / 60), $ctx['a'] ?? null);
    } catch (Throwable $e) {
        log_activity((int)$net['id'], $ctx['m'], 'authorize_failed', $e->getMessage());
        return fail('We could not reach the network controller. Please try again in a moment.', 'controller');
    }
    q('UPDATE sessions SET network_id=?, ssid=COALESCE(?,ssid), ap_mac=COALESCE(?,ap_mac), ip=?, last_seen=? WHERE id=?',
        [$net['id'], $ctx['ss'] ?? null, $ctx['a'] ?? null, client_ip(), time(), $session['id']]);
    return success_payload($ctx, $remaining);
}

function success_payload(array $ctx, int $remaining): array
{
    $s = settings();
    $url = null;
    if ($s['portal_redirect_mode'] === 'custom' && $s['portal_redirect_url'] !== '') {
        $url = $s['portal_redirect_url'];
    } elseif ($s['portal_redirect_mode'] === 'original' && !empty($ctx['u']) && preg_match('#^https?://#i', (string)$ctx['u'])) {
        $url = $ctx['u'];
    }
    return [
        'ok' => true,
        'remaining' => $remaining,
        'redirect' => $url,
        'title' => $s['portal_success_title'],
        'message' => str_replace('{time}', fmt_duration($remaining), $s['portal_success_message']),
    ];
}

/* ====================================================================== admin session */

function admin_session_start(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('UPADMIN');
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/', 'secure' => !empty($_SERVER['HTTPS'])]);
        session_start();
    }
}
function admin_logged_in(): bool
{
    admin_session_start();
    return !empty($_SESSION['admin_id']);
}
