<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_assessment = new PG_WS_Assessment;
    $pg_assessment = DB::where($pg_assessment,"ws_id","=",$data["ws_id"]); 

    echo json_encode($pg_assessment);