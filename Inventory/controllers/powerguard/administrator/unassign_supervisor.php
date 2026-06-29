<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $pg_dept = new PG_Department;
    $pg_dept = DB::prepare($pg_dept,$data["dept_id"]);
    $pg_dept->sup_id = "-";
    DB::update($pg_dept);

    $response = [
        "status" => true,
        "title" => $data["dept_name"]." Removed!",
        "type" => "info",
        "message" => "Department ".$data["dept_name"]." has been removed from ".$data["sup_name"].". Please assign a supervisor to continue all ongoing tickets."
    ];

    echo json_encode($response);