<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "ISP not found."
    ];

    if($_SESSION["g_member"]){
        if($data["id"]) {
            $router = new Routers;

            $wan1 = DB::where($router,"wan1","=",$data["id"]);
            $wan2 = DB::where($router,"wan2","=",$data["id"]);

            if(count($wan1)){
                foreach($wan1 as $w){
                    $temp = DB::prepare($router,$w["id"]);
                    if($temp->active != "-"){
                        $temp->active == $temp->wan1 ? $temp->active = "-" : null;   
                    }
                    $temp->wan1 = "-";
                    DB::update($temp);
                }
            }
            if(count($wan2)){
                foreach($wan2 as $w){
                    $temp = DB::prepare($router,$w["id"]);
                    if($temp->active != "-"){
                        $temp->active == $temp->wan2 ? $temp->active = "-" : null;   
                    }
                    $temp->wan2 = "-";
                    DB::update($temp);
                }
            }

            $isp = new ISP;
            $isp_temp = DB::find($isp,$data["id"]);
            DB::delete($isp,$data["id"]);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted an ISP \"".$isp_temp[0]["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "ISP has been deleted.",
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