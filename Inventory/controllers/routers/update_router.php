<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $router = new Routers;
            $router = DB::prepare($router,$data["id"]);
            $router_name_temp = $router->name;
            $router->name = $data["router_name"];
            $router->ip = $data["router_ip"];
            if($router->active != "-"){
                if($router->wan1 == $router->active && $router->wan1 != $data["router_wan1"]){
                    $router->active = "-";
                }
                if($router->wan2 == $router->active && $router->wan2 != $data["router_wan2"]){
                    $router->active = "-";
                }    
            }
            
            $router->subnet = $data["router_subnet"];

            $router_wan1_temp = $router->wan1;
            $router->wan1 = $data["router_wan1"];
            if($router->wan1 != "-"){
                $isp = new ISP;
                $isp_temp = DB::find($isp,$router->wan1);

                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has set ISP \"".$isp_temp[0]["name"]."\" as WAN 1 of router \"".$data["router_name"]."\".";
                if($_SESSION["log1"] != $log->log){
                    $_SESSION["log1"] = $log->log;
                    DB::save($log);
                }
            }

            if($router_wan1_temp != "-" && $router_wan1_temp != $data["router_wan1"]){
                if($data["router_wan1"] != "-"){
                    $isp = new ISP;
                    $isp_temp = DB::find($isp,$router->wan1);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has set ISP \"".$isp_temp[0]["name"]."\" as WAN 1 of router \"".$data["router_name"]."\".";
                    if($_SESSION["log1"] != $log->log){
                        $_SESSION["log1"] = $log->log;
                        DB::save($log);
                    }
                }else{
                    $isp = new ISP;
                    $isp_temp = DB::find($isp,$router_wan1_temp);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has remove ISP \"".$isp_temp[0]["name"]."\" from WAN 1 of router \"".$data["router_name"]."\".";
                    if($_SESSION["log1"] != $log->log){
                        $_SESSION["log1"] = $log->log;
                        DB::save($log);
                    }
                }
            }

            $router_wan2_temp = $router->wan2;
            $router->wan2 = $data["router_wan2"];
            if($router->wan2 != "-"){
                $isp = new ISP;
                $isp_temp = DB::find($isp,$router->wan2);

                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has set ISP \"".$isp_temp[0]["name"]."\" as WAN 2 of router \"".$data["router_name"]."\".";
                if($_SESSION["log2"] != $log->log){
                    $_SESSION["log2"] = $log->log;
                    DB::save($log);
                }
            }

            if($router_wan2_temp != "-" && $router_wan2_temp != $data["router_wan2"]){
                if($data["router_wan2"] != "-"){
                    $isp = new ISP;
                    $isp_temp = DB::find($isp,$router->wan2);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has set ISP \"".$isp_temp[0]["name"]."\" as WAN 2 of router \"".$data["router_name"]."\".";
                    if($_SESSION["log1"] != $log->log){
                        $_SESSION["log1"] = $log->log;
                        DB::save($log);
                    }
                }else{
                    $isp = new ISP;
                    $isp_temp = DB::find($isp,$router_wan2_temp);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has remove ISP \"".$isp_temp[0]["name"]."\" from WAN 2 of router \"".$data["router_name"]."\".";
                    if($_SESSION["log1"] != $log->log){
                        $_SESSION["log1"] = $log->log;
                        DB::save($log);
                    }
                }
            }
            
            DB::update($router);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has updated an information of router \"".$data["router_name"]."\".";
            if($_SESSION["log3"] != $log->log){
                $_SESSION["log3"] = $log->log;
                DB::save($log);
            }

            if($router_name_temp != $data["router_name"]){
                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has updated a router name from \"".$router_name_temp."\" to \"".$data["router_name"].".\"";
                if($_SESSION["log4"] != $log->log){
                    $_SESSION["log4"] = $log->log;
                    DB::save($log);
                }    
            }

            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "Router has been updated."
            ];
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