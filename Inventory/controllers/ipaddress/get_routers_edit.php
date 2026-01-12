<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);

    $network = new IP_Network;
    $network = DB::find($network,$data["id"]);

    $response = [
        "router" => $router,
        "network" => $network
    ];
    echo json_encode($response);
?>