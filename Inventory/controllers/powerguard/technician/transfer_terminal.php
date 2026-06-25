<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    
    $pg_user = new PG_User;
    $pg_user = DB::find($pg_user,$data["tech_id"]);

    $response = [
        "status" => true,
        "title" => "Transfer Success!",
        "type" => "success",
        "message" => "Workstation ".$data["ws"]." has been successfully transferred to technician ".$data["tech_name"].".",
    ];

    if(!count($pg_user)){
        $response = [
            "status" => false,
            "title" => "Tranfer Failed!",
            "type" => "error",
            "message" => "Attempt to transfer workstation ".$data["ws"]." to technician ".$data["tech_name"]." has failed.",
        ];
        echo json_encode($response);
        exit;
    }

    $pg_ws = new PG_WS;
    $pg_ws = DB::prepare($pg_ws,$data["ws_id"]);
    $pg_ws->tech_id = $data["tech_id"];
    DB::update($pg_ws);

    echo json_encode($response);