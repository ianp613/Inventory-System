<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $pg_dept = new PG_Department;
    $pg_dept = DB::all($pg_dept);

    $pg_user = new PG_User;
    $pg_user = DB::all($pg_user);

    $pg_dept_ = [];

    foreach ($pg_dept as $dept) {
        $dept["sup_name"] = null;
        $dept["sup_id"] = $dept["sup_id"] != "-" ? $dept["sup_id"] : null;
        foreach ($pg_user as $pgu) {
            if($pgu["id"] == $dept["sup_id"]){
                $dept["sup_name"] = $pgu["fname"][0].". ".$pgu["lname"];
            }
        }
        array_push($pg_dept_,$dept);
    }

    echo json_encode($pg_dept_);