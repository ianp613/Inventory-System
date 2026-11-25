<?php
declare(strict_types=1);
require __DIR__ . '/../../app/lib.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$in = json_decode((string)file_get_contents('php://input'), true);
$in = is_array($in) ? $in : [];
$action = (string)($in['action'] ?? '');

try {
    $ctx = read_ctx((string)($in['ctx'] ?? ''));
    if (!$ctx) {
        json_out(fail('Your session expired. Please reconnect to the WiFi and try again.', 'ctx'), 400);
    }
    $net = network_by_site((string)$ctx['s']);
    if (!$net) {
        json_out(fail('This network is not configured.', 'network'), 404);
    }
    $mac = $ctx['m'];
    $state = mac_state($mac);

    // Device already has a running voucher: just let it back in.
    if ($state['state'] === 'active') {
        $res = grant_access($net, $ctx, $state['session']);
        if ($res['ok']) {
            log_activity((int)$net['id'], $mac, 'reconnect', 'Resumed ' . $state['session']['voucher_code']);
        }
        json_out($res + ['state' => 'active']);
    }
    if ($action === 'resume') {
        json_out(fail('No active session.', 'none') + ['state' => $state['state']]);
    }
    if ($action !== 'redeem') {
        json_out(fail('Unknown action'), 400);
    }

    if ($state['state'] === 'cooldown') {
        json_out(fail('Please wait before using another voucher.', 'cooldown') + ['state' => 'cooldown', 'remaining' => $state['remaining']], 429);
    }

    $keys = attempt_keys($mac);
    if ($left = lockout_remaining($keys)) {
        json_out(fail('Too many wrong attempts. Try again in ' . fmt_duration($left) . '.', 'locked') + ['lock' => $left], 429);
    }

    $res = redeem_voucher($net, $ctx, (string)($in['code'] ?? ''));
    if (!$res['ok']) {
        record_attempt($keys, false);
        log_activity((int)$net['id'], $mac, 'voucher_rejected', substr(normalize_code((string)($in['code'] ?? '')), 0, 16) . ': ' . $res['error']);
        json_out($res);
    }

    $sess = $res['session'];
    $grant = grant_access($net, $ctx, $sess);
    if (!$grant['ok']) {
        // Controller failed: give the voucher back so the guest can try again.
        q('DELETE FROM sessions WHERE id=?', [$sess['id']]);
        json_out($grant, 502);
    }
    record_attempt($keys, true);
    log_activity((int)$net['id'], $mac, 'voucher_redeemed', format_code($sess['voucher_code']) . ' (' . $sess['voucher_name'] . ')');
    json_out($grant);
} catch (Throwable $e) {
    error_log('[portal] ' . $e->getMessage());
    json_out(fail('Something went wrong. Please try again.', 'server'), 500);
}
