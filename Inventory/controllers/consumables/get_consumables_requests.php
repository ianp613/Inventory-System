<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $request = new Consumable_Request;

    

    if($data["type"] != "user"){
        $users = new User;
        $users = DB::all($users);
    }else{
        $groups = new User_Group;
        $groups = DB::all($groups);

        $request = DB::where($request,"uid","=",$_SESSION["userid"]);
        
        $consumables = new Consumables;
        $consumables = $_SESSION["g_id"] ? DB::where($consumables,"gid","=",$_SESSION["g_id"]) : DB::all($consumables);

        $response = [
            "status" => true,
            "groups" => $groups,
            "requests" => $request,
            "consumables" => $consumables,
        ];
    }
    echo json_encode($response);
?>