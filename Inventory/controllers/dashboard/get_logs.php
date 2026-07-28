<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);

    $cache_key = "icore_log:" . $data["id"];
    $cache_data = $redis->get($cache_key);

    if ($cache_data !== null) {
        echo $cache_data;
        exit;
    }

    $consumable_log = new Consumable_Log;
    $response = DB::where($consumable_log, "cid", "=", $data["id"]);

    $redis->setex($cache_key, 300, json_encode($response));
    echo json_encode($response);
?>