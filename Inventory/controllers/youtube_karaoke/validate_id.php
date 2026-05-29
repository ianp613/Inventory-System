<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $yk_room = new YK_Room;

    $response = [
        "status" => false,
        "type" => "warning",
        "message" => "Invalid Room ID.",
        "name" => ""
    ];
    
    $yk_room = DB::where($yk_room,"room_id","=",$data["id"]);
    if(count($yk_room)){
        $response["status"] = true;
        $response["name"] = $yk_room[0]["room_name"];
    }

    echo json_encode($response);