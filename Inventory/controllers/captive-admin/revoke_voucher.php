<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $captive_ = new Captive_;
    $captive_ = DB::prepare($captive_,$data["id"]);
    $captive_->status = "revoked";
    DB::update($captive_);

    $response = [
        "status" => true,
        "message" => "Voucher Revoked."
    ];

    echo json_encode($response);