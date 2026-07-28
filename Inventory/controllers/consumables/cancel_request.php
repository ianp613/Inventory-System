<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $g_id = $_SESSION["g_id"] ?? "";

    // clear every cached variant for this group (group view + all personal views)
    $keys = $redis->keys("icore_consumable_request:{$g_id}:*");
    foreach ($keys as $key) {
        $redis->del($key);
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status"  => false,
        "type"    => "error",
        "size"    => null,
        "message" => "Something went wrong."
    ];

    $consumable_request = new Consumable_Request;
    DB::delete($consumable_request, $data["id"]);

    $response["status"]  = true;
    $response["type"]    = "info";
    $response["message"] = "Request has been canceled.";
    echo json_encode($response);
?>