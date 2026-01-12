<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if(file_exists($data["qr_file"])){
        unlink($data["qr_file"]);
    }

    $response = [
        "status" => true,
        "type" => "info",
        "size" => null,
        "message" => "QR file has been deleted.".file_exists($data["qr_file"])
    ];
    
    echo json_encode($response)
?>