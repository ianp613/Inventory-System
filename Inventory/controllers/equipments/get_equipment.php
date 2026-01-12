<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $equipment = new Equipment;
    $equipment = $_SESSION["g_id"] ? DB::where($equipment,"gid","=",$_SESSION["g_id"]) : DB::all($equipment);
    $response = [
        "status" => true,
        "equipments" => $equipment
    ];    
    echo json_encode($response);
?>