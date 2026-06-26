<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    

    $response = [
        "status" => true,
        "title" => "Account Rejected!",
        "type" => "info",
        "message" => ""
    ];

    $pg_user = new PG_User;
    $pg_user = DB::prepare($pg_user,$data["user_id"]);
    $pg_user->account = "rejected";
    DB::update($pg_user);

    $response["message"] = $pg_user->fname." ".$pg_user->lname."'s account has been rejected and they will be notified through email.";

    echo json_encode($response);