<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_dept = new PG_Department;
    $pg_dept = DB::where($pg_dept,"sup_id","=",$data["sup_id"]);

    echo json_encode($pg_dept);