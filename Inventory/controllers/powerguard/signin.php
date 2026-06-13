<?php
    header('Content-Type: application/json');
    session_start();
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => false,
        "title" => "Opps!",
        "type" => "warning",
        "message" => "Incorrect username and password."
    ];

    $userid = $data["username"];;
    $password = $data['password'];
    $user = new PG_User;
    $auth = false;

    $users = DB::where($user,"username","=",$userid);

    if(count($users) == 1){
        $hash = $users[0]["password"];
        if(Data::decrypt($password,$hash)){
            $auth = true;
            $response = [
                "status" => true,
                "title" => "Greetings!",
                "type" => "success",
                "message" => "Welcome ".$users[0]["fname"][0].".".$users[0]["lname"],
                "user" => $users[0]
            ];
        }
    }

    if($auth){
        echo json_encode($response);
        exit;        
    }

    $users = DB::where($user,"employee_id","=",$userid);

    if(count($users) == 1){
        $hash = $users[0]["password"];
        if(Data::decrypt($password,$hash)){
            $auth = true;
            $response = [
                "status" => true,
                "title" => "Greetings!",
                "type" => "success",
                "message" => "Welcome ".$users[0]["fname"][0].".".$users[0]["lname"],
                "user" => $users[0]
            ];
        }
    }

    echo json_encode($response);



    
