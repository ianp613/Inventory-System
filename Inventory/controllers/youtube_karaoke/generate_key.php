<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $yk_room = new YK_Room;

    $response = [
        "status" => false,
        "type" => "warning",
        "message" => "Error generating room ID.",
        "size" => null,
        "key" => "Auto-generated"
    ];
    
    $unique = false;
    while (!$unique) {
        $key = strtoupper(Data::generate(5,"alphanumeric"));
        if(DB::validate($yk_room,"room_id",$key)){
            $unique = true;
        }
    }
    
    if(!$unique){
        echo json_encode($response);
        exit;
    }

    $yk_room->room_id   = $key;
    $yk_room->room_name = $data["yk_room_name"];
    DB::save($yk_room);
    $response["status"] = true;
    $response["type"] = "success";
    $response["size"] = "lg";
    $response["message"] = "<div class=\"ps-3 pe-3\">Room has been created, please input generated room id once you enter karaoke room or use reservation control.</div>";
    $response["key"] = $key;
    echo json_encode($response);