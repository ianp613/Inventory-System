<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["name"]) {
            $network = new IP_Network;
            
                $network = DB::prepare($network,$data["id"]);
                $network_name_temp = $network->name;
                $network->name = $data["name"];
                $network->subnet = $data["subnet"];
                $network_rid_temp = $network->rid;
                $network->rid = $data["gateway"];
                DB::update($network);

                $ip = new IP_Address;
                $ip_temp = DB::where($ip,"nid","=",$data["id"]);
                foreach ($ip_temp as $ip_t) {
                    $ip = DB::prepare($ip,$ip_t["id"]);
                    $ip->subnet = $data["subnet"];
                    $ip->status = $ip_t["status"];
                    DB::update($ip);
                }
                $net = DB::where($network,"name","=",$data["name"]);

                if($network_name_temp != $data["name"]){
                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has updated a network name from \"".$network_name_temp."\" to \"".$data["name"].".\"";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    }    
                }

                if(count($net) > 1){
                    $response = [
                        "status" => true,
                        "type" => "warning",
                        "size" => null,
                        "message" => "Data has been updated. Please note that the ".$data["name"]." network already exists, which may lead to potential conflicts or data integrity issues."
                    ]; 
                }else{
                    $response = [
                        "status" => true,
                        "type" => "success",
                        "size" => null,
                        "message" => "Network has been updated.",
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