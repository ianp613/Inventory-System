<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["id"]){
            $entry = new Equipment_Entry;
            $entry_temp = DB::where($entry,"eid","=",$data["id"]);
            foreach ($entry_temp as $i) {
                DB::delete($entry,$i["id"]);
            }

            $equipment = new Equipment;
            $equipment_temp = DB::find($equipment,$data["id"]);
            DB::delete($equipment,$data["id"]);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted an equipment \"".$equipment_temp[0]["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Equipment has been deleted."
            ];

        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Equipment not found."
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