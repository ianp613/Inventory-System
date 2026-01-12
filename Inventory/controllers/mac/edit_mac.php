<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $mac = new MAC_Address;
            $bol = true;
            $wid = null;

            $mac_temp = DB::where($mac,"mac","=",$data["mac"]);
            foreach ($mac_temp as $mt) {
                if($mt["mac"] == $data["mac"] && $mt["id"] != $data["id"]){
                    $wid = $mt["wid"];
                    $bol = false;
                }
            }

            if($bol){
                $mac = DB::prepare($mac,$data["id"]);
                $mac_mac_temp = $mac->mac;
                $mac->mac = $data["mac"];
                $mac->name = $data["name"] ? $data["name"] : "-";
                $mac->device = $data["device"] ? $data["device"] : "-";
                $mac->project = $data["project"] ? $data["project"] : "-";
                $mac->location = $data["location"] ? $data["location"] : "-";
                $mac->remarks = $data["remarks"] ? $data["remarks"] : "-";

                DB::update($mac);

                $wifi = new Wifi;
                $wifi_temp = DB::find($wifi,$data["id"]);

                if($mac_mac_temp != $data["mac"]){
                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has updated a MAC Address from \"".$mac_mac_temp."\" to \"".$data["mac"].".\"";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    }    
                }

                // $log = new Logs;
                // $log->uid = $_SESSION["userid"];
                // $log->log = $_SESSION["name"]." has add a MAC address \"".$data["mac"]."\" to wifi \"".$wifi_temp[0]["name"]."\".";
                // if($_SESSION["log"] != $log->log){
                //     $_SESSION["log"] = $log->log;
                //     DB::save($log);
                // }

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "MAC address has been updated.",
                ];
            }else{
                $wifi = new Wifi;
                $wifi = DB::find($wifi,$wid);
                $response = [
                    "status" => false,
                    "type" => "warning",
                    "size" => null,
                    "message" => "MAC address \"".$data["mac"]."\" already exist in ".$wifi[0]["name"].".",
                ];
            }
            
        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Something went wrong."
            ];
        }
    }else{
        $response = [
            "status" => false,
            "type" => "info",
            "size" => null,
            "message" => "Please operate as group member."
        ];
    }
    echo json_encode($response);
?>