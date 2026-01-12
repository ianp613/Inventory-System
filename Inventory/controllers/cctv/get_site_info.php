<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $cctv = new CCTV_Location;
    $cctv = DB::find($cctv,$data["id"]);
    $response = [
        "status" => true,
        "cctv" => $cctv
    ];    
    echo json_encode($response);
?>