<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $wifi = new Wifi;
    $group = new User_Group;
    $group = DB::where($group,"group_name","like","%wifi%");

    $wifis = count($group) ? DB::where($wifi,"gid","=",$group[0]["id"]) : [];
    
    $response = [
        "status" => true,
        "wifis" => $wifis,
        "g_id" => count($group) ? $group[0]["id"] : null
    ];    
    echo json_encode($response);
?>