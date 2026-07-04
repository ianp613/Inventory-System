<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $captive_ = new Captive_;

    $response = [
        "status" => true,
        "message" => "Voucher Loaded.",
        "data" => DB::all($captive_)
    ];

    echo json_encode($response);
