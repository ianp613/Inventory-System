<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $yk_reserved = new YK_Reserved;
    $yk_reserved->rid = $data["id"];
    $yk_reserved->yt_link = $data["link"];
    $yk_reserved->yk_singer = $data["singer"];
    DB::save($yk_reserved);

    $response = [
        "status" => true,
        "type" => "success",
        "message" => "Song has been reserved.",
    ];

    echo json_encode($response);