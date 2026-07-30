<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_router_isp:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);

    $isp = new ISP;
    $isp = DB::all($isp);

    $response = [
        "status" => true,
        "router" => $router,
        "isp" => $isp
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>