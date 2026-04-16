<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $isp = new ISP;
    $isp = $_SESSION["g_id"] ? DB::where($isp,"gid","=",$_SESSION["g_id"]) : DB::all($isp);
    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);
    $response = [
        "status" => true,
        "isp" => $isp,
        "router" => $router
    ];    
    echo json_encode($response);
?>