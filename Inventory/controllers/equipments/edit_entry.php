<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $entry = new Equipment_Entry;
            $entry = DB::prepare($entry,$data["id"]);

            $equipment = new Equipment;
            $equipment_temp = DB::find($equipment,$data["eid"]);
            $entry_description_temp = $entry->description;
            $entry->description = $data["description"];
            $entry->model_no = $data["model_no"] ? strtoupper($data["model_no"]) : "-";
            $entry->barcode = $data["barcode"] ? strtoupper($data["barcode"]) : "-";
            $entry->specifications = $data["specifications"] ? $data["specifications"] : "-";
            $entry->status = $data["status"] ? $data["status"] : "-";
            $entry->remarks = $data["remarks"] ? $data["remarks"] : "-";
            DB::update($entry);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has updated an information of entry \"".$data["description"]."\" from equipment \"".$equipment_temp[0]["name"]."\".";
            if($_SESSION["log1"] != $log->log){
                $_SESSION["log1"] = $log->log;
                DB::save($log);
            }

            if($entry_description_temp != $data["description"]){
                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has updated an entry description from \"".$entry_description_temp."\" to \"".$data["description"].".\"";
                if($_SESSION["log2"] != $log->log){
                    $_SESSION["log2"] = $log->log;
                    DB::save($log);
                }    
            }

            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "Entry has been updated."
            ];
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => null,
                "message" => "Please provide equipment name."
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