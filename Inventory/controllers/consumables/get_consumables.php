<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $cache_key = "icore_consumable:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $consumables = new Consumables;
    $consumables = $_SESSION["g_id"] ? DB::where($consumables,"gid","=",$_SESSION["g_id"]) : DB::all($consumables);

    $response = [
        "status" => true,
        "consumables" => $consumables,
    ];

    $redis->setex($cache_key,300,json_encode($response));
        
    echo json_encode($response);
?>