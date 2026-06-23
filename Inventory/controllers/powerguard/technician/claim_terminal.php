<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ws = new PG_WS;
    $pg_ws = DB::prepare($pg_ws,$data["ws_id"]);
    $pg_ws->tech_id = $data["tech_id"];
    DB::update($pg_ws);

    $response = [
        "status" => true,
        "title" => "Workstation Claimed!",
        "type" => "success",
        "message" => "Workstation ".$pg_ws->ws_number." has been claimed."
    ];

    echo json_encode($response);