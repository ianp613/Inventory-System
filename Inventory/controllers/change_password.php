<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Code is invalid.",
    ]; 

    if($data) {
        if($data["code"] == $_SESSION["code"]){
            $user = new User;
            $user = DB::prepare($user,$data["id"]);
            $user->password = $data["password"];
            DB::update($user);

            $_SESSION["code"] = null;

            $response = [
                "status" => true,
                "type" => "success",
                "size" => "lg",
                "message" => "You've successfully change your password. <br>Redirecting to Login ...",
            ];
        }    
    }
    echo json_encode($response);
?>
