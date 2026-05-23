<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    

    $response = [
        "status" => true,
        "type" => "info",
        "size" => null,
        "message" => "Terminal has been deleted."
    ];
    if($_SESSION["g_member"]){
        if($data) {
            $t = new Terminals;
            DB::delete($t,$data["id"]);
        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Something went wrong."
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