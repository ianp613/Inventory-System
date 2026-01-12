<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["name"]) {
            if($data["from"] && $data["to"]){
                if($data["subnet"]){
                    $fromParts = explode('.', $data["from"]);
                    $toParts = explode('.', $data["to"]);

                    if (count($fromParts) == 4 && count($toParts) == 4 && is_numeric($fromParts[3]) && is_numeric($toParts[3])) {
                        $trd_octets = true;
                        for ($i = 0; $i < 3; $i++) {
                            if ($fromParts[$i] !== $toParts[$i]) {
                                $trd_octets = false;
                            }
                        }
                        if($trd_octets){
                            if ((int)$fromParts[3] <= (int)$toParts[3] && (int)$fromParts[3] != (int)$toParts[3]) {
                                $ip_count = ((int)$toParts[3] + 1) - (int)$fromParts[3];
                                if($ip_count <= 1000){
                                    $ip_network = new IP_Network;
                                    $bol = DB::validate($ip_network,"name",$data["name"]);
                                    if($bol){
                                        $ip_network->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                                        $ip_network->uid = $data["uid"];
                                        $ip_network->name = $data["name"];
                                        $ip_network->from = $data["from"];
                                        $ip_network->to = $data["to"];
                                        $ip_network->subnet = $data["subnet"];
                                        $ip_network->rid = $data["gateway"];
                                        DB::save($ip_network);

                                        $nid = DB::where($ip_network,"name","=",$data["name"])[0]["id"];

                                        $ip_address = new IP_Address;
                                        
                                        for ($i = $fromParts[3]; $i <= $toParts[3]; $i++) { 
                                            $ip_address->nid = $nid;
                                            $ip_address->ip = $fromParts[0].".".$fromParts[1].".".$fromParts[2].".".$i;
                                            $ip_address->subnet = $data["subnet"];
                                            $ip_address->hostname = "-";
                                            $ip_address->site = "-";
                                            $ip_address->server = "-";
                                            $ip_address->state = "DOWN";
                                            $ip_address->status = "UNASSIGNED";
                                            $ip_address->remarks = "-";
                                            $ip_address->webmgmtpt = "-";
                                            $ip_address->username = "-";
                                            $ip_address->password = "-";
                                            DB::save($ip_address);

                                            $log = new Logs;
                                            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                                            $log->uid = $_SESSION["userid"];
                                            $log->log = $_SESSION["name"]." has added a network \"".$data["name"]."\".";
                                            if($_SESSION["log1"] != $log->log){
                                                $_SESSION["log1"] = $log->log;
                                                DB::save($log);
                                            }

                                            if($data["gateway"] != "-"){
                                                $router = new Routers;
                                                $router_temp = DB::find($router,$data["gateway"]);

                                                $log = new Logs;
                                                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                                                $log->uid = $_SESSION["userid"];
                                                $log->log = $_SESSION["name"]." has connected network \"".$data["name"]."\" to router \"".$router_temp[0]["name"]."\".";
                                                if($_SESSION["log2"] != $log->log){
                                                    $_SESSION["log2"] = $log->log;
                                                    DB::save($log);
                                                }    
                                            }
                                            
                                        }
                                        
                                        $response = [
                                            "status" => true,
                                            "type" => "success",
                                            "size" => null,
                                            "message" => "Network has been saved."
                                        ]; 
                                    }else{
                                        $response = [
                                            "status" => false,
                                            "type" => "warning",
                                            "size" => null,
                                            "message" => "Network already exist."
                                        ];    
                                    }
                                }else{
                                    $response = [
                                        "status" => false,
                                        "type" => "error",
                                        "size" => null,
                                        "message" => "The IP Address exceeds the limits of 1000 IP per network only. You can use network name like \"Network [1] 1-1000\", \"Network [2] 1001-2000\" and so on."
                                    ];     
                                }
                            }else{
                                $response = [
                                    "status" => false,
                                    "type" => "error",
                                    "size" => null,
                                    "message" => "The fourth octet of 'FROM' must be greater than the fourth octet of 'TO'."
                                ];    
                            }
                        }else{
                            $response = [
                                "status" => false,
                                "type" => "error",
                                "size" => null,
                                "message" => "The first three octets of the IP addresses must be the same."
                            ];
                        }
                    }else{
                        $response = [
                            "status" => false,
                            "type" => "error",
                            "size" => null,
                            "message" => "Both IP addresses must contain exactly four octets."
                        ];
                    }     
                }else{
                    $response = [
                        "status" => false,
                        "type" => "warning",
                        "size" => null,
                        "message" => "Please provide subnet mask."
                    ];
                }
                
            }else{
                $response = [
                    "status" => false,
                    "type" => "error",
                    "size" => null,
                    "message" => "Please provide IP addresses."
                ];
            }
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => null,
                "message" => "Please provide network name."
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