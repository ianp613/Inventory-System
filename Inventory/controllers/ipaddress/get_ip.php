<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["nid"]){
        $cache_key = "icore_ip:nid" . $data["nid"];
        $cache_data = $redis->get($cache_key);

        if ($cache_data !== null) {
            echo $cache_data;
            exit;
        }

        $ip = new IP_Address;
        $ip = DB::where($ip,"nid","=",$data["nid"]);
        $response = [
            "status" => true,
            "ip" => $ip
        ];

        $redis->setex($cache_key, 300, json_encode($response));
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