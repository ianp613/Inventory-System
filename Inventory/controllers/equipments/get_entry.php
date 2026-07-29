<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["eid"]){
        $cache_key = "icore_entry:eid" . $data["eid"];
        $cache_data = $redis->get($cache_key);

        if ($cache_data !== null) {
            echo $cache_data;
            exit;
        }

        $entry = new Equipment_Entry;
        $entry = DB::where($entry,"eid","=",$data["eid"]);
        $response = [
            "status" => true,
            "entry" => $entry
        ];

        $redis->setex($cache_key, 300, json_encode($response));
    }else{
        $response = [
            "status" => false,
            "type" => "warning",
            "size" => null,
            "message" => "Please select an equipment first."
        ];
    }
        
    echo json_encode($response);
?>