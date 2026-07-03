<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => true,
        "title" => "Changes Saved!",
        "type" => "success",
        "message" => "Account changes has been saved successfully."
    ];

    $pg_user = new PG_User;
    $pg_user_ = DB::where($pg_user,"employee_id","=",$data["empid"]);
    $pg_user__ = DB::find($pg_user,$data["user_id"]);

    if(!count($pg_user__)){
        $response["status"] = false;
        $response["title"] = "Error!";
        $response["type"] = "error";
        $response["message"] = "User account not found.";
        echo json_encode($response);
        exit;
    }

    if(count($pg_user_)){
        if($pg_user_[0]["id"] != $data["user_id"]){
            $response["status"] = false;
            $response["title"] = "Oop!";
            $response["type"] = "warning";
            $response["message"] = "Account with Employee ID ".$data["empid"]." already exist.";
            echo json_encode($response);
            exit;
        }
    }

    $pg_user___ = DB::prepare($pg_user,$data["user_id"]);
    $pg_user___->fname = $data["fname"];
    $pg_user___->lname = $data["lname"];
    $pg_user___->email = $data["email"] ? $data["email"] : "-";
    $pg_user___->phone = $data["phone"] ? $data["phone"] : "-";
    $pg_user___->job_title = $data["job_title"];
    $pg_user___->employee_id = $data["empid"];
    $pg_user___->username = $data["empid"];
    DB::update($pg_user___);

    echo json_encode($response);