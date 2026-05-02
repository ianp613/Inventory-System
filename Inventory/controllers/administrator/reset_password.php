<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $auth = false;

    $user = new User;
    $users = DB::find($user,$_SESSION["userid"]);

    if(count($users) == 1){
        $hash = $users[0]["password"];
        if(Data::decrypt($data["password"],$hash)){
            $auth = true;
        }
    }

    if($auth) {
        $user_temp = DB::prepare($user,$data["id"]);
        $user_temp->password = Data::encrypt("12345");
        DB::update($user_temp);
        
        $response = [
            "status" => true,
            "type" => "info",
            "size" => "lg",
            "message" => "Login password for <b>\"".$user_temp->name."\"</b> has ben reset.",
        ];
    }else{
        $response = [
            "status" => false,
            "type" => "error",
            "size" => null,
            "message" => "Incorrect password."
        ];
    }
    echo json_encode($response);
?>