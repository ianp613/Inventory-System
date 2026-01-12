<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $isp = new ISP;

    $isp = $_SESSION["g_id"] ? DB::where($isp,"gid","=",$_SESSION["g_id"]) : DB::all($isp);
    $response = [
        "status" => true,
        "isp" => $isp
    ];    
    echo json_encode($response);
?>