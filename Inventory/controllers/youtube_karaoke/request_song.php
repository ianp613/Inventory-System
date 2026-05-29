<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $yk_reserved = new YK_Reserved;
    $yk_reserved = DB::where($yk_reserved,"rid","=",$data["id"]);
    echo json_encode($yk_reserved);