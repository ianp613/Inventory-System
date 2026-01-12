<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);
    $log = new Logs;
    DB::delete($log,$data["id"]);
    $response = [
        "status" => true,
        "type" => "info",
        "message" => "Log has been deleted."
    ];

    echo json_encode($response);
?>