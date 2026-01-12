<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "ISP not found."
    ];

    if($data["id"]) {
        $isp = new ISP;
        if(count(DB::find($isp,$data["id"]))){
            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "Edit ISP with ID ".$data["id"],
                "isp" => DB::find($isp,$data["id"])
            ];
        }
    }
    echo json_encode($response);
?>