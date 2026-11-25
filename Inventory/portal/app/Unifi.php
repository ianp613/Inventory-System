<?php
declare(strict_types=1);

/**
 * Minimal UniFi Network Application API client.
 * Works with classic controllers (port 8443) and UniFi OS consoles (port 443,
 * auto-detected). Login cookies are cached on disk so we don't log in on every call.
 */
final class UnifiException extends RuntimeException {}

final class Unifi
{
    private string $base;
    private string $site;
    private string $user;
    private string $pass;
    private string $cookieFile;
    private string $stateFile;
    /** @var array{os:?bool,csrf:?string,login:int} */
    private array $state = ['os' => null, 'csrf' => null, 'login' => 0];

    public function __construct(array $net)
    {
        $scheme = in_array(($net['scheme'] ?? 'https'), ['http', 'https'], true) ? $net['scheme'] : 'https';
        $this->base = sprintf('%s://%s:%d', $scheme, $net['host'], (int)$net['port']);
        $this->site = (string)$net['site_id'];
        $this->user = (string)$net['username'];
        $this->pass = (string)$net['password'];

        $dir = DATA_DIR . '/cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $key = substr(sha1($this->base . '|' . $this->user), 0, 16);
        $this->cookieFile = "$dir/unifi_$key.cookie";
        $this->stateFile = "$dir/unifi_$key.json";
        if (is_file($this->stateFile)) {
            $s = json_decode((string)file_get_contents($this->stateFile), true);
            if (is_array($s)) {
                $this->state = array_merge($this->state, $s);
            }
        }
    }

    /* ------------------------------------------------------------------ public API */

    /** Authorize a guest for N minutes (the controller drops the guest when time is up). */
    public function authorize(string $mac, int $minutes, ?string $apMac = null): void
    {
        $body = ['cmd' => 'authorize-guest', 'mac' => $mac, 'minutes' => max(1, $minutes)];
        if ($apMac) {
            $body['ap_mac'] = $apMac;
        }
        $this->api('POST', $this->sitePath('/cmd/stamgr'), $body);
    }

    public function unauthorize(string $mac): void
    {
        $this->api('POST', $this->sitePath('/cmd/stamgr'), ['cmd' => 'unauthorize-guest', 'mac' => $mac]);
    }

    /** Force the device to re-associate. */
    public function kick(string $mac): void
    {
        $this->api('POST', $this->sitePath('/cmd/stamgr'), ['cmd' => 'kick-sta', 'mac' => $mac]);
    }

    /** All currently connected clients (wired + wireless). */
    public function clients(): array
    {
        return $this->api('GET', $this->sitePath('/stat/sta'));
    }

    /** Access points only. */
    public function accessPoints(): array
    {
        $devs = $this->api('GET', $this->sitePath('/stat/device'));
        return array_values(array_filter($devs, fn($d) => ($d['type'] ?? '') === 'uap'));
    }

    public function wlans(): array
    {
        return $this->api('GET', $this->sitePath('/rest/wlanconf'));
    }

    /* ------------------------------------------------------------------ internals */

    private function sitePath(string $suffix): string
    {
        return '/s/' . rawurlencode($this->site) . $suffix;
    }

    public function api(string $method, string $path, ?array $body = null): array
    {
        if (!$this->state['login']) {
            $this->login();
        }
        $code = 0;
        $out = '';
        for ($try = 0; $try < 2; $try++) {
            $prefix = $this->state['os'] ? '/proxy/network/api' : '/api';
            [$code, , $out] = $this->http($method, $prefix . $path, $body);
            if (in_array($code, [401, 403], true) && $try === 0) {
                $this->login();
                continue;
            }
            break;
        }
        $j = json_decode($out, true);
        if (!is_array($j)) {
            throw new UnifiException("Unexpected response from controller (HTTP $code)");
        }
        $rc = $j['meta']['rc'] ?? ($code < 400 ? 'ok' : 'error');
        if ($code >= 400 || $rc !== 'ok') {
            throw new UnifiException((string)($j['meta']['msg'] ?? "Controller error (HTTP $code)"));
        }
        return $j['data'] ?? [];
    }

    private function login(): void
    {
        @unlink($this->cookieFile);
        $this->state['csrf'] = null;
        $payload = ['username' => $this->user, 'password' => $this->pass, 'remember' => true];

        if ($this->state['os'] === true) {
            [$code, $h, $body] = $this->http('POST', '/api/auth/login', $payload);
        } else {
            [$code, $h, $body] = $this->http('POST', '/api/login', $payload);
            if ($code === 404) { // UniFi OS console
                [$code, $h, $body] = $this->http('POST', '/api/auth/login', $payload);
                $this->state['os'] = $code === 200 ? true : null;
            } else {
                $this->state['os'] = false;
            }
        }
        if ($code !== 200) {
            $msg = json_decode($body, true)['meta']['msg'] ?? ($code === 400 ? 'invalid credentials' : "HTTP $code");
            $this->state['login'] = 0;
            $this->saveState();
            throw new UnifiException("Controller login failed: $msg");
        }
        $this->state['csrf'] = $h['x-csrf-token'] ?? null;
        $this->state['login'] = time();
        $this->saveState();
    }

    /** @return array{0:int,1:array<string,string>,2:string} */
    private function http(string $method, string $path, ?array $body = null): array
    {
        $headers = ['Accept: application/json'];
        $resHeaders = [];
        $ch = curl_init($this->base . $path);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false, // controllers use self-signed certs
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$resHeaders) {
                $p = explode(':', $line, 2);
                if (count($p) === 2) {
                    $resHeaders[strtolower(trim($p[0]))] = trim($p[1]);
                }
                return strlen($line);
            },
        ];
        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
            $headers[] = 'Content-Type: application/json';
        }
        if ($method !== 'GET') {
            $csrf = $this->csrf();
            if ($csrf) {
                $headers[] = 'X-Csrf-Token: ' . $csrf;
            }
        }
        $opts[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $opts);
        $out = curl_exec($ch);
        if ($out === false) {
            $e = curl_error($ch);
            throw new UnifiException("Cannot reach controller {$this->base}: $e");
        }
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if (isset($resHeaders['x-updated-csrf-token'])) {
            $this->state['csrf'] = $resHeaders['x-updated-csrf-token'];
            $this->saveState();
        }
        return [$code, $resHeaders, (string)$out];
    }

    private function csrf(): ?string
    {
        if (!empty($this->state['csrf'])) {
            return $this->state['csrf'];
        }
        // Classic controllers (v6+) hand the token out as a cookie.
        if (is_file($this->cookieFile)) {
            foreach (file($this->cookieFile, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
                $line = preg_replace('/^#HttpOnly_/', '', $line);
                if ($line === '' || $line[0] === '#') {
                    continue;
                }
                $p = explode("\t", $line);
                if (count($p) >= 7 && $p[5] === 'csrf_token') {
                    return $p[6];
                }
            }
        }
        return null;
    }

    private function saveState(): void
    {
        @file_put_contents($this->stateFile, json_encode($this->state), LOCK_EX);
    }
}
