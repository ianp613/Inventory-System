<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $user = new User;
        DB::delete($user,$data["id"]);
        
        $response = [
            "status" => true,
            "type" => "info",
            "size" => null,
            "message" => "User account has been deleted.",
            "entry" => DB::all($user)
        ];
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