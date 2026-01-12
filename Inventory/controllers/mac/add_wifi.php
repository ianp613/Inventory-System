<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["wifi_name"]) {
            $wifi = new Wifi;
            $bol = DB::validate($wifi,"name",$data["wifi_name"]);
            // if($bol){
                $wifi->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $wifi->uid = $data["uid"];
                $wifi->name = $data["wifi_name"];
                $wifi->password = $data["wifi_password"] ? $data["wifi_password"] : "-";
                DB::save($wifi);


                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has added a wifi \"".$data["wifi_name"]."\".";
                if($_SESSION["log1"] != $log->log){
                    $_SESSION["log1"] = $log->log;
                    DB::save($log);
                }

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Wifi has been saved."
                ]; 
            // }else{
            //     $response = [
            //         "status" => false,
            //         "type" => "warning",
            //         "size" => null,
            //         "message" => "Wifi already exist."
            //     ];    
            // }
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