<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_equipment:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $equipment = new Equipment;
    $equipment = $_SESSION["g_id"] ? DB::where($equipment,"gid","=",$_SESSION["g_id"]) : DB::all($equipment);
    $response = [
        "status" => true,
        "equipments" => $equipment
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>