<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Something went wrong.",
    ];

    if($_SESSION["c_authority"] != "true"){
        $response["status"] = false;
        $response["type"] = "error";
        $response["message"] = "You do not have permission to do this action.";
    }else{
        if($_SESSION["g_member"]){
            $consumable_request = new Consumable_Request;
            $request = DB::find($consumable_request,$data["id"]);

            $consumable = new Consumables;
            $consumables = DB::find($consumable,$request[0]["cid"]);

            if((int) $consumables[0]["stock"] >= (int) $request[0]["requested_quantity"]){
                $consumabl_log = new Consumable_Log;
                $consumabl_log->gid = $request[0]["gid"];
                $consumabl_log->uid = $request[0]["uid"];
                $consumabl_log->cid = $request[0]["cid"];
                $consumabl_log->date = $request[0]["date"];
                $consumabl_log->time = $request[0]["time"];
                $consumabl_log->quantity_deduction = $request[0]["requested_quantity"];
                $consumabl_log->remarks = $request[0]["remarks"];
                DB::save($consumabl_log);

                $consumables = DB::prepare($consumable,$request[0]["cid"]);
                $consumables->stock = (int) $consumables->stock - (int) $request[0]["requested_quantity"];
                DB::update($consumables);

                DB::delete($consumable_request,$data["id"]);

                $response["status"] = true;
                $response["type"] = "success";
                $response["message"] = "Request has been saved to log.";
            }else{
                $response["status"] = false;
                $response["type"] = "warning";
                $response["message"] = "Insufficient stock for the quantity requested.";
            }
        }else{
            $response = [
                "status" => false,
                "type" => "info",
                "size" => null,
                "message" => "Please operate as group member."
            ];
        }
    }
    echo json_encode($response);
?>