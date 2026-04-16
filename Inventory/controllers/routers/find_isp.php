<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $isp = new ISP;
    $conf = new ISP_Configuration;
    $response = [
        "isp" => DB::find($isp,$data["id"]),
        "configuration" => DB::all($conf),
    ];
    // $data["id"] == "-" ? $data["id"] = "0" : null;
    echo json_encode($response);
?>