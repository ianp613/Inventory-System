<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        $isp = new ISP;
        $isp = DB::prepare($isp,$data["id"]);
        $isp_name_temp = $isp->name;
        $isp->name = $data["name"] ? $data["name"] : "-";
        $isp->isp_name = $data["isp_name"] ? $data["isp_name"] : "-";
        $isp->wan_ip = $data["wan_ip"] ? $data["wan_ip"] : "-";
        $isp->configuration = $data["configuration"] ? $data["configuration"] : "-";
        DB::update($isp);

        $log = new Logs;
        $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $log->uid = $_SESSION["userid"];
        $log->log = $_SESSION["name"]." has updated an information of ISP \"".$data["name"]."\".";
        if($_SESSION["log1"] != $log->log){
            $_SESSION["log1"] = $log->log;
            DB::save($log);
        }

        if($isp_name_temp != $data["name"]){
            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has updated an ISP name from \"".$isp_name_temp."\" to \"".$data["name"].".\"";
            if($_SESSION["log2"] != $log->log){
                $_SESSION["log2"] = $log->log;
                DB::save($log);
            }    
        }

        $g_id = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $redis->del("icore_current_configuration:all");
        $redis->del("icore_isp_router:all" . $g_id);

        $response = [
            "status" => true,
            "type" => "success",
            "size" => null,
            "message" => "ISP has been updated.",
        ];
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