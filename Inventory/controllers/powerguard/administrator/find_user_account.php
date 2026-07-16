<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user = DB::find($pg_user,$data["user_id"]);

    if(count($pg_user)){
        $response = [
            "status" => true,
            "fname" => $pg_user[0]["fname"],
            "lname" => $pg_user[0]["lname"],
            "privileges" => $pg_user[0]["privileges"],
            "account" => $pg_user[0]["account"]
        ];
    }else{
        $response = [
            "status" => false
        ];    
    }

    echo json_encode($response);