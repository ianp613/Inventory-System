<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "IP not found."
    ];
    if($_SESSION["g_member"]){
        if($data["id"]) {
            $ip = new IP_Address;
            DB::prepare($ip,$data["id"]);
            $ip->hostname = "-";
            $ip->site = "-";
            $ip->server = "-";
            $ip->status = "UNASSIGNED";
            $ip->remarks = "-";
            $ip->webmgmtpt = "-";
            $ip->username = "-";
            $ip->password = "-";
            $ip->state = "DOWN";
            DB::update($ip);

            $response = [
                "status" => true,
                "type" => "info",
                "size" => null,
                "message" => "IP address has been unassigned."
            ];
        }
    }else{
        $response = [
            "status" => false,
            "type" => "info",
            "size" => null,
            "message" => "Please operate as group member."
        ];
    }
    echo json_encode($response);
?>