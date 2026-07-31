<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $yk_reserved = new YK_Reserved;

    $song = DB::find($yk_reserved, $data["id"]);
    $rid = $song[0]["rid"] ?? null;

    $result = DB::delete($yk_reserved,$data["id"]);

    if ($rid !== null) {
        $redis->del("icore_yk_reserved:" . $rid);
    }

    echo json_encode($result);
?>