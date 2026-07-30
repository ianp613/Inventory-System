<?php
/**
 * get-code.php
 *
 * Expects POST JSON body: { "voucher_site": "..." }  (matched against "site_id" in config)
 * Returns JSON: { status, message, controller, vouchers }
 *
 * status is one of: "success", "warning", "danger", "info"
 */

header('Content-Type: application/json');
include("../../includes.php");
$data = json_decode(file_get_contents('php://input'), true);

$user = new User;
$user = DB::where($user,"name","=",$data["name"]);

$mac = new MAC_Address;
$mac->gid       = 4; //NETWORK TEAM ID USER GROUP
$mac->uid       = $user[0]["id"];
$mac->wid       = 10; //WLANTestMode ID in NETWORK TEAM USER GROUP
$mac->mac       = "-";
$mac->name      = $data["voucher_name"];
$mac->device    = $data["voucher_device"];
$mac->project   = $data["voucher_project"];
$mac->location  = $data["voucher_location"];
$mac->remarks   = "-";
DB::save($mac);

invalidate_mac_caches($redis, 4);

// ---------------------------------------------------------------
// 1. Read input
// ---------------------------------------------------------------
$voucher_site = $data['voucher_site'] ?? '';

if ($voucher_site === '') {
    respond('warning', 'Missing "voucher_site" parameter.');
}

// ---------------------------------------------------------------
// 2. Load controllers config
// ---------------------------------------------------------------
$configPath = '../../assets/files/unifi-mac.config.json'; // adjust path if needed

if (!file_exists($configPath)) {
    respond('danger', 'controllers.json not found on server.');
}

$config = json_decode(file_get_contents($configPath), true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($config['unifi'])) {
    respond('danger', 'controllers.json is invalid or malformed.');
}

// ---------------------------------------------------------------
// 3. Find the matching controller by site_id
// ---------------------------------------------------------------
$controller = null;
foreach ($config['unifi'] as $c) {
    if (($c['site_id'] ?? '') === $voucher_site) {
        $controller = $c;
        break;
    }
}

if ($controller === null) {
    respond('warning', "Site \"$voucher_site\" was not found in controllers.json.");
}

$log = new Logs;
$log->gid = 4;
$log->uid = $user[0]["id"];
$log->log = $user[0]["name"]." has requested a voucher code from site ".$controller['name'].".";
DB::save($log);

// ---------------------------------------------------------------
// 4. Fetch vouchers from the matched controller
// ---------------------------------------------------------------
$result = fetchVouchers($controller);


respond(
    $result['status'],
    $result['message'],
    $controller['name'],
    $result['vouchers'] ?? []
);

// =================================================================
// Functions
// =================================================================

/**
 * Logs into a UniFi controller and retrieves its hotspot vouchers.
 */
function fetchVouchers(array $controller): array
{
    $base = "https://{$controller['host']}:{$controller['port']}";
    $site = $controller['site_id'];
    $name = $controller['name'];

    $cookieFile = tempnam(sys_get_temp_dir(), 'unifi_');

    // --- Login ---
    $loginPayload = json_encode([
        'username' => $controller['username'],
        'password' => $controller['password'],
    ]);

    $ch = curl_init("$base/api/login");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $loginPayload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, // self-signed certs on local controllers
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,     // fail fast if host unreachable
        CURLOPT_TIMEOUT        => 10,
    ]);

    $loginResponse = curl_exec($ch);
    $curlErrno     = curl_errno($ch);
    $curlError     = curl_error($ch);
    $httpCode      = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // --- Connection-level failure (host down, timeout, DNS, refused) ---
    if ($curlErrno !== 0) {
        @unlink($cookieFile);
        return [
            'status'  => 'danger',
            'message' => "Controller \"$name\" is not reachable ($curlError).",
        ];
    }

    // --- Authentication failure ---
    if ($httpCode !== 200) {
        @unlink($cookieFile);
        return [
            'status'  => 'danger',
            'message' => "Login failed for \"$name\" (HTTP $httpCode). Check credentials.",
        ];
    }

    // --- Fetch vouchers ---
    $ch = curl_init("$base/api/s/$site/stat/voucher");
    curl_setopt_array($ch, [
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
    ]);

    $voucherResponse = curl_exec($ch);
    $curlErrno2      = curl_errno($ch);
    $curlError2      = curl_error($ch);
    $httpCode2       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // logout (best-effort, ignore failures)
    $ch = curl_init("$base/api/logout");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 5,
    ]);
    curl_exec($ch);
    curl_close($ch);
    @unlink($cookieFile);

    if ($curlErrno2 !== 0) {
        return [
            'status'  => 'danger',
            'message' => "Lost connection to \"$name\" while fetching vouchers ($curlError2).",
        ];
    }

    if ($httpCode2 !== 200) {
        return [
            'status'  => 'danger',
            'message' => "Failed to fetch vouchers from \"$name\" (HTTP $httpCode2).",
        ];
    }

    $decoded = json_decode($voucherResponse, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['data'])) {
        return [
            'status'  => 'danger',
            'message' => "Unexpected response format from \"$name\".",
        ];
    }

    // --- Shape the voucher list down to what the front end needs ---
    $vouchers = array_map(function ($v) {
        return [
            'id'          => $v['_id']         ?? null,
            'code'        => $v['code']         ?? null,
            'note'        => $v['note']         ?? '',
            'duration'    => $v['duration']     ?? null,  // minutes
            'quota'       => $v['quota']        ?? null,  // 0 = multi-use, 1 = single-use
            'used'        => $v['used']         ?? 0,     // times used
            'create_time' => $v['create_time']  ?? null,
            'status'      => $v['status']       ?? null,  // e.g. VALID_ONE, USED_MULTIPLE, EXPIRED
            'down'        => array_key_exists("qos_rate_max_down", $v) ? $v['qos_rate_max_down'] : 0,
            'up'          => array_key_exists("qos_rate_max_up", $v) ? $v['qos_rate_max_up'] : 0
        ];
    }, $decoded['data']);

    if (count($vouchers) === 0) {
        return [
            'status'   => 'info',
            'message'  => "No vouchers found for \"$name\".",
            'vouchers' => [],
        ];
    }

    return [
        'status'   => 'success',
        'message'  => count($vouchers) . " voucher(s) found for \"$name\".",
        'vouchers' => $vouchers,
    ];
}

/**
 * Sends a JSON response and terminates.
 *
 * @param string $status  one of: success, warning, danger, info
 */
function respond(string $status, string $message, string $controller = '', array $vouchers = []): void
{
    echo json_encode([
        'status'     => $status,
        'message'    => $message,
        'controller' => $controller,
        'vouchers'   => $vouchers,
    ]);
    exit;
}