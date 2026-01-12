<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $consumables = new Consumables;
    $consumables = $_SESSION["g_id"] ? DB::where($consumables,"gid","=",$_SESSION["g_id"]) : DB::all($consumables);

    $response = [
        "status" => true,
        "consumables" => $consumables,
    ];
        
    echo json_encode($response);
?>