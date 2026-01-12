<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Router not found."
    ];

    if($data["id"]) {
        $router = new Routers;
        if(count(DB::find($router,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit Router with ID ".$data["id"],
                "router" => DB::find($router,$data["id"])
            ];
        }
    }
    echo json_encode($response);
?>