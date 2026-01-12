<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $logs = new Consumable_Log;
    $logs = $_SESSION["g_id"] ? DB::where($logs,"gid","=",$_SESSION["g_id"]) : DB::all($logs);

    $users = new User;
    $users = DB::all($users);

    $consumables = new Consumables;
    $consumables = $_SESSION["g_id"] ? DB::where($consumables,"gid","=",$_SESSION["g_id"]) : DB::all($consumables);

    $response = [
        "status" => true,
        "logs" => $logs,
        "users" => $users,
        "consumables" => $consumables,
    ];
        
    echo json_encode($response);
?>