<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $user = new User;

    if($_SESSION["g_member"]){
        $users = [];
        $group = new User_Group;
        $group = DB::find($group,$_SESSION["g_id"])[0];
        $supervisors = explode("|",$group["supervisors"]);
        foreach ($supervisors as $sup) {
            $temp = DB::find($user,$sup);
            if(count($temp)){
                array_push($users,$temp[0]);
            }
        }
        $users_ = explode("|",$group["users"]);
        foreach ($users_ as $use) {
            $temp = DB::find($user,$use);
            if(count($temp)){
                array_push($users,$temp[0]);
            }
        }
    }else{
        $users =  DB::all($user);
    }

    echo json_encode($users);
?>