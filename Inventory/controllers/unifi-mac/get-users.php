<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_user:all";
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        $users = json_decode($cache_data, true);
        // if the cached response was wrapped (from get_users.php), unwrap it to raw array
        echo json_encode(isset($users["status"]) ? $users : $users);
        exit;
    }

    $user = new User;
    $users = DB::all($user);

    $redis->setex($cache_key, 300, json_encode($users));

    echo json_encode($users);
?>