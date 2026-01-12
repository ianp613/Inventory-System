<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["id"]) {
        $mac = new MAC_Address;
        $response = [
            "status" => true,
            "type" => "info",
            "size" => null,
            "message" => "Edit MAC Address with ID ".$data["id"],
            "mac" => DB::find($mac,$data["id"])
        ];
    }else{
        $response = [
            "status" => false,
            "type" => "error",
            "size" => null,
            "message" => "Entry not found."
        ];
    }
    echo json_encode($response);
?>