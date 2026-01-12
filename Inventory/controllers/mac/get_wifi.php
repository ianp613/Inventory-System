<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $wifi = new Wifi;
    $wifi = $_SESSION["g_id"] ? DB::where($wifi,"gid","=",$_SESSION["g_id"]) : DB::all($wifi);

    if(count($wifi) > 1){
        array_push($wifi,["id" => "Show All", "name" => "Show All"]);
    }

    $response = [
        "status" => true,
        "wifis" => $wifi
    ];    
    echo json_encode($response);
?>