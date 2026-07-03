<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user = DB::prepare($pg_user,$data["user_id"]);
    $pg_user->password = Data::encrypt($data["password"]);
    DB::update($pg_user);

    $response = [
        "status" => true,
        "title" => "Password Reset!",
        "type" => "success",
        "message" => "Account password for ".$pg_user->fname[0].". ".$pg_user->lname." has been reset."
    ];

    echo json_encode($response);