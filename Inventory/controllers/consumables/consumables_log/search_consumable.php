<?php
    session_start();
    header('Content-Type: application/json');
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => true,
        "data" => []
    ];

    if(file_exists("../links/".$data["link"])){
        $consumables = new Consumables;
        $codes = DB::where($consumables,"code","like",$data["search"]);

        if(array_key_exists("g_id",$data)){
            $temp1 = [];
            foreach ($codes as $code) {
                if($code["gid"] == $data["g_id"]){
                    array_push($temp1,$code);
                }
            }
            $codes = $temp1;
        }else{
            $codes = [];
        }

        $descriptions = DB::where($consumables,"description","like",$data["search"]);
        if(array_key_exists("g_id",$data)){
            $temp2 = [];
            foreach ($descriptions as $description) {
                if($description["gid"] == $data["g_id"]){
                    array_push($temp2,$description);
                }
            }
            $descriptions = $temp2;
        }else{
            $descriptions = [];
        }
        $response["data"] = array_merge($codes, $descriptions);
    }else{
        $response["status"] = false;
    }

    


    echo json_encode($response);
?>