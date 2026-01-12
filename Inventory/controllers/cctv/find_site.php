<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $location = new CCTV_Location;
    $location = DB::where($location,"id","=",$data["id"]);
    $response = [
        "status" => true,
        "site" => $location
    ];    
    echo json_encode($response);
?>