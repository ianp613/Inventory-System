<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $consumable_log = new Consumable_Log;
    $response = DB::where($consumable_log,"cid","=",$data["id"]);

    echo json_encode($response);
?>