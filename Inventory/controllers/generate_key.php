<?php
    header('Content-Type: application/json');
    include("../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $user = new User;
    
    
    $passkey = Data::generate(4,"numeric");
    while(!DB::validate($user,"passkey",$passkey)){
        $passkey = Data::generate(4,"numeric");
    }

    $user = DB::prepare($user, $data["id"]);
    $user->passkey = $passkey;
    DB::update($user);

    $response = [
        "status" => true,
        "key" => $passkey
    ];

    echo json_encode($response);
?>