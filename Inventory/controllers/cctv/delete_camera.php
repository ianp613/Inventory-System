<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $camera = new CCTV_Camera;
    $camera = DB::delete($camera,$data["id"]);
    $response = [
        "status" => true,
        "message" => "Camera has been delete."
    ];    
    echo json_encode($response);
?>