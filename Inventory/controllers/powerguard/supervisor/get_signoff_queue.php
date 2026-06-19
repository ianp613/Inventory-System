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
        $pg_ws_temp = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        $ws = [
            "ws_number" => "",
            "technician_name" => "",
            "submitted_at" => "",
            "findings" => "",
            "status" => "pending"
        ];
        $ws_add = false;
        foreach ($pg_ws_temp as $ws) {

            if($ws["tech_id"] != "-" && $ws["sign_off_queue"] != "pending"){

                $ws_temp = [
                    "ws_number" => $ws["ws_number"],
                    "technician_name" => "",
                    "submitted_at" => "",
                    "findings" => "Done",
                    "status" => $ws["sign_off_queue"]
                ];

                if($ws["sign_off_queue"] != "done"){
                    $ws_add = true;
                }

            }else{
                $ws_add = true;
                $ws_temp = [
                    "ws_number" => $ws["ws_number"],
                    "technician_name" => "",
                    "submitted_at" => "",
                    "findings" => "Awaiting technician ...",
                    "status" => "pending"
                ];
            }
            array_push($pgt["workstations"],$ws_temp);
        }


        $dt = new DateTime($pgt["incident_datetime"]);
        $year = $dt->format('Y');

        if((new DateTime())->format('Y') == $year && $ws_add){
            array_push($pg_ticket_final,$pgt);
        }
    }

    echo json_encode($pg_ticket_final);
