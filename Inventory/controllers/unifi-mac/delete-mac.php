<?php
    class UNIFI_MAC_DELETE {
        public static function delete($config, $ssid, $client_mac) {
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

                // ✅ 1. Check if controller reachable
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $controllerUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 7);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
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

                // ✅ 2. Validate MAC
                $client_mac = strtolower(trim($client_mac ?? ''));
                if (!preg_match('/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/', $client_mac)) {
                    array_push($response["status"], "warning");
                    array_push($response["message"], "⚠ Invalid MAC address.");
                    continue;
                }

                // ✅ 3. Login
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

                // ✅ 4. Get WLAN configs
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$controllerUrl/api/s/$siteId/rest/wlanconf");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $clientsResponse = curl_exec($ch);
                curl_close($ch);

                $meta = json_decode($clientsResponse);

                if (!$meta || $meta->meta->rc != "ok") {
                    array_push($response["status"], "danger");
                    array_push($response["message"], "⚠ Failed to retrieve WLAN configs, please try again.");
                    continue;
                }

                // ✅ 5. Find SSID
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

                $wlanId = $target->_id;
                $macList = $target->mac_filter_list ?? [];

                // ✅ 6. Check if MAC exists
                if (!in_array($client_mac, array_map('strtolower', $macList))) {
                    array_push($response["status"], "warning");
                    array_push($response["message"], "⚠ MAC $client_mac not found in SSID '$ssid'.");
                    continue;
                }

                // ✅ 7. Remove MAC
                $macList = array_filter($macList, function ($mac) use ($client_mac) {
                    return strtolower($mac) !== $client_mac;
                });

                $target->mac_filter_list = array_values($macList);

                // ✅ 8. Update controller
                $updatePayload = json_encode($target);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$controllerUrl/api/s/$siteId/rest/wlanconf/$wlanId");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $updatePayload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $updateResponse = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $updateResult = json_decode($updateResponse);

                if ($updateResult && $updateResult->meta->rc === "ok" && $httpCode === 200) {
                    array_push($response["status"], "success");
                    array_push($response["message"], "✅ MAC $client_mac successfully removed from SSID '$ssid'.");
                } else {
                    array_push($response["status"], "danger");
                    array_push($response["message"], "⚠ Failed to update MAC filter list.");
                }
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
    $ssid = DB::find($wifi,$data["delete_mac_ssid"]);

    UNIFI_MAC_DELETE::delete($unifi_config,$ssid[0]["name"],$data["delete_mac_address"]);
?>