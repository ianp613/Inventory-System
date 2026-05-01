<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $user = new User;
        
        $passkey = Data::generate(6,"numeric");
        while(!DB::validate($user,"passkey",$passkey)){
            $passkey = Data::generate(6,"numeric");
        }
        
        if(DB::validate($user,"username",$data["username"])){
            $user->name = $data["name"];
            $user->email = $data["email"] ? $data["email"] : "-";
            $user->username = $data["username"];
            $user->password = Data::encrypt($data["password"]);
            $user->privileges = $data["privilege"];
            $user->last_login_ip = "-";
            $user->c_authority = $data["privilege"] != "User" ? "true" : "false";
            $user->passkey = $passkey;
            
            DB::save($user);
            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "User account has been created."
            ];
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => null,
                "message" => "User ID already exist."
            ];
        } 
    }else{
        $response = [
            "status" => false,
            "type" => "error",
            "size" => null,
            "message" => "Something went wrong."
        ];
    }
    echo json_encode($response);
?>