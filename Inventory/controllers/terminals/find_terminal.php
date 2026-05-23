<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $t = new Terminals;
    echo json_encode(DB::find($t,$data["id"])[0]);
?>