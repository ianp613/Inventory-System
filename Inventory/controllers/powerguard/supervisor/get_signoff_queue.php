<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ticket = new PG_Ticket;
    $pg_ticket = DB::where($pg_ticket,"sup_id","=",$data["sup_id"]);

    $pg_ws = new PG_WS;
    $pg_ticket_final = [];
    $ws_resolved = 0;
    $ticket_closed = 0;
    foreach ($pg_ticket as $pgt) {
        $pgt["workstations"] = [];
        $pg_ws_temp = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        $ws_temp = [
            "ws_number" => "",
            "technician_name" => "",
            "submitted_at" => "",
            "findings" => "",
            "status" => "pending"
        ];
        $ws_add = false;
        foreach ($pg_ws_temp as $ws) {
            if($ws["tech_id"] != "-"){
                $tech = new PG_User;
                $tech = DB::find($tech,$ws["tech_id"]);
                
                $ws_temp = [
                    "ws_number" => $ws["ws_number"],
                    "technician_name" => count($tech) ? $tech[0]["fname"][0].". ".$tech[0]["lname"] : "",
                    "submitted_at" => "",
                    "findings" => "",
                    "status" => $ws["sign_off_queue"]
                ];

                $ws_assessment = new PG_WS_Assessment;
                $ws_assessment = DB::where($ws_assessment,"ws_id","=",$ws["id"]);

                if(count($ws_assessment) && $ws["sign_off_queue"] != "draft"){
                    // if($ws["sign_off_queue"] != "rejected"){
                    //     continue;
                    // }
                    
                    $ws_resolved += $ws["sign_off_queue"] == "done" ? 1 : 0;

                    $ws_temp["submitted_at"] = $ws_assessment[0]["assessed_at"];
                    $ws_temp["findings"] = $ws_assessment[0]["ups_condition"] != "Functional" ? "UPS ".$ws_assessment[0]["ups_condition"] : "" ;
                    $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["ups_condition"] != "Functional" ? " · " : "").($ws_assessment[0]["system_unit_condition"] != "Functional" ? "System Unit ".$ws_assessment[0]["system_unit_condition"] : "");
                    $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["system_unit_condition"] != "Functional" ? " · " : "").($ws_assessment[0]["monitor_condition"] != "Functional" ? "Monitor ".$ws_assessment[0]["monitor_condition"] : "");
                    $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["monitor_condition"] != "Functional" ? " · " : "").($ws_assessment[0]["technical_findings"]);
                }

                if($ws["sign_off_queue"] == "pending"){
                    $ws_temp["findings"] = "Assessment has not yet started.";
                }

                if($ws["sign_off_queue"] != "done"){
                    $ws_add = true;
                }

                if(!count($ws_assessment) && $ws["sign_off_queue"] == "submitted"){
                    $ws_temp["status"] = "draft";
                }

                if(!count($tech)){
                    $ws_temp["findings"] = "Not yet assigned to any technician.";
                    $ws_temp["status"] = "pending";
                }
            }else{
                $ws_add = true;
                $ws_temp = [
                    "ws_number" => $ws["ws_number"],
                    "technician_name" => "",
                    "submitted_at" => "",
                    "findings" => "Not yet assigned to any technician.",
                    "status" => "pending"
                ];
            }

            array_push($pgt["workstations"],$ws_temp);
        }


        $dt = new DateTime($pgt["incident_datetime"]);
        $year = $dt->format('Y');

        if((new DateTime())->format('Y') == $year || $ws_add){
            array_push($pg_ticket_final,$pgt);
        }
        if(!$ws_add){
            $ticket_closed++;
        }
    }

    echo json_encode([$pg_ticket_final,$ws_resolved,$ticket_closed]);
