<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_isp_router:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $isp = new ISP;
    $isp = $_SESSION["g_id"] ? DB::where($isp,"gid","=",$_SESSION["g_id"]) : DB::all($isp);
    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);
    $response = [
        "status" => true,
        "isp" => $isp,
        "router" => $router
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>