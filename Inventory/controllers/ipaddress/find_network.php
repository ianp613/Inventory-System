<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Network not found."
    ];

    if($data["id"]) {
        $network = new IP_Network;
        if(count(DB::find($network,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit IP Network with ID ".$data["id"],
                "network" => DB::find($network,$data["id"])
            ];
        }
    }
    echo json_encode($response);
?>