<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $isp_configuration = new ISP_Configuration;
    $isp_configuration = $_SESSION["g_id"] ? DB::where($isp_configuration,"gid","=",$_SESSION["g_id"]) : DB::all($isp_configuration);
    $response = [
        "status" => true,
        "isp_configuration" => $isp_configuration
    ];    
    echo json_encode($response);
?>