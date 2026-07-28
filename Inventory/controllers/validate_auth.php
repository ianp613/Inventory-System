<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");

    if (!array_key_exists("operate_as_group", $_SESSION)) {
        $_SESSION["operate_as_group"] = null;
    }

    $response = [
        "status"  => false,
        "type"    => "error",
        "size"    => null,
        "message" => "Invalid authentication!"
    ];

    if (array_key_exists("auth", $_SESSION)) {
        if ($_SESSION["auth"]) {
            $group = null;

            if ($_SESSION["g_member"]) {
                $group_cache_key = "icore_group:" . $_SESSION["g_id"];
                $cached_group = $redis->get($group_cache_key);

                if ($cached_group !== null) {
                    $group = json_decode($cached_group, true);
                } else {
                    $group = new User_Group;
                    $group = DB::find($group, $_SESSION["g_id"]);
                    $redis->setex($group_cache_key, 300, json_encode($group)); // 5 min, groups rarely change
                }

                $_SESSION["g_name"] = $group[0]["group_name"];
                $_SESSION["g_id"]   = $group[0]["id"];
                $_SESSION["g_type"] = $group[0]["type"];
            }

            $user_cache_key = "icore_user:" . $_SESSION["userid"];
            $cached_user = $redis->get($user_cache_key);

            if ($cached_user !== null) {
                $user = json_decode($cached_user, true);
            } else {
                $user = new User;
                $user = DB::find($user, $_SESSION["userid"]);
                $redis->setex($user_cache_key, 60, json_encode($user)); // 60s — short, since privileges/passkey live here
            }

            if (!$_SESSION["operate_as_group"]) {
                $_SESSION["privileges"] = $user[0]["privileges"];
            }

            $response = [
                "status"   => true,
                "type"     => "success",
                "size"     => null,
                "message"  => "Welcome",
                "g_member" => $_SESSION["g_member"],
                "user"     => $user,
                "group"    => $group
            ];
        }
    }

    echo json_encode($response);
?>