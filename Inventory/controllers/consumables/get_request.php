<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $consumable_request = new Consumable_Request;
    $request = DB::find($consumable_request,$data["id"]);

    echo json_encode($request);
?>