<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $consumables = new Consumables;
            $bol = true;

            $consumable_temp = DB::where($consumables,"description","=",$data["description"]);
            foreach ($consumable_temp as $ct) {
                if($ct["description"] == $data["description"] && $ct["id"] != $data["id"]){
                    $bol = false;
                }
            }

            // if($bol){
                $consumables = DB::prepare($consumables,$data["id"]);
                $consumables_consumable_temp = $consumables->description;
                $consumables->description = $data["description"];
                $consumables->measurement = $data["measurement"];
                $consumables->unit = $data["unit"];
                $consumables->stock = $data["stock"];
                $consumables->restock_point = $data["restock_point"];

                DB::update($consumables);

                $wifi = new Wifi;
                $wifi_temp = DB::find($wifi,$data["id"]);

                if($consumables_consumable_temp != $data["description"]){
                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has updated a consumable description from \"".$consumables_consumable_temp."\" to \"".$data["description"].".\"";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    }    
                }


                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Consumable has been updated.",
                ];
            // }else{
            //     $response = [
            //         "status" => false,
            //         "type" => "warning",
            //         "size" => null,
            //         "message" => "Consumable \"".$data["description"]."\" already exist.",
            //     ];
            // }
            
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