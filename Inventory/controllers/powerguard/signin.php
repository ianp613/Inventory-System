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
        if($users[0]["account"] == "inactive"){
            $auth = false;
            $response = [
                "status" => false,
                "title" => "Pending Account Activation",
                "type" => "warning",
                "message" => "Your account is not yet activated, please wait for account activation notification that will be sent to ".$users[0]["email"],
            ];
        }

        if($users[0]["account"] == "deactivated"){
            $auth = false;
            $response = [
                "status" => false,
                "title" => "Deactivated Account",
                "type" => "error",
                "message" => "Your account has been deactivated. Please contact your system administrator immediately to prevent account deletion.",
            ];
        }

        if($users[0]["account"] == "rejected"){
            DB::delete($user,$users[0]["id"]);
            $auth = false;
            $response = [
                "status" => false,
                "title" => "Rejected Account",
                "type" => "error",
                "message" => "Your account application has been rejected. Please review your information thoroughly before resubmitting. This account application will now be deleted.",
            ];
        }
    }

    echo json_encode($response);


    // if($auth){
    //     echo json_encode($response);
    //     exit;        
    // }

    // $users = DB::where($user,"employee_id","=",$userid);

    // if(count($users) == 1){
    //     $hash = $users[0]["password"];
    //     if(Data::decrypt($password,$hash)){
    //         $auth = true;
    //         $response = [
    //             "status" => true,
    //             "title" => "Greetings!",
    //             "type" => "success",
    //             "message" => "Welcome ".$users[0]["fname"][0].".".$users[0]["lname"],
    //             "user" => $users[0]
    //         ];
    //     }
    // }

    // if($auth){
    //     if($users[0]["account"] == "inactive"){
    //         $auth = false;
    //         $response = [
    //             "status" => false,
    //             "title" => "Pending Account Activation",
    //             "type" => "warning",
    //             "message" => "Your account is not yet activated, please wait for account activation notification that will be sent to ".$users[0]["email"],
    //         ];
    //     }

    //     if($users[0]["account"] == "deactivated"){
    //         $auth = false;
    //         $response = [
    //             "status" => false,
    //             "title" => "Deactivated Account",
    //             "type" => "error",
    //             "message" => "Your account has been deactivated. Please contact your system administrator immediately to prevent account deletion.",
    //         ];
    //     }
    // }

    // echo json_encode($response);



    
