<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $pg_dept = new PG_Department;
    DB::delete($pg_dept,$data["dept_id"]);

    $response = [
        "status" => true,
        "title" => $data["dept_name"]." Deleted!",
        "type" => "info",
        "message" => "Department ".$data["dept_name"]." has been deleted."
    ];

    echo json_encode($response);