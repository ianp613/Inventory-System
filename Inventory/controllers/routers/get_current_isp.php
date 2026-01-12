<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $wan1 = [];
    $wan2 = [];

    $isp = new ISP;

    if($data["wan1"] && $data["wan1"] != "-"){
        $wan1 = DB::find($isp,$data["wan1"]);
    }
    if($data["wan2"] && $data["wan2"] != "-"){
        $wan2 = DB::find($isp,$data["wan2"]);
    }
    $response = [
        "wan1" => $wan1,
        "wan2" => $wan2
    ];
    echo json_encode($response);
?>