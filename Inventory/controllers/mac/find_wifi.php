<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Wifi not found."
    ];

    if($data["id"]) {
        $wifi = new Wifi;
        if(count(DB::find($wifi,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit Wifi with ID ".$data["id"],
                "wifi" => DB::find($wifi,$data["id"])
            ];
        }
    }
    echo json_encode($response);
?>