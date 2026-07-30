<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_wifi:session".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $wifi = new Wifi;
    $wifi = $_SESSION["g_id"] ? DB::where($wifi,"gid","=",$_SESSION["g_id"]) : DB::all($wifi);

    if(count($wifi) > 1){
        array_push($wifi,["id" => "Show All", "name" => "Show All"]);
    }

    $response = [
        "status" => true,
        "wifis" => $wifi
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>