<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ticket_ = new PG_Ticket;
    $pg_ticket_ = DB::all($pg_ticket_);
    $pg_ticket = [];

    $pg_dept = new PG_Department;
    $pg_dept = DB::where($pg_dept,"sup_id","=",$data["sup_id"]);
    $pg_dept_id = [];

    // get departments id
    foreach ($pg_dept as $pgd) {
        array_push($pg_dept_id,$pgd["id"]);
    }

    // get matching ticket with matcing dept id
    foreach ($pg_ticket_ as $pgt_) {
        if(in_array($pgt_["dept_id"],$pg_dept_id)){
            array_push($pg_ticket,$pgt_);
        }
    }

    

    $pg_ws = new PG_WS;
    $pg_ticket_final = [];
    $ws_resolved = 0;
    $ticket_closed = 0;
    $ticket_open = 0;
    foreach ($pg_ticket as $pgt) {
        $pgt["resolved_count"] = 0;
        $pgt["workstations"] = [];
        $pgt["status"] = "pending";
        $pg_ws_temp = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        $ws_temp = [
            "ws_number" => "",
            "technician_name" => "",
            "submitted_at" => "",
            "findings" => "",
            "status" => "pending",
            "id" => ""
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
                    "status" => $ws["sign_off_queue"],
                    "id" => $ws["id"]
                ];

                $ws_assessment = new PG_WS_Assessment;
                $ws_assessment = DB::where($ws_assessment,"ws_id","=",$ws["id"]);

                if($ws["sign_off_queue"] == "done" && $ws["tech_id"] != "-"){
                    $pgt["resolved_count"]++;
                }

                // if(count($ws_assessment) && $ws["sign_off_queue"] != "draft"){
                if(count($ws_assessment)){
                    // if($ws["sign_off_queue"] != "rejected"){
                    //     continue;
                    // }
                    

                    // $ws_temp["submitted_at"] = $ws_assessment[0]["assessed_at"];
                    // $ws_temp["findings"] = $ws_assessment[0]["ups_condition"] != "Functional" && $ws_assessment[0]["ups_condition"] != "-" ? "UPS ".$ws_assessment[0]["ups_condition"] : "" ;
                    // $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["ups_condition"] != "Functional" ? " • " : "").($ws_assessment[0]["system_unit_condition"] != "Functional" && $ws_assessment[0]["system_unit_condition"] != "-" ? "System Unit ".$ws_assessment[0]["system_unit_condition"] : "");
                    // $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["system_unit_condition"] != "Functional" ? " • " : "").($ws_assessment[0]["monitor_condition"] != "Functional" && $ws_assessment[0]["monitor_condition"] != "-" ? "Monitor ".$ws_assessment[0]["monitor_condition"] : "");
                    // $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["monitor_condition"] != "Functional" && $ws_assessment[0]["technical_findings"] != "-" ? " • " : "").($ws_assessment[0]["technical_findings"] != "-" ? $ws_assessment[0]["technical_findings"] : "");

                    $ws_temp["submitted_at"] = $ws_assessment[0]["assessed_at"];
                    $ws_temp["findings"] = $ws_assessment[0]["ups_condition"] != "Functional" && $ws_assessment[0]["ups_condition"] != "-" ? "UPS ".$ws_assessment[0]["ups_condition"] : "" ;
                    $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["ups_condition"] != "Functional" ? " • " : "").($ws_assessment[0]["system_unit_condition"] != "Functional" && $ws_assessment[0]["system_unit_condition"] != "-" ? "System Unit ".$ws_assessment[0]["system_unit_condition"] : "");
                    $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["system_unit_condition"] != "Functional" ? " • " : "").($ws_assessment[0]["monitor_condition"] != "Functional" && $ws_assessment[0]["monitor_condition"] != "-" ? "Monitor ".$ws_assessment[0]["monitor_condition"] : "");
                    $ws_temp["findings"] .= ($ws_temp["findings"] && $ws_assessment[0]["monitor_condition"] != "Functional" && $ws_assessment[0]["technical_findings"] != "-" ? " • " : "").($ws_assessment[0]["technical_findings"] != "-" ? $ws_assessment[0]["technical_findings"] : "");

                    // add parts_needed only if not empty
                    $parts = $ws_assessment[0]["parts_needed"] != "-" && $ws_assessment[0]["parts_needed"] != "" ? $ws_assessment[0]["parts_needed"] : "";
                    if($parts){
                        $ws_temp["findings"] .= ($ws_temp["findings"] ? " • " : "")."Parts needed: ".$parts;
                    }

                    $escalate = $ws_assessment[0]["escalate_to"] != "-" && $ws_assessment[0]["escalate_to"] != "" ? $ws_assessment[0]["escalate_to"] : "";
                    if($escalate){
                        $ws_temp["findings"] .= ($ws_temp["findings"] ? " • " : "")."Escalate to: ".$escalate;
                    }

                    if($ws["sign_off_queue"] == "done"){
                        $ws_resolved++;
                    }
                    if(!$ws_temp["findings"]){
                        $ws_temp["findings"] = "This workstation is in good condition and operating normally.";
                    }

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
                    "status" => "pending",
                    "id" => $ws["id"]
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

        if((new DateTime())->format('Y') == $year || $ws_add){
            array_push($pg_ticket_final,$pgt);
        }
        if(!$ws_add){
            $ticket_closed++;
        }else{
            $ticket_open++;
        }
    }

    usort($pg_ticket_final, function ($a, $b) {
        if ($a['status'] === 'closed' && $b['status'] !== 'closed') {
            return 1; // Move "closed" after everything else
        }

        if ($a['status'] !== 'closed' && $b['status'] === 'closed') {
            return -1; // Keep non-"closed" before "closed"
        }

        return 0; // Keep original order for other statuses
    });

    echo json_encode([$pg_ticket_final,$ws_resolved,$ticket_closed,$ticket_open]);
