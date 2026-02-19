<?php
    class UNIFI_MAC_STATUS {
        public static function status($config, $ssid) {
            $response = [
                "site" => [],
                "status" => [],
                "message" => []
            ];

            foreach ($config->unifi as $conf) {

                $controllerUrl = 'https://' . $conf->host . ':' . $conf->port;
                $siteId = $conf->site_id;
                $username = $conf->username;
                $password = $conf->password;

                array_push($response["site"], $conf->name);

                // ✅ 1. Check controller reachable
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $controllerUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                curl_exec($ch);

                if (curl_errno($ch)) {
                    array_push($response["status"], "danger");
                    array_push($response["message"], "⚠ Controller unreachable, please check unifi controller and try again.");
                    curl_close($ch);
                    continue;
                }

                curl_close($ch);

                // ✅ 2. Login
                $cookie = sys_get_temp_dir() . '/unifi_' . uniqid() . '.txt';

                $loginData = json_encode([
                    'username' => $username,
                    'password' => $password
                ]);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$controllerUrl/api/login");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_exec($ch);
                curl_close($ch);

                // ✅ 3. Get WLAN configs
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$controllerUrl/api/s/$siteId/rest/wlanconf");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $wlanResponse = curl_exec($ch);
                curl_close($ch);

                $meta = json_decode($wlanResponse);

                if (!$meta || $meta->meta->rc != "ok") {
                    array_push($response["status"], "danger");
                    array_push($response["message"], "⚠ Failed to retrieve WLAN configs, please try again.");
                    continue;
                }

                // ✅ 4. Find SSID
                $target = null;
                foreach ($meta->data as $wlan) {
                    if (isset($wlan->name) && strtolower($wlan->name) === strtolower($ssid)) {
                        $target = $wlan;
                        break;
                    }
                }

                if (!$target) {
                    array_push($response["status"], "warning");
                    array_push($response["message"], "⚠ SSID '$ssid' not found.");
                    continue;
                }

                // ✅ 5. Build formatted message
                $enabled      = ($target->enabled ?? false) ? "True" : "False";
                $fastRoaming  = ($target->fast_roaming_enabled ?? false) ? "True" : "False";
                $macFilter    = ($target->mac_filter_enabled ?? false) ? "True" : "False";
                $securityKey  = $target->x_passphrase;

                $formattedMessage =
                    $ssid."\n".
                    "Wireless Network Enabled: $enabled\n" .
                    "Fast Roaming Enabled: $fastRoaming\n" .
                    "Mac Filter Enabled: $macFilter\n" .
                    "Security Key: $securityKey";

                array_push($response["status"], "success");
                array_push($response["message"], $formattedMessage);
            }

            echo json_encode($response);
        }
    }

    session_start();
    header('Content-Type: application/json');
    include("../../exeptionhandler.php");
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $unifi_config = json_decode(file_get_contents("../../assets/files/unifi-mac.config.json"));

    $wifi = new Wifi;
    $ssid = DB::find($wifi,$data["password_mac_ssid"]);

    UNIFI_MAC_STATUS::status($unifi_config,$ssid[0]["name"]);
?>