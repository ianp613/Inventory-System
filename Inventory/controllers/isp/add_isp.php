<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $isp = new ISP;
            $isp->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $isp->uid = $data["uid"];
            $isp->name = $data["name"] ? $data["name"] : "-";
            $isp->isp_name = $data["isp_name"] ? $data["isp_name"] : "-";
            $isp->wan_ip = $data["wan_ip"] ? $data["wan_ip"] : "-";
            $isp->subnet = $data["subnet"] ? $data["subnet"] : "-";
            $isp->gateway = $data["gateway"] ? $data["gateway"] : "-";
            $isp->dns1 = $data["dns1"] ? $data["dns1"] : "-";
            $isp->dns2 = $data["dns2"] ? $data["dns2"] : "-";
            $isp->webmgmtpt = $data["webmgmtpt"] ? $data["webmgmtpt"] : "-";

            DB::save($isp);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has added an ISP \"".$data["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "ISP has been saved."
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