<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user = DB::prepare($pg_user,$data["user_id"]);
    $pg_user->account = $data["status"];
    DB::update($pg_user);

    $response = [
        "status" => true,
        "title" => $data["status"] == "active" ? "Account Activated!" : "Account Deactivated!",
        "type" => $data["status"] == "active" ? "success" : "info",
        "message" => "Account status for ".$pg_user->fname[0].". ".$pg_user->lname." has been ".($data["status"] == "active" ? "activated." : "deacticated.")
    ];

    echo json_encode($response);