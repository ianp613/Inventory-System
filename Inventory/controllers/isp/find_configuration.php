<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "ISP configuration not found."
    ];

    if($data["id"]) {
        $conf = new ISP_Configuration;
        if(count(DB::find($conf,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit ISP configuration with ID ".$data["id"],
                "configuration" => DB::find($conf,$data["id"])
            ];
        }
    }
    echo json_encode($response);
?>