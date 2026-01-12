<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $user = new User;
    $group = new User_Group;
    $users = [];

    if($_SESSION["g_member"]){
        $group = DB::find($group, $_SESSION["g_id"]);

        // GET ALL USERS
        $user_temp = explode("|",$group[0]["users"]);
        foreach ($user_temp as $ut) {
            if(DB::find($user,$ut)){
                array_push($users,DB::find($user,$ut)[0]);
            }
        }
        // GET ALL SUPERVISOR
        $supervisor_temp = explode("|",$group[0]["supervisors"]);

        foreach ($supervisor_temp as $st) {
            if(DB::find($user,$st)){
                array_push($users,DB::find($user,$st)[0]);
            }
        }
    }else{
        $users = DB::all($user);    
    }


    $response = [
        "status" => true,
        "user" => $users,
        "g_id" => $_SESSION["g_id"]
    ];

    echo json_encode($response);
?>