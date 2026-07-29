<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_isp_configuration:all".($_SESSION["g_id"] ? $_SESSION["g_id"] : "");
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $isp_configuration = new ISP_Configuration;
    $isp_configuration = $_SESSION["g_id"] ? DB::where($isp_configuration,"gid","=",$_SESSION["g_id"]) : DB::all($isp_configuration);
    $response = [
        "status" => true,
        "isp_configuration" => $isp_configuration
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>