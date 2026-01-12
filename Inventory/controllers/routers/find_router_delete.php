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
        $router = DB::find($router,$data["id"]);

        $network = new IP_Network;
        $network = DB::where($network,"rid","=",$data["id"]);
        $response = [
            "status" => true,
            "type" => "info",
            "size" => null,
            "message" => "Delete Router with ID ".$data["id"],
            "router" => $router,
            "network" => $network
        ];
    }
    echo json_encode($response);
?>