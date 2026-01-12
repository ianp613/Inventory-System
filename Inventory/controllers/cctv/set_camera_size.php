<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $cctv = new CCTV_Location;
    $cctv = DB::prepare($cctv,$data["id"]);
    $cctv->camera_size = $data["camera_size"];
    DB::update($cctv);
    $response = [
        "status" => true,
        "message" => "Camera size has been updated."
    ];    
    echo json_encode($response);
?>