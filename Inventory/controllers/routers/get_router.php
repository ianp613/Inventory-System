<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);
    $response = [
        "status" => true,
        "router" => $router
    ];    
    echo json_encode($response);
?>