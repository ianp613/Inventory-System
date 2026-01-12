<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["wifi_name"]) {
            $wifi = new Wifi;
        
            $wifi = DB::prepare($wifi,$data["id"]);
            $wifi_name_temp = $wifi->name;
            $wifi->name = $data["wifi_name"];
            $wifi->password = $data["wifi_password"] ? $data["wifi_password"] : "-";
            DB::update($wifi);

            $net = DB::where($wifi,"name","=",$data["wifi_name"]);

            if($wifi_name_temp != $data["wifi_name"]){
                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has updated a wifi name from \"".$wifi_name_temp."\" to \"".$data["wifi_name"].".\"";
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
                    "message" => "Data has been updated. Please note that the ".$data["wifi_name"]." wifi already exists, which may lead to potential conflicts or data integrity issues."
                ]; 
            }else{
                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Wifi has been updated.",
                ];
            }
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => null,
                "message" => "Please provide wifi name."
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