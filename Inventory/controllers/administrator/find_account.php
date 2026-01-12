<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["id"]) {
        $user = new User;
        $response = [
            "status" => true,
            "type" => "info",
            "size" => null,
            "message" => "Edit user with ID ".$data["id"],
            "user" => DB::find($user,$data["id"])
        ];
    }else{
        $response = [
            "status" => false,
            "type" => "error",
            "size" => null,
            "message" => "User not found."
        ];
    }
    echo json_encode($response);
?>