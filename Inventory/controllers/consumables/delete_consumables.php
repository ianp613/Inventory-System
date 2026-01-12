<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["id"]){
            $consumable = new Consumables;
            $consumable_temp = DB::find($consumable,$data["id"]);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted consumable \"".$consumable_temp[0]["description"]."\" with code \"".$consumable_temp[0]["code"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $consumable_log = new Consumable_Log;
            $consumable_log_temp = DB::where($consumable_log,"cid","=",$data["id"]);

            foreach ($consumable_log_temp as $clog) {
                DB::delete($consumable_log,$clog["id"]);
            }

            DB::delete($consumable,$data["id"]);
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Consumable has been deleted."
            ];
        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Consumable not found."
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