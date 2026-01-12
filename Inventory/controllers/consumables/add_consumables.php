<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $consumables = new Consumables;
            $bol = true;

            $bol = DB::validate($consumables,"description",$data["description"]) ? true : false;

            // if($bol){
                $consumables->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $consumables->uid = $data["uid"];
                $consumables->code = $_SESSION["consumables_code"];
                $consumables->description = $data["description"];
                $consumables->measurement = $data["measurement"];
                $consumables->unit = $data["unit"];
                $consumables->stock = $data["stock"];
                $consumables->restock_point = $data["restock_point"];

                DB::save($consumables);

                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has added an entry \"".$data["description"];
                if($_SESSION["log"] != $log->log){
                    $_SESSION["log"] = $log->log;
                    DB::save($log);
                }

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Entry has been saved.",
                ];
            // }else{
            //     $response = [
            //         "status" => false,
            //         "type" => "warning",
            //         "size" => null,
            //         "message" => "\"".$data["description"]."\" already exist.",
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