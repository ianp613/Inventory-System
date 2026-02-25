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

    $consumable_request = new Consumable_Request;
    DB::delete($consumable_request,$data["id"]);

    $response["status"] = true;
    $response["type"] = "info";
    $response["message"] = "Request has been canceled.";
    echo json_encode($response);
?>