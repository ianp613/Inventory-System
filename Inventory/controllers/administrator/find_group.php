<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Group not found."
    ];

    if($data["id"]) {
        $group = new User_Group;
        $user = new User;
        if(count(DB::find($group,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit Group with ID ".$data["id"],
                "group" => DB::find($group,$data["id"]),
                "users" => DB::all($user)
            ];
        }
    }
    echo json_encode($response);
?>