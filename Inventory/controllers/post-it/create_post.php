<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => true,
        "type" => "success",
        "size" => null,
        "message" => "Your message has been posted.",
        "data" => $data
    ];

    $post_it = new Post_It;
    $post_it->name = $data["name"];
    $post_it->recipient = $data["recipient"];
    $post_it->message = $data["message"];
    DB::save($post_it);
    $redis->del("icore_post_it:all");

    echo json_encode($response);
?>