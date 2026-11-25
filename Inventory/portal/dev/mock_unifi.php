<?php
// Tiny fake UniFi controller for testing:  php -S 127.0.0.1:9443 dev/mock_unifi.php
// State is kept in /tmp/mock_unifi.json. Not for production.
$f = '/tmp/mock_unifi.json';
$st = is_file($f) ? json_decode(file_get_contents($f), true) : ['auth' => [], 'log' => []];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$body = json_decode(file_get_contents('php://input'), true) ?: [];
header('Content-Type: application/json');
$ok = fn($d = []) => print(json_encode(['meta' => ['rc' => 'ok'], 'data' => $d]));
if ($path === '/api/login') {
    if (($body['password'] ?? '') !== 'secret') { http_response_code(400); echo json_encode(['meta' => ['rc' => 'error', 'msg' => 'api.err.Invalid']]); exit; }
    setcookie('unifises', 'abc'); $ok(); exit;
}
if (($_COOKIE['unifises'] ?? '') !== 'abc') { http_response_code(401); echo json_encode(['meta' => ['rc' => 'error', 'msg' => 'api.err.LoginRequired']]); exit; }
if (preg_match('#/s/([^/]+)/cmd/stamgr#', $path)) {
    $st['log'][] = $body; $mac = $body['mac'];
    if ($body['cmd'] === 'authorize-guest') $st['auth'][$mac] = time() + $body['minutes'] * 60;
    if ($body['cmd'] === 'unauthorize-guest') unset($st['auth'][$mac]);
    file_put_contents($f, json_encode($st)); $ok(); exit;
}
if (preg_match('#/stat/sta#', $path)) {
    $c = [];
    foreach ([['aa:bb:cc:00:00:01', 'OPPO-A54', 'DDC Free Wifi', 95, true], ['aa:bb:cc:00:00:02', 'iPhone', 'ITAdmin', 70, false]] as [$m, $n, $s, $sat, $g]) {
        $c[] = ['mac' => $m, 'name' => $n, 'essid' => $s, 'satisfaction' => $sat, 'ip' => '192.168.32.' . hexdec(substr($m, -2)), 'uptime' => 3600, 'is_guest' => $g, 'authorized' => isset($st['auth'][$m]), 'is_wired' => false];
    }
    $ok($c); exit;
}
if (preg_match('#/stat/device#', $path)) { $ok([['type' => 'uap', 'mac' => '11:22:33:44:55:66']]); exit; }
http_response_code(404); echo json_encode(['meta' => ['rc' => 'error', 'msg' => 'not found']]);
