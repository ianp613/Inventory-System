<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    
    
    if($_SESSION["g_member"]){
        if($data) {
            $entry = new Equipment_Entry;
            $barcode_ = DB::validate($entry,"barcode",$data["barcode"]);

            if($barcode_){
                $entry->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $entry->uid = $data["uid"];
                $entry->eid = $data["eid"];
                $entry->description = $data["description"];
                $entry->model_no = $data["model_no"] ? strtoupper($data["model_no"]) : "-";
                $entry->barcode = $data["barcode"] ? strtoupper($data["barcode"]) : "-";
                $entry->specifications = $data["specifications"] ? $data["specifications"] : "-";
                $entry->status = $data["status"] ? $data["status"] : "N/A";
                $entry->building = $data["building"] ? $data["building"] : "-";
                $entry->room = $data["room"] ? $data["room"] : "-";
                $entry->project = $data["project"] ? $data["project"] : "-";
                $entry->cabinet = $data["cabinet"] ? $data["cabinet"] : "-";
                $entry->remarks = $data["remarks"] ? $data["remarks"] : "-";

                DB::save($entry);

                $equipment = new Equipment;
                $equipment_temp = DB::find($equipment,$data["eid"]);

                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has add an entry \"".$data["description"]."\" to equipment \"".$equipment_temp[0]["name"]."\".";
                if($_SESSION["log"] != $log->log){
                    $_SESSION["log"] = $log->log;
                    DB::save($log);
                }

                $g_id = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $redis->del("icore_entry:all" . $g_id);
                $redis->del("icore_entry:eid" . $data["eid"]);

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Entry has been saved.",
                    "entry" => DB::all($entry)
                ];    
            }else{
                $response = [
                    "status" => false,
                    "type" => "warning",
                    "size" => null,
                    "message" => "Barcode already exist."
                ];
            }
            
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