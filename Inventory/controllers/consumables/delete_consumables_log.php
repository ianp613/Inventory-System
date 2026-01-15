<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        $group = new User_Group;
        $group = DB::find($group,$_SESSION["g_id"]);

        $log = new Logs;
        $log->gid = $_SESSION["g_id"];
        $log->uid = $_SESSION["userid"];
        $log->log = $_SESSION["name"]." has cleared logs for \"".$group[0]["group_name"]."\" group.";
        if($_SESSION["log"] != $log->log){
            $_SESSION["log"] = $log->log;
            DB::save($log);
        }

        $consumable_log = new Consumable_Log;
        $consumable_log_temp = DB::where($consumable_log,"gid","=",$_SESSION["g_id"]);

        foreach ($consumable_log_temp as $clog) {
            DB::delete($consumable_log,$clog["id"]);
        }

        $response = [
            "status" => true,
            "type" => "info",
            "size" => "lg",
            "message" => "Logs for all consumables of \"".$group[0]["group_name"]."\" group has been cleared."
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