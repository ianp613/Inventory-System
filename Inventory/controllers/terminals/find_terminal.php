<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $modal = file_get_contents("../../views/modals/edit_terminal.html");
    $t = new Terminals;
    echo json_encode([$modal,DB::find($t,$data["id"])[0]]);
?>