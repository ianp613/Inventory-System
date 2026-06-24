<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => true,
        "title" => "Assessment Rejected!",
        "type" => "success",
        "message" => "Workstation ".$data["ws_number"]." has been signed-off."
    ];

    $ws = new PG_WS;
    $ws = DB::prepare($ws,$data["ws_id"]);
    $ws->sign_off_queue = "rejected";
    DB::update($ws);


    echo json_encode($response);