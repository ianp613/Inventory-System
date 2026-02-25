<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $request = new Consumable_Request;

    $consumables = new Consumables;
    $consumables = DB::all($consumables);

    if($data["type"] == "group" && $_SESSION["c_authority"] == "true"){
        $users = new User;
        $users = DB::all($users);

        $request = $_SESSION["g_id"] ? DB::where($request,"gid","=",$_SESSION["g_id"]) : DB::all($request);

        $response = [
            "status" => true,
            "users" => $users,
            "requests" => $request,
            "consumables" => $consumables,
        ];
    }else{
        $groups = new User_Group;
        $groups = DB::all($groups);

        $request = DB::where($request,"uid","=",$_SESSION["userid"]);

        $response = [
            "status" => true,
            "groups" => $groups,
            "requests" => $request,
            "consumables" => $consumables,
        ];
    }
    echo json_encode($response);
?>