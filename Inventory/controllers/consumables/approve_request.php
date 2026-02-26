<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

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
        $request = DB::prepare($consumable_request,$data["id"]);
        $request->status = "Approved";
        DB::update($request);

        $response["status"] = true;
        $response["type"] = "success";
        $response["message"] = "Request has been approved.";
    }

    echo json_encode($response);
?>