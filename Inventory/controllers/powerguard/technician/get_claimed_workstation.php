<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ws_final = [];
    $pg_ws_ = new PG_WS;
    $pg_ws = DB::where2($pg_ws_,"tech_id","=",$data["tech_id"],"sign_off_queue","!=","done");
    
    $pg_ticket = new PG_Ticket;
    $pg_ticket = DB::all($pg_ticket);

    foreach ($pg_ws as $pgw) {
        foreach ($pg_ticket as $pgt) {
            if($pgw["ticket_id"] == $pgt["id"]){
                $pgw["priority"] = $pgt["priority"];
                $pgw["ticket_no"] = $pgt["ticket_no"];
            }
        }
        array_push($pg_ws_final,$pgw);
    }

    $pg_ws_completed_final = [];
    $pg_ws_completed = DB::where2($pg_ws_,"tech_id","=",$data["tech_id"],"sign_off_queue","=","done");

    foreach ($pg_ws_completed as $pgw) {
        foreach ($pg_ticket as $pgt) {
            if($pgw["ticket_id"] == $pgt["id"]){
                $pg_assess = new PG_WS_Assessment;
                $pg_assess = DB::where($pg_assess,"ws_id","=",$pgw["id"]);
                if(count($pg_assess)){
                    $pgw["technical_findings"] = $pg_assess[0]["technical_findings"];
                    $pgw["assessed_at"] = $pg_assess[0]["assessed_at"];
                }else{
                    $pgw["technical_findings"] = "-";
                    $pgw["assessed_at"] = "";
                }
                $pgw["ticket_no"] = $pgt["ticket_no"];
            }
        }
        array_push($pg_ws_completed_final,$pgw);
    }





    $pg_ws_rejected = count(DB::where2($pg_ws_,"tech_id","=",$data["tech_id"],"sign_off_queue","=","rejected"));
    $pg_ws_submitted = count(DB::where2($pg_ws_,"tech_id","=",$data["tech_id"],"sign_off_queue","=","submitted"));

    echo json_encode([$pg_ws_final,[$pg_ws_completed_final,$pg_ws_submitted,$pg_ws_rejected]]);