<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $cache_key = "icore_yk_reserved:" . $data["id"];
    $cache_data = $redis->get($cache_key);

    if ($cache_data !== null) {
        echo $cache_data;
        exit;
    }

    $yk_reserved = new YK_Reserved;
    $yk_reserved = DB::where($yk_reserved,"rid","=",$data["id"]);

    $redis->set($cache_key, json_encode($yk_reserved)); // no expiry — cleared only on write

    echo json_encode($yk_reserved);
?>