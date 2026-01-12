<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    session_start();
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["name"]) {
            $equipment = new Equipment;
            $bol = true;
            $equip = DB::where($equipment,"name","=",$data["name"]);
            if(count($equip)){
                foreach ($equip as $eq) {
                    if($eq["gid"] == $_SESSION["g_id"]){
                        $bol = false;
                    }
                }
            }
            // if($bol){
                $equipment->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $equipment->uid = $data["uid"];
                $equipment->name = $data["name"];
                DB::save($equipment);
                
                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has added an equipment \"".$data["name"]."\".";
                if($_SESSION["log"] != $log->log){
                    $_SESSION["log"] = $log->log;
                    DB::save($log);
                }

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Equipment has been saved."
                ]; 
            // }else{
            //     $response = [
            //         "status" => false,
            //         "type" => "warning",
            //         "size" => null,
            //         "message" => "Equipment already exist."
            //     ];    
            // }
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => "lg",
                "message" => "Please select or provide equipment name."
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