<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["id"]){
            $mac = new MAC_Address;
            $mac_temp = DB::where($mac,"wid","=",$data["id"]);
            foreach ($mac_temp as $m) {
                DB::delete($mac,$m["id"]);
            }

            $wifi = new Wifi;
            $wifi_temp = DB::find($wifi,$data["id"]);
            DB::delete($wifi,$data["id"]);

            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Wifi has been deleted."
            ];

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted a wifi \"".$wifi_temp[0]["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }
        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Network not found."
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