<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $cache_key = "icore_routers_edit:" . $data["id"] . ":" . ($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);

    $network = new IP_Network;
    $network = DB::find($network,$data["id"]);

    $response = [
        "router" => $router,
        "network" => $network
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>