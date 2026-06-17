<?php
    header('Content-Type: application/json');
    session_start();
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $bol = true;

    $response = [
        "status" => false,
        "title" => "Opps!",
        "type" => "warning",
        "message" => "Account already exist."
        // "message" => "Account with username ".$data["username"]." already exist."
    ];
    $user = new PG_User;

    if(!DB::validate($user,"username",$data["username"])){
        $response["message"] = "Account with username ".$data["username"]." already exist.";
        $bol = false;
    }
    if(!DB::validate($user,"employee_id",$data["employee_id"])){
        $response["message"] = "Account with Employee ID ".$data["employee_id"]." already exist.";
        $bol = false;
    }

    if($bol){
        $user->fname = $data["fname"];
        $user->lname = $data["lname"];
        $user->job_title = $data["job_tittle"];
        $user->email = $data["email"];
        $user->phone = $data["phone"];
        $user->employee_id = $data["employee_id"];
        $user->username = $data["username"];
        $user->password = Data::encrypt($data["password"]);
        $user->privileges = "supervisor";
        $user->account = "inactive";
        DB::save($user);

        $response = [
            "status" => true,
            "title" => "Success!",
            "type" => "success",
            "message" => "Account has been created."
        ];   
    }

    echo json_encode($response);