<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user = DB::where2($pg_user,"privileges","=","supervisor","account","=","active");
    $pg_user_ = [];
    foreach($pg_user as $pgu){
        $pgu["name"] = $pgu["fname"][0].". ".$pgu["lname"];
        array_push($pg_user_,$pgu);
    }

    echo json_encode($pg_user_);