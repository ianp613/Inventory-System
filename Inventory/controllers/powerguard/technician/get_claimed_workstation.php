<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ws_final = [];
    $pg_ws = new PG_WS;
    $pg_ws = DB::where2($pg_ws,"tech_id","=",$data["tech_id"],"sign_off_queue","!=","done");
    
    $pg_ticket = new PG_Ticket;
    $pg_ticket = DB::all($pg_ticket);

    foreach ($pg_ws as $pgw) {
        foreach ($pg_ticket as $pgt) {
            if($pgw["ticket_id"] == $pgt["id"]){
                $pgw["ticket_no"] = $pgt["ticket_no"];
            }
        }
        array_push($pg_ws_final,$pgw);
    }

    echo json_encode($pg_ws_final);