<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $isp = new ISP;
    // $data["id"] == "-" ? $data["id"] = "0" : null;
    echo json_encode(DB::find($isp,$data["id"]));
?>