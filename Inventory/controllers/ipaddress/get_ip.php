<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["nid"]){
        $ip = new IP_Address;
        $ip = DB::where($ip,"nid","=",$data["nid"]);
        $response = [
            "status" => true,
            "ip" => $ip
        ];
    }else{
        $response = [
            "status" => false,
            "type" => "warning",
            "size" => null,
            "message" => "Please select a network first."
        ];
    }
        
    echo json_encode($response);
?>