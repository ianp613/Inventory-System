<?php
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_accounts_dropdown:all";
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $user = new User;
    $supervisors = [];
    $users = [];

    $supervisor_temp = DB::where($user,"privileges","=","Supervisor");
    $users_temp = DB::where($user,"privileges","=","User");

    $usergroup = new User_Group;
    $usergroup_data = DB::all($usergroup);

    foreach ($supervisor_temp as $st) {
        $add_id = true;
        foreach ($usergroup_data as $ug) {
            $ids = explode("|",$ug["supervisors"]);
            if(in_array($st["id"],$ids)){
                $add_id = false;
            }
        }

        if($add_id){
            array_push($supervisors,$st);
        }
    }
    foreach ($users_temp as $ut) {
        $add_id = true;
        foreach ($usergroup_data as $ug) {
            $ids = explode("|",$ug["users"]);
            if(in_array($ut["id"],$ids)){
                $add_id = false;
            }
        }

        if($add_id){
            array_push($users,$ut);
        }
    }
    
    $response = [
        "status" => true,
        "supervisor" => $supervisors,
        "user" => $users,
    ];

    $redis->setex($cache_key, 300, json_encode($response));

    echo json_encode($response);
?>