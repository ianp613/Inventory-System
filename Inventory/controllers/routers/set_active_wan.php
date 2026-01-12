<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Something went wrong."
    ];

    if($_SESSION["g_member"]){
        if($data["id"]) {
            $router = new Routers;

            $router = DB::prepare($router,$data["id"]);
            $router_active_temp = $router->active;
            $router->active = $data["active_wan"];
            DB::update($router);

            if($data["active_wan"] != "-"){
                if($router_active_temp != "-"){
                    $isp = new ISP;
                    $isp_temp1 = DB::find($isp,$router_active_temp);
                    $isp_temp2 = DB::find($isp,$data["active_wan"]);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has remove ISP \"".$isp_temp1[0]["name"]."\" and set ISP \"".$isp_temp2[0]["name"]."\" as ACTIVE for router \"".$router->name."\".";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    }
                }else{
                    $isp = new ISP;
                    $isp_temp = DB::find($isp,$data["active_wan"]);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has set ISP \"".$isp_temp[0]["name"]."\" as ACTIVE for router \"".$router->name."\".";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    }    
                }
            }else{
                if($router_active_temp != "-"){
                    $isp = new ISP;
                    $isp_temp = DB::find($isp,$router_active_temp);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has remove ISP \"".$isp_temp[0]["name"]."\" as ACTIVE for router \"".$router->name."\".";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    } 
                }
            }
                
            
            $router = DB::find($router,$data["id"])[0];
            
            $wan1 = [];
            $wan2 = [];

            $isp = new ISP;

            if($router["wan1"] != "-"){
                $wan1 = DB::find($isp,$router["wan1"]);
            }
            if($router["wan2"] != "-"){
                $wan2 = DB::find($isp,$router["wan2"]);
            }
            $response = [
                "status" => true,
                "router" => $router,
                "wan1" => $wan1,
                "wan2" => $wan2
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