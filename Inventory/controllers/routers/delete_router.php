<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Router not found."
    ];

    if($_SESSION["g_member"]){
        if($data["id"]) {
            $network = new IP_Network;
            $temp = DB::where($network,"rid","=",$data["id"]);
            if(count($temp)){
                foreach ($temp as $n) {
                    $t = DB::prepare($network,$n["id"]);
                    $t->rid = "-";
                    DB::update($t);
                }
            }

            $router = new Routers;
            $router_temp = DB::find($router,$data["id"]);
            DB::delete($router,$data["id"]);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted a router \"".$router_temp[0]["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Router has been deleted.",
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