<?php
declare(strict_types=1);
// Run every minute:  * * * * * php /path/to/unifi-portal/bin/maintenance.php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require __DIR__ . '/../app/lib.php';

$n = expire_sessions();
q('DELETE FROM attempts WHERE ts<?', [time() - 86400]);
q('DELETE FROM activity WHERE ts<?', [time() - 90 * 86400]);

// Backstop: make sure controllers don't keep guests authorized whose voucher time is over.
foreach (networks(true) as $net) {
    try {
        $u = unifi_for($net);
        foreach ($u->clients() as $c) {
            if (empty($c['is_guest']) || empty($c['authorized'])) continue;
            $mac = normalize_mac((string)($c['mac'] ?? ''));
            if (!$mac) continue;
            $st = mac_state($mac);
            if ($st['state'] === 'active') continue;
            $last = $st['session'] ?? $st['last'] ?? null;
            if ($last) { // only touch guests this portal manages
                $u->unauthorize($mac);
                log_activity((int)$net['id'], $mac, 'unauthorized', 'Time used up');
            }
        }
    } catch (Throwable $e) {
        fwrite(STDERR, "[{$net['name']}] " . $e->getMessage() . "\n");
    }
}
echo date('c') . " expired=$n\n";
