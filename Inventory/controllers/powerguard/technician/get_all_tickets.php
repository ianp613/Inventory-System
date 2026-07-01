<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_ticket = new PG_Ticket;
    $pg_ticket = DB::all($pg_ticket);

    $pg_ws = new PG_WS;
    $pg_ws = DB::all($pg_ws);
    
    $pg_tech = new PG_User;
    $pg_tech = DB::all($pg_tech);

    $pg_dept_all = new PG_Department;
    $pg_dept_all = DB::all($pg_dept_all);


    // Check all open ticket
    $pg_ticket_final = [];
    foreach ($pg_ticket as $pgt) {
        $pgt["status"] = "pending";
        $pgt["area"] = $pgt["area"] != "-" ? $pgt["area"] : "";

        $pg_ws = new PG_WS;
        $pg_ws = DB::where($pg_ws,"ticket_id","=",$pgt["id"]);
        $pg_ws_final = [];
        $add_ticket = false;
        foreach ($pg_ws as $pgw) {
            if($pgw["sign_off_queue"] != "done"){
                $add_ticket = true;
            }

            // Get tech details
            if($pgw["tech_id"] != "-"){
                $pgw["claimed_by"] = $pgw["tech_id"];
                $pg_user_temp = new PG_User;
                $pg_user_temp = DB::find($pg_user_temp,$pgw["tech_id"]);
                if(count($pg_user_temp)){
                    $pgw["technician_name"] = $pg_user_temp[0]["fname"][0].". ".$pg_user_temp[0]["lname"];
                }
            }else{
                $pgw["technician_name"] = null;
                $pgw["claimed_by"] = null;
            }
            $pgw["status"] = $pgw["sign_off_queue"];
            array_push($pg_ws_final,$pgw);
        }
        if($add_ticket){
            $pgt["workstations"] = $pg_ws_final;
            $pgt["dept_name"] = "No department";
            foreach ($pg_dept_all as $pgd) {
                if($pgd["id"] == $pgt["dept_id"]){
                    $pgt["dept_name"] = $pgd["name"];
                }
            }

            // Get supervisor name
            $pgt["supervisor_name"] = "No Supervisor";
            $pg_dept = new PG_Department;
            $pg_dept = DB::find($pg_dept,$pgt["dept_id"]);
            if(count($pg_dept)){
                $pg_user = new PG_User;
                $pg_user = DB::find($pg_user,$pg_dept[0]["sup_id"]);
                if(count($pg_user)){
                    $pgt["supervisor_name"] = $pg_user[0]["fname"][0].". ".$pg_user[0]["lname"];
                }
            }

            array_push($pg_ticket_final,$pgt);
        }
    }
    echo json_encode($pg_ticket_final);