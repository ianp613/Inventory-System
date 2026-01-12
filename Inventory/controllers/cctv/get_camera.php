<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $camera = new CCTV_Camera;
    $camera = DB::where($camera,"lid","=",$data["lid"]);
    $response = [
        "status" => true,
        "camera" => $camera
    ];    
    echo json_encode($response);
?>