<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_wifi:all";
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $wifi = new Wifi;
    $group = new User_Group;
    $group = DB::where($group,"group_name","like","wifi");

    $wifis = count($group) ? DB::where($wifi,"gid","=",$group[0]["id"]) : [];
    
    $response = [
        "status" => true,
        "wifis" => $wifis,
        "g_id" => count($group) ? $group[0]["id"] : null
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>