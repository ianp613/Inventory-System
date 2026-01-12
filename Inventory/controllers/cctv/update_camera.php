<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    session_start();
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["camera_id"]) {
        $camera = new CCTV_Camera;
        $camera = DB::prepare($camera,$data["id"]);
        $camera->camera_id = $data["camera_id"];
        $camera->camera_type = $data["camera_type"] ? $data["camera_type"] : "-";
        $camera->camera_subtype = $data["camera_subtype"] ? $data["camera_subtype"] : "-";
        $camera->camera_ip_address = $data["camera_ip_address"] ? $data["camera_ip_address"] : "-";
        $camera->camera_port_no = $data["camera_port_no"] ? $data["camera_port_no"] : "-";
        $camera->camera_username = $data["camera_username"] ? $data["camera_username"] : "-";
        $camera->camera_password = $data["camera_password"] ? $data["camera_password"] : "-";
        $camera->camera_angle = $data["camera_angle"] ? $data["camera_angle"] : "0";
        $camera->camera_location = $data["camera_location"] ? $data["camera_location"] : "-";
        $camera->camera_brand = $data["camera_brand"] ? $data["camera_brand"] : "-";
        $camera->camera_model_no = strtoupper($data["camera_model_no"]) ? strtoupper($data["camera_model_no"]) : "-";
        $camera->camera_barcode = strtoupper($data["camera_barcode"]) ? strtoupper($data["camera_barcode"]) : "-";
        $camera->camera_status = $data["camera_status"] ? $data["camera_status"] : "-";
        $camera->camera_remarks = $data["camera_remarks"] ? $data["camera_remarks"] : "-";
        DB::update($camera);

        $response = [
            "status" => true,
            "type" => "success",
            "size" => null,
            "message" => "Camera has been updated."
        ];

    }else{
        $response = [
            "status" => false,
            "type" => "warning",
            "size" => null,
            "message" => "Please provide camera alias."
        ];
    }
    echo json_encode($response);
?>