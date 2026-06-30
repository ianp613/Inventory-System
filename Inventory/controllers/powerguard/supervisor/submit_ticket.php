<?php
   header('Content-Type: application/json');
   session_start();
   include("../../../includes.php");
   $data = json_decode(file_get_contents('php://input'), true);

   $response = [
        "status" => true,
        "title" => "Ticket Submitted!",
        "type" => "success",
        "message" => "You ticket has been submitted, please wait for technician to review and resolve your ticket."
    ];

   $pg_ticket = new PG_Ticket;

   $dt = new DateTime($data["incident_datetime"]);
   $year = $dt->format('Y');
   $ticket_no = $year."-".Data::generate(6,"numeric");
   while(!DB::validate($pg_ticket,"ticket_no",$ticket_no)){
      $ticket_no = $year."-".Data::generate(6,"numeric");
   }

   $pg_ticket->dept_id             = $data["dept_id"];
   $pg_ticket->ticket_no           = $ticket_no;
   $pg_ticket->incident_datetime   = $data["incident_datetime"];
   $pg_ticket->fluctuation_type    = $data["fluctuation_type"];  
   $pg_ticket->priority            = $data["priority"];
   $pg_ticket->area                = $data["area"] ? $data["area"] : "-";
   $pg_ticket->duration_minutes    = $data["duration_minutes"];
   $pg_ticket->description         = $data["description"];
   $ticket_id = DB::save($pg_ticket);

   $count = 0;
   foreach ($data["workstations"] as $ws) {
      $pg_ws = new PG_WS;
      $pg_ws->ticket_id            = $ticket_id;
      $pg_ws->ws_number            = $ws["ws_number"];
      $pg_ws->assigned_user        = $ws["assigned_user"];
      $pg_ws->ups_status           = $ws["ups_status"];
      $pg_ws->system_unit_status   = $ws["system_unit_status"];
      $pg_ws->monitor_status       = $ws["monitor_status"];
      $pg_ws->notes                = $ws["notes"] ? $ws["notes"] : "-";
      $pg_ws->sign_off_queue       = "pending"; 
      $pg_ws->tech_id              = "-";
      DB::save($pg_ws);
   }

    echo json_encode($response);



    
