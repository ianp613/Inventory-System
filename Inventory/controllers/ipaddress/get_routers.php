<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_routers:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);

    $redis->setex($cache_key, 300, json_encode($router));

    echo json_encode($router);
?>