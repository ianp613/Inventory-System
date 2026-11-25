# UniFi Voucher Captive Portal

PHP + JavaScript + SQLite captive portal for several UniFi controllers/sites, with a voucher manager,
user list, dashboard and a theme editor for the guest page.

## Requirements
PHP 8.1+ with `pdo_sqlite`, `curl`, `mbstring`, `json`; Apache (mod_rewrite) or nginx + php-fpm.
The web server must be able to reach every controller on its API port (8443), and the controllers'
guests must be able to reach this server (port 80/443) **before** they are authorized.

## Install
1. Copy the folder to the server (e.g. `/var/www/unifi-portal`) and point the web root at **`public/`**.
   `app/`, `data/` and `bin/` must stay outside the web root (they hold the SQLite file and the controller passwords).
2. Make `data/` and `public/uploads/` writable by the web user: `chown -R www-data data public/uploads`.
3. Apache: `public/.htaccess` already contains the rewrite. nginx: see `nginx.conf.example`.
4. Open `http://<server>/admin/` and sign in with **admin / admin123**, then change the password (Account page).
   Your four controllers from `app/config/controllers.json` are imported on first run; after that they are managed on the **Networks** page.
5. Cron (every minute) - expires sessions and makes sure controllers drop guests whose time is over:
   `* * * * * php /var/www/unifi-portal/bin/maintenance.php`

## UniFi controller setup (do this on each controller / site)
Settings -> Guest Hotspot (or Hotspot -> Hotspot Portal):
- Authentication: **External Portal Server** -> the IP or hostname of this server.
- Pre-Authorization Access: allow this server's IP (if it is not added automatically).
- Enable the guest portal on the guest SSID (e.g. "DDC Free Wifi").
Guests are redirected to `/guest/s/<site-id>/?id=<mac>&ap=<ap-mac>&ssid=...&url=...`.
**Site IDs must be unique across controllers** (yours are: default, z1qqymab, 5n1pz4m9, dqevxwsa) - the portal uses the site ID to find the network.

## How it works
- **Voucher types**: *Single-use* = one device, once. *Multiple-use* = up to N devices, each device once. *Unlimited* = any number of devices (each is still held back by the cooldown). Each device gets its own timer.
- **Time runs on the wall clock** from redemption (`expires_at`) whether or not the device is connected. On return the portal re-authorizes the device for the time that is left; once it is over the device is in cooldown.
- **Cooldown** (User Settings): after time is used up (or an admin unauthorizes the device) the device cannot enter a voucher until the cooldown ends. *Reset cooldown* in the User List clears it. It is per device (MAC), across all networks.
- **Voucher codes** are always 6 digits. The guest portal shows six boxes (digits only, paste works). There is no verification code.
- **User List**: pick a network and SSID. *Unauthorize* disconnects and ends the session (cooldown starts), *Reconnect* (every device) re-authorizes for the remaining voucher time (also restores an unauthorized session that still has time) and forces the device to reconnect; *Unauthorize* is shown only for devices connected through a guest SSID, *Reset cooldown* lets the user enter a new voucher.
- Wrong-code lockout (User Settings) is applied per device and per IP.

## Notes
- If a phone uses a **private/random MAC**, each SSID/network may look like a new device and bypass the cooldown. Turn private MAC off for the guest SSID on the phones, or accept it.
- Controller passwords are stored in plain text in `data/portal.sqlite` (the API needs them). Keep `data/` outside the web root and use a dedicated, least-privilege controller account.
- Serve `/admin/` over HTTPS if it is reachable from outside.
- Works with classic controllers (port 8443) and UniFi OS consoles (auto-detected).

## Development / testing
`dev/mock_unifi.php` is a fake controller; `dev/router.php` is a router for `php -S`:
```
php -S 127.0.0.1:9443 dev/mock_unifi.php
php -S 127.0.0.1:8080 -t public dev/router.php
```
Environment: `PORTAL_DATA_DIR` (data folder), `PORTAL_TZ` (default Asia/Manila).
