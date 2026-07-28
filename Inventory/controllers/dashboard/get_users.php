<?php
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_user:all";

    // $redis->del($cache_key);

    $cache_data = $redis->get($cache_key);

    if ($cache_data !== null) {
        echo $cache_data;
        exit;
    }

    $users = new User;
    $response = DB::all($users);

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>