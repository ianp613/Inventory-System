<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["eid"]){
        $entry = new Equipment_Entry;
        $entry = DB::where($entry,"eid","=",$data["eid"]);
        $response = [
            "status" => true,
            "entry" => $entry
        ];
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