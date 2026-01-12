<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $consumables_log = new Consumable_Log;
    $consumables_log = $_SESSION["g_id"] ? DB::where($consumables_log,"gid","=",$_SESSION["g_id"]) : [];

    $response = [
        "status" => true,
        "consumables_log" => $consumables_log,
    ];
        
    echo json_encode($response);
?>