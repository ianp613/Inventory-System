<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ticket = new PG_Ticket;
    $pg_ticket = DB::where($pg_ticket,"sup_id","=",$data["sup_id"]);

    $pg_ws = new PG_WS;
    $pg_ticket_final = [];
    foreach ($pg_ticket as $pgt) {
        $pgt["workstations"] = [];
        $pgt["resolved_count"] = 0;
        $pg_ws_temp = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        $ws = [
            "status" => "",
            "ws_number" => ""
        ];
        foreach ($pg_ws_temp as $ws) {
            if($ws["tech_id"] != "-"){
                $pgt["resolved_count"]++;
                $ws = [
                    "status" => "resolved",
                    "ws_number" => $ws["ws_number"]
                ];
            }else{
                $ws = [
                    "status" => "damaged",
                    "ws_number" => $ws["ws_number"]
                ];
            }
            array_push($pgt["workstations"],$ws);
        }
        array_push($pg_ticket_final,$pgt);
    }

    echo json_encode($pg_ticket_final);
