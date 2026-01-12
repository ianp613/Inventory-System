<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => false
    ];

    if($data["id"]) {
        $router = new Routers;
        $wan1 = DB::where($router,"wan1","=",$data["id"]);
        $wan2 = DB::where($router,"wan2","=",$data["id"]);

        $bol = false;

        count($wan1) ? $bol = true : null;
        count($wan2) ? $bol = true : null;

        $response = [
            "status" => $bol,
            "wan1" => $wan1,
            "wan2" => $wan2
        ];
    }
    echo json_encode($response);
?>