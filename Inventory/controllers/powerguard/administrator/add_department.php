<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $pg_dept = new PG_Department;
    $pg_dept->name = $data["name"];
    $pg_dept->sup_id = "-";
    DB::save($pg_dept);

    $response = [
        "status" => true,
        "title" => "Department Added!",
        "type" => "success",
        "message" => "Department ".$data["name"]." has been added successfully."
    ];

    echo json_encode($response);