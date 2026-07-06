<?php
header('Content-Type: application/json');
include("../../../includes.php");
if(array_key_exists('HTTP_ORIGIN',$_SERVER)){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");

    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }
}

$unifiSites = json_decode(file_get_contents("../../../assets/files/unifi-mac.config.json"));

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['code']) || empty($input['site'])) {
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Missing code or site."]);
    exit;
}

$code = strtoupper(trim($input['code']));
$siteId = $input['site'];

// ---------------------------------------------------------------
// 1. Look up the voucher.
// ---------------------------------------------------------------
$voucher = new Captive_;
$result = DB::where($voucher, "code", "=", $code);

if (empty($result)) {
    echo json_encode(["status" => false, "message" => "That code isn't valid."]);
    exit;
}

$row = is_array($result) ? $result[0] : $result;

// ---------------------------------------------------------------
// 2. Validate the voucher can still be used.
// ---------------------------------------------------------------
if ($row["status"] === 'revoked') {
    echo json_encode(["status" => false, "message" => "This code has been revoked."]);
    exit;
}

if ($row["status"] === 'used' || ($row["uses"] > 0 && $row["uses_remaining"] <= 0)) {
    echo json_encode(["status" => false, "message" => "This code has already been used up."]);
    exit;
}

// ---------------------------------------------------------------
// 3. Match this voucher's site against your controller configs.
// ---------------------------------------------------------------
$controller = null;
foreach ($unifiSites->unifi as $site) {
    if ($site->site_id === $siteId) {
        $controller = $site;
        break;
    }
}

if (!$controller) {
    echo json_encode(["status" => false, "message" => "No controller is configured for site '{$siteId}'."]);
    exit;
}

// ---------------------------------------------------------------
// 4. Convert duration to minutes.
// ---------------------------------------------------------------
$unitToMinutes = ["Minutes" => 1, "Hours" => 60, "Days" => 1440];
$minutes = $row["duration"] * ($unitToMinutes[$row["duration_unit"]] ?? 1);

$mac = $input['mac'] ?? '';
$apMac = $input['ap'] ?? '';

if (empty($mac)) {
    echo json_encode(["status" => false, "message" => "Missing client MAC address."]);
    exit;
}

// =================================================================
// 5. Log in to the UniFi controller and get a session + CSRF token.
// =================================================================
function unifiControllerLogin($controller) {
    $baseUrl = "https://{$controller->host}:{$controller->port}";
    $cookieFile = tempnam(sys_get_temp_dir(), 'unifi_');

    $ch = curl_init("{$baseUrl}/api/login");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            "username" => $controller->username,
            "password" => $controller->password
        ]),
        CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_COOKIEFILE     => $cookieFile,
        // Self-hosted controllers almost always run self-signed certs.
        // Turning verification off is the common workaround, but it
        // does mean this connection can't detect a tampered certificate
        // on the way to your controller — acceptable on a private/VPN
        // link, worth revisiting if this call ever crosses the open
        // internet.
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        return ["ok" => false, "error" => $error ?: "Login failed (HTTP {$httpCode})", "cookieFile" => $cookieFile];
    }

    $headers = substr($response, 0, $headerSize);

    // Controller versions 6.0+ send this header on login — later calls
    // that change state (like authorize-guest) need it echoed back.
    $csrfToken = null;
    if (preg_match('/x-csrf-token:\s*(\S+)/i', $headers, $m)) {
        $csrfToken = trim($m[1]);
    }

    return ["ok" => true, "cookieFile" => $cookieFile, "csrfToken" => $csrfToken];
}

// =================================================================
// 6. Authorize the guest's MAC address on that controller/site.
// =================================================================
function unifiAuthorizeGuest($controller, $cookieFile, $csrfToken, $mac, $minutes, $dataCapMb, $apMac) {
    $baseUrl = "https://{$controller->host}:{$controller->port}";
    $url = "{$baseUrl}/api/s/{$controller->site_id}/cmd/stamgr";

    $payload = [
        "cmd"     => "authorize-guest",
        "mac"     => strtolower($mac),
        "minutes" => (int)$minutes
    ];
    if (!empty($apMac))        $payload["ap_mac"] = strtolower($apMac);
    // Total data quota for the session, converted MB -> bytes. UniFi has
    // no separate "data limit" vs "data cap" concept in this legacy
    // authorize-guest command — only one quota field ("bytes"). If your
    // data_limit and data_cap are meant to do two different things
    // (e.g. a speed cap vs a total quota), that distinction has to be
    // enforced elsewhere (traffic rules / QoS on the controller), not
    // through this call.
    if ($dataCapMb > 0)        $payload["bytes"] = $dataCapMb * 1024 * 1024;

    $headers = ["Content-Type: application/json"];
    if ($csrfToken) $headers[] = "X-Csrf-Token: {$csrfToken}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ["ok" => false, "error" => $error ?: "No response from controller"];
    }

    $decoded = json_decode($response, true);
    $rc = $decoded['meta']['rc'] ?? null;

    if ($httpCode >= 400 || $rc !== 'ok') {
        return ["ok" => false, "error" => $decoded['meta']['msg'] ?? "Authorization failed (HTTP {$httpCode})"];
    }

    return ["ok" => true, "raw" => $decoded];
}

// -----------------------------------------------------------------
// Run it.
// -----------------------------------------------------------------
$login = unifiControllerLogin($controller);

if (!$login['ok']) {
    @unlink($login['cookieFile']);
    echo json_encode(["status" => false, "message" => "Could not reach the controller: " . $login['error']]);
    exit;
}

$auth = unifiAuthorizeGuest($controller, $login['cookieFile'], $login['csrfToken'], $mac, $minutes, (int)$row["data_cap"], $apMac);

@unlink($login['cookieFile']); // done with the session either way

if (!$auth['ok']) {
    echo json_encode(["status" => false, "message" => "Controller rejected the request: " . $auth['error']]);
    exit;
}

// ---------------------------------------------------------------
// 7. Success — decrement uses and flip status if this was the last
//    use. Adjust this DB call to whatever your ORM's update syntax
//    actually is (DB::where(...) above was for reading; this needs
//    your library's write/update equivalent).
// ---------------------------------------------------------------
$newUsesRemaining = $row["uses"] > 0 ? max(0, (int)$row["uses_remaining"] - 1) : $row["uses_remaining"];
$newStatus = ($row["uses"] > 0 && $newUsesRemaining <= 0) ? "used" : $row["status"];

// Example — replace with your actual update call:
// DB::update($voucher, $row["id"], [
//     "uses_remaining" => $newUsesRemaining,
//     "status"         => $newStatus
// ]);

echo json_encode([
    "status"   => true,
    "message"  => "Connected.",
    "redirect" => $input['url'] ?? 'https://example.com'
]);