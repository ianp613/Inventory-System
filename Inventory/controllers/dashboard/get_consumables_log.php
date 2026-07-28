<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_consumable_log:all";
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $consumables_log = new Consumable_Log;
    $consumables_log = $_SESSION["g_id"] ? DB::where($consumables_log,"gid","=",$_SESSION["g_id"]) : [];

    $response = [
        "status" => true,
        "consumables_log" => $consumables_log,
    ];
    $redis->setex($cache_key, 300, json_encode($response));
    echo json_encode($response);
?>