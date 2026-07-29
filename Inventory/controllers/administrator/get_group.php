<?php
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_group:all";
    $cache_data = $redis->get($cache_key);

    if ($cache_data !== null) {
        echo $cache_data;
        exit;
    }

    $groups = new User_Group;
    $groups = DB::all($groups);
    $response = [
        "status" => true,
        "groups" => $groups
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>