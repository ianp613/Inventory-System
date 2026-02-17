<?php
    class UNIFI_MAC{
        public static function register($config,$ssid,$client_mac){
            $response = [
                "site" => [],
                "status" => [],
                "message" => []
            ];
            foreach ($config->unifi as $conf) {
                $controllerUrl = 'https://'.$conf->host.':'.$conf->port;
                $siteId = $conf->site_id;
                $username = $conf->username;
                $password = $conf->password;
                array_push($response["site"],$conf->name);


                // Check if controller is reachable
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $controllerUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);           // no body, faster
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);            // total timeout
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);     // connection timeout
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                curl_exec($ch);

                if (curl_errno($ch)) {
                    array_push($response["status"], "danger");
                    array_push($response["message"], "⚠ Controller unreachable.");
                    curl_close($ch);
                    continue;   // 🔥 skip to next server
                }

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $client_mac = strtolower(trim($client_mac ?? ''));
                // Validate MAC address
                if (!preg_match('/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/', $client_mac)) {
                    array_push($response["status"],"warning");
                    array_push($response["message"],"⚠ Invalid MAC address");
                    continue;
                }

                // Step 1: Login
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
                curl_setopt($ch, CURLOPT_COOKIEJAR, 'unifi_cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE, 'unifi_cookie.txt');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_exec($ch);
                curl_close($ch);

                // Step 2: Get WLAN configs
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$controllerUrl/api/s/$siteId/rest/wlanconf");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_COOKIEFILE, 'unifi_cookie.txt');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $clientsResponse = curl_exec($ch);
                curl_close($ch);

                $meta = json_decode($clientsResponse);

                if ($meta->meta->rc != "ok") {
                    array_push($response["status"],"danger");
                    array_push($response["message"],"⚠ Failed to retrieve WLAN configs.");
                    continue;
                }

                // Step 3: Find SSID by name (case-insensitive)
                $target = null;
                foreach ($meta->data as $wlan) {
                if (isset($wlan->name) && strtolower($wlan->name) === strtolower($ssid)) {
                    $target = $wlan;
                    break;
                }
                }

                if (!$target) {
                    array_push($response["status"],"warning");
                    array_push($response["message"],"⚠ SSID '$ssid' not found.");
                    continue;
                }

                $wlanId = $target->_id;


                // Step 4: Check MAC filtering enabled
                $macFilterEnabled = $target->mac_filter_enabled ?? false;
                $macFilterPolicy  = $target->mac_filter_policy ?? "none";
                $macList          = $target->mac_filter_list ?? [];

                if (!$macFilterEnabled) {
                    array_push($response["status"],"warning");
                    array_push($response["message"],"⚠ MAC filtering is not enabled on SSID '$ssid'.");
                    continue;
                }

                // Step 5: Check if MAC exists in filter list
                $exists = false;
                foreach ($macList as $mac) {
                if (strtolower($mac) === strtolower($client_mac)) {
                    $exists = true;
                    break;
                }
                }

                if ($exists) {
                    array_push($response["status"],"warning");
                    array_push($response["message"],"⚠ MAC $client_mac already exists in SSID '$ssid' filter list.");
                    continue;
                }

                // Step 6: Add MAC to filter list
                $macList[] = strtolower($client_mac);
                $target->mac_filter_list = array_values($macList); // reindex
                $target->mac_filter_enabled = true; // ensure still enabled
                $target->mac_filter_policy = $macFilterPolicy; // keep existing policy

                $updatePayload = json_encode($target);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$controllerUrl/api/s/$siteId/rest/wlanconf/$wlanId");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $updatePayload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_COOKIEFILE, 'unifi_cookie.txt');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $updateResponse = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $updateResult = json_decode($updateResponse);

                if ($updateResult && $updateResult->meta->rc === "ok" && $httpCode === 200) {
                    array_push($response["status"],"success");
                    array_push($response["message"],"✅ MAC $client_mac successfully added to SSID '$ssid' filter list.");
                    continue;
                } else {
                    array_push($response["status"],"danger");
                    array_push($response["message"],"⚠ Failed to update MAC filter list.");
                    continue;
                }
            }
            echo json_encode($response);
        }
    }



    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $unifi_config = json_decode(file_get_contents("../../assets/files/unifi-mac.config.json"));

    $wifi = new Wifi;
    $ssid = DB::find($wifi,$data["mac_ssid"]);

    $mac = new MAC_Address;
    if($data["g_id"]){
        $mac->gid = $data["g_id"];
        $mac->uid = "_*";
        $mac->wid = $ssid[0]["id"];
        $mac->mac = $data["mac_address"] ? $data["mac_address"] : "-";
        $mac->name = $data["mac_name"] ? $data["mac_name"] : "-";
        $mac->device = $data["mac_device"] ? $data["mac_device"] : "-";
        $mac->project = $data["mac_project"] ? $data["mac_project"] : "-";
        $mac->location = $data["mac_location"] ? $data["mac_location"] : "-";
        $mac->remarks = $data["mac_remarks"] ? $data["mac_remarks"] : "-";

        if (preg_match('/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/', $data["mac_address"])) {
            $bol = true;
            $mac_temp = DB::where($mac,"wid","=",$ssid[0]["id"]);
            foreach ($mac_temp as $mt) {
                if($mt["mac"] == $data["mac_address"]){
                    $bol = false;
                }
            }
            if($bol){
                DB::save($mac);
            }

            $user = new User;
            $user = DB::where($user,"name","=",$data["mac_register_by"])[0];

            $log = new Logs;
            $log->gid = $data["g_id"];
            $log->uid = $user["id"];
            $log->log = $user["name"]." has registered a MAC \"".$data["mac_address"]."\" to \"".$ssid[0]["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }
        }

        
    }
    UNIFI_MAC::register($unifi_config,$ssid[0]["name"],$data["mac_address"]);
?>