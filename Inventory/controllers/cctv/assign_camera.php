<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["assign_camera_id"] != $data["id"] || $_SESSION["assign_type"] != $data["type"]){
        $_SESSION["assign_camera_id"] = $data["id"];
        $_SESSION["assign_type"] = $data["type"];

        $camera = new CCTV_Camera;
        $camera = DB::prepare($camera,$data["id"]);
        if($data["type"] == "assign"){
            $camera->cx = $data["cx"];
            $camera->cy = $data["cy"];
            DB::update($camera);
            $response = [
                "status" => true,
                "message" => "Camera has been assigned."
            ];
        }
        if($data["type"] == "unassign"){
            $camera->cx = "-";
            $camera->cy = "-";
            DB::update($camera);
            $response = [
                "status" => true,
                "message" => "Camera has been unassigned."
            ];
        }     
    }else{
        $response = [
            "status" => false,
            "message" => "Camera has already been assigned."
        ]; 
    }
       
    echo json_encode($response);
?>