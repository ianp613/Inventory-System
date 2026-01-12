<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $user = new User;
        $user = DB::prepare($user,$data["id"]);
        $user->email = $data["email"] ? $data["email"] : "-";
        $user->password = $data["password"];
        DB::update($user);
        $response = [
            "status" => true,
            "type" => "success",
            "size" => "lg",
            "message" => "Your account has been successfully updated."
        ]; 
    }
    echo json_encode($response);
?>
