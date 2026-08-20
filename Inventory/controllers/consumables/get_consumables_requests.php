<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);

    // $key_suffix = ($_SESSION["g_id"] ?? "") . ":" . ($data["type"] ?? "personal");
    // if ($data["type"] !== "group" || $_SESSION["c_authority"] !== "true") {
    //     $key_suffix .= ":" . $_SESSION["userid"]; // personal view is per-user
    // }
    // $cache_key = "icore_consumable_request:" . $key_suffix;

    // $cache_data = $redis->get($cache_key);

    // if ($cache_data !== null) {
    //     echo $cache_data;
    //     exit;
    // }

    $request = new Consumable_Request;

    $consumables = new Consumables;
    $consumables = DB::all($consumables);

    if ($data["type"] == "group" && $_SESSION["c_authority"] == "true") {
        $users = new User;
        $users = DB::all($users);

        $request = $_SESSION["g_id"] ? DB::where($request, "gid", "=", $_SESSION["g_id"]) : DB::all($request);

        $response = [
            "status"      => true,
            "users"       => $users,
            "requests"    => $request,
            "consumables" => $consumables,
        ];
    } else {
        $groups = new User_Group;
        $groups = DB::all($groups);

        $request = DB::where($request, "uid", "=", $_SESSION["userid"]);

        $response = [
            "status"      => true,
            "groups"      => $groups,
            "requests"    => $request,
            "consumables" => $consumables,
        ];
    }

    // $redis->setex($cache_key, 300, json_encode($response));
    echo json_encode($response);
?>