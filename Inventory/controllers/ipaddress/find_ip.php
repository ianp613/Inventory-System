<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Entry not found."
    ];

    if($data["id"]) {
        $ip = new IP_Address;
        if(count(DB::find($ip,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit IP with ID ".$data["id"],
                "ip" => DB::find($ip,$data["id"])
            ];
        }
    }
    echo json_encode($response);
?>