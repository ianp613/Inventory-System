<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $captive_ = new Captive_;
    $captive_->code = strtoupper(Data::generate(6,"alphanumeric"));
    $captive_->voucher_name = $data["voucher_name"];
    $captive_->duration = $data["duration"];
    $captive_->duration_unit = $data["duration_unit"];
    $captive_->uses = $data["uses"];
    $captive_->uses_remaining = $data["uses"];
    $captive_->data_limit = $data["data_limit"];
    $captive_->data_cap = $data["data_cap"];
    $captive_->status = "active";
    DB::save($captive_);

    $response = [
        "status" => true,
        "message" => "Voucher Created."
    ];

    echo json_encode($response);