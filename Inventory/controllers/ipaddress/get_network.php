<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $network = new IP_Network;
    $network = $_SESSION["g_id"] ? DB::where($network,"gid","=",$_SESSION["g_id"]) : DB::all($network);
    $response = [
        "status" => true,
        "networks" => $network
    ];    
    echo json_encode($response);
?>