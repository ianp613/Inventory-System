<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user = DB::where2($pg_user,"privileges","=","supervisor","account","NOT LIKE","%acti%");


    echo json_encode($pg_user);