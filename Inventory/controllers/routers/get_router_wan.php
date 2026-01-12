<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Something went wrong."
    ];

    if($data["id"]) {
        $router = new Routers;
        $router = DB::find($router,$data["id"])[0];
        
        $wan1 = [];
        $wan2 = [];

        $isp = new ISP;

        if($router["wan1"] != "-"){
            $wan1 = DB::find($isp,$router["wan1"]);
        }
        if($router["wan2"] != "-"){
            $wan2 = DB::find($isp,$router["wan2"]);
        }
        $response = [
            "status" => true,
            "router" => $router,
            "wan1" => $wan1,
            "wan2" => $wan2
        ];
    }
    echo json_encode($response);
?>