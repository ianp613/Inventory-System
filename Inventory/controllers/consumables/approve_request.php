<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $total_amount_requested = 0;
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Something went wrong."
    ];

    if($_SESSION["c_authority"] != "true"){
        $response["status"] = false;
        $response["type"] = "error";
        $response["message"] = "You do not have permission to approve this request.";
    }else{
        $consumable_request = new Consumable_Request;
        $request = DB::find($consumable_request,$data["id"]);

        $group_request = DB::where2($consumable_request,"gid","=",$request[0]["gid"],"status","=","Approved");

        foreach ($group_request as $gr) {
            $total_amount_requested += (int) $gr["requested_quantity"];
        }
        $total_amount_requested += (int) $request[0]["requested_quantity"];

        $consumable = new Consumables;
        $consumable = DB::find($consumable,$request[0]["cid"]);

        if($total_amount_requested > (int) $consumable[0]["stock"]){
            $response["status"] = false;
            $response["type"] = "warning";
            $response["size"] = "lg";
            $response["message"] = "Insufficient stock for the quantity requested. <br> Please check if there are approved request but are unclaimed.";
        }else{
            $request = DB::prepare($consumable_request,$data["id"]);
            $request->status = "Approved";
            DB::update($request);

            $response["status"] = true;
            $response["type"] = "success";
            $response["message"] = "Request has been approved.";
        }
    }

    echo json_encode($response);
?>