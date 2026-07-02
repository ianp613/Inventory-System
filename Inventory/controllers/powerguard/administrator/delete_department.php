<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Get and loop all ticket with same dept_id to delete WS and WS_Assessment
    $pg_ticket = new PG_Ticket;
    $pg_ticket_ = DB::where($pg_ticket,"dept_id","=",$data["dept_id"]);
    foreach($pg_ticket_ as $pgt){
        // Get and loop all workstation under each ticket and delete the assessment if present
        $pg_ws = new PG_WS;
        $pg_ws_ = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        foreach($pg_ws_ as $pgw){
            $pg_assessment = new PG_WS_Assessment;
            $pg_assessment_ = DB::where($pg_assessment,"ws_id","=",$pgw["id"]);
            if(count($pg_assessment_)){
                DB::delete($pg_assessment,$pg_assessment_[0]["id"]);
            }
            DB::delete($pg_ws,$pgw["id"]);
        }
        DB::delete($pg_ticket,$pgt["id"]);
    }
    $pg_dept = new PG_Department;
    DB::delete($pg_dept,$data["dept_id"]);

    $response = [
        "status" => true,
        "title" => $data["dept_name"]." Deleted!",
        "type" => "info",
        "message" => "Department ".$data["dept_name"]." has been deleted."
    ];

    echo json_encode($response);