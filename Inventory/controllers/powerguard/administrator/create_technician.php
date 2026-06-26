<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    

    $response = [
        "status" => true,
        "title" => "Account Created!",
        "type" => "success",
        "message" => ""
    ];

    $pg_user = new PG_User;
    $pg_user->fname = $data["fname"];
    $pg_user->lname = $data["lname"];
    $pg_user->email = $data["email"] ? $data["email"] : "-";
    $pg_user->job_title = $data["job_title"];
    $pg_user->phone = $data["phone"] ? $data["phone"] : "-";
    $pg_user->employee_id = $data["employee_id"];
    $pg_user->privileges = "technician";
    $pg_user->account = "active";
    $pg_user->username = $data["username"];
    $pg_user->password = Data::encrypt($data["password"]);
    
    if(!DB::validate($pg_user,"employee_id",$data["employee_id"])){
        $response = [
            "status" => false,
            "title" => "Opps!",
            "type" => "warning",
            "message" => "Account with Employee ID ".$data["employee_id"]." already exist."
        ];
        echo json_encode($response);
        exit;
    }
    if(!DB::validate($pg_user,"username",$data["username"])){
        $response = [
            "status" => false,
            "title" => "Opps!",
            "type" => "warning",
            "message" => "Account with username ".$data["username"]." already exist."
        ];
        echo json_encode($response);
        exit;
    }
    DB::save($pg_user);
    $response["message"] = "Technician account created for ".$pg_user->fname." ".$pg_user->lname.". Account is active immediately — no approval needed.";

    echo json_encode($response);
