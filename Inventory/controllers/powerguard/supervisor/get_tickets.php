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
        $pgt["status"] = "pending";
        $pg_ws_temp = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        $ws = [
            "status" => "",
            "ws_number" => ""
        ];
        foreach ($pg_ws_temp as $ws) {
            // $pg_ws_assessment = new PG_WS_Assessment;
            // $pg_ws_assessment = DB::where($pg_ws_assessment,"ws_id","=",$ws["id"]);
            if($ws["sign_off_queue"] == "done" && $ws["tech_id"] != "-"){
                $pgt["resolved_count"]++;
                $ws_temp = [
                    "status" => "resolved",
                    "ws_number" => $ws["ws_number"]
                ];
            }else{
                $ws_temp = [
                    "status" => "damaged",
                    "ws_number" => $ws["ws_number"]
                ];
            }
            array_push($pgt["workstations"],$ws_temp);
        }
        if($pgt["resolved_count"] == count($pg_ws_temp) && count($pg_ws_temp)){
            $pgt["status"] = "closed";
        }
        if($pgt["resolved_count"] <= count($pg_ws_temp) - 1 && $pgt["resolved_count"] != 0 && count($pg_ws_temp)){
            $pgt["status"] = "in_progress";
        }
        $dt = new DateTime($pgt["incident_datetime"]);
        $year = $dt->format('Y');

        if((new DateTime())->format('Y') == $year || $pgt["status"] != "closed"){
            array_push($pg_ticket_final,$pgt);
        }
    }

    echo json_encode($pg_ticket_final);
