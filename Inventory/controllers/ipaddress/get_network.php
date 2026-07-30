<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_network:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $network = new IP_Network;
    $network = $_SESSION["g_id"] ? DB::where($network,"gid","=",$_SESSION["g_id"]) : DB::all($network);
    $response = [
        "status" => true,
        "networks" => $network
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>