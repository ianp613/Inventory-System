<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $yk_reserved = new YK_Reserved;
    echo json_encode(DB::delete($yk_reserved,$data["id"]));