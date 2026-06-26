<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user = DB::where2($pg_user,"id","!=",$data["id"],"privileges","=","technician");

    $pg_user_final = [];

    foreach ($pg_user as $pgu) {
        if($pgu["account"] == "active"){
            array_push($pg_user_final,$pgu);
        }
    }

    echo json_encode($pg_user_final);