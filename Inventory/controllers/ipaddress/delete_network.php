<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["id"]){
            $ip = new IP_Address;
            $ip_temp = DB::where($ip,"nid","=",$data["id"]);
            foreach ($ip_temp as $i) {
                DB::delete($ip,$i["id"]);
            }

            $network = new IP_Network;
            $network_temp = DB::find($network,$data["id"]);
            DB::delete($network,$data["id"]);

            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Network has been deleted."
            ];

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted a network \"".$network_temp[0]["name"]."\".";
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