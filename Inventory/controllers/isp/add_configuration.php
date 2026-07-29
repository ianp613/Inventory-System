<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $conf = new ISP_Configuration;
            $conf->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $conf->uid = $data["uid"];
            $conf->name = $data["name"] ? $data["name"] : "-";
            $conf->subnet = $data["subnet"] ? $data["subnet"] : "-";
            $conf->gateway = $data["gateway"] ? $data["gateway"] : "-";
            $conf->dns1 = $data["dns1"] ? $data["dns1"] : "-";
            $conf->dns2 = $data["dns2"] ? $data["dns2"] : "-";
            DB::save($conf);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has added an ISP configuration \"".$data["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $g_id = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $redis->del("icore_isp_configuration:all" . $g_id);

            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "ISP configuration has been saved."
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