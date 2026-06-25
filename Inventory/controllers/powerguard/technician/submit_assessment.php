<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => true,
        "title" => "",
        "type" => "success",
        "message" => ""
    ];

    $pgWSAssess = new PG_WS_Assessment;
    $new = true;
    if(!DB::validate($pgWSAssess,"ws_id",$data["ws_id"])){
        $pgWSAssess_ = DB::where($pgWSAssess,"ws_id","=",$data["ws_id"])[0];
        $pgWSAssess = DB::prepare($pgWSAssess,$pgWSAssess_["id"]);
        $new = false;
    }

    $pgWSAssess->ws_id = $data["ws_id"];
    $pgWSAssess->assessed_at = $data["assessed_at"] != "" ? $data["assessed_at"] : "-";
    $pgWSAssess->ups_condition = $data["ups_condition"] != "" ? $data["ups_condition"] : "-";
    $pgWSAssess->system_unit_condition = $data["system_unit_condition"] != "" ? $data["system_unit_condition"] : "-";
    $pgWSAssess->monitor_condition = $data["monitor_condition"] != "" ? $data["monitor_condition"] : "-";
    $pgWSAssess->technical_findings = $data["technical_findings"] != "" ? $data["technical_findings"] : "-";
    $pgWSAssess->parts_needed = $data["parts_needed"] != "" ? $data["parts_needed"] : "-";
    $pgWSAssess->escalate_to = $data["escalate_to"] != "" ? $data["escalate_to"] : "-";

    $pg_ws = new PG_WS;
    $pg_ws = DB::prepare($pg_ws,$data["ws_id"]);
    if($data["type"] == "submit"){
        $pg_ws->sign_off_queue = "submitted";
        $response["message"] = "Assessment for workstation ".$pg_ws->ws_number." has been submitted.";
    }else{
        $pg_ws->sign_off_queue = "draft";
        $response["message"] = "Assessment for workstation ".$pg_ws->ws_number." has been save as draft. You can continue the assessment later.";
    }
    DB::update($pg_ws);

    if($new){
        DB::save($pgWSAssess);
    }else{
        DB::update($pgWSAssess);
    }

    if($data["type"] == "submit"){
        $response["title"] = "Assessment Submitted!";
        $response["message"] = "Assessment for workstation ".$pg_ws->ws_number." has been submitted.";
    }else{
        $response["title"] = "Draft Saved!";
        $response["message"] = "Assessment for workstation ".$pg_ws->ws_number." has been save as draft. You can continue the assessment later.";
    }

    echo json_encode($response);