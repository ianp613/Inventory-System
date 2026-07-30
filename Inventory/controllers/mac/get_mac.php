<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);


    if($data["wid"]){
        $cache_key = "icore_mac:" . strtolower($data["wid"]) . (strtolower($data["wid"]) == "show all" ? ($_SESSION["g_id"] ? $_SESSION["g_id"] : "") : "");
        $cache_data = $redis->get($cache_key);

        if($cache_data !== null){
            echo $cache_data;
            exit;
        }

        $mac = new MAC_Address;
        if(strtolower($data["wid"]) == "show all"){
            $mac = $_SESSION["g_id"] ? DB::where($mac,"gid","=",$_SESSION["g_id"]) : DB::all($mac);
        }else{
            $mac = DB::where($mac,"wid","=",$data["wid"]); 
        }
        
        $response = [
            "status" => true,
            "mac" => $mac,
        ];

        $redis->setex($cache_key, 300, json_encode($response));
    }else{
        $response = [
            "status" => false,
            "type" => "warning",
            "size" => null,
            "message" => "Please select a wifi first."
        ];
    }
        
    echo json_encode($response);
?>