<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $cache_key = "icore_post_it:all";
    $cache_data = $redis->get($cache_key);

    if ($cache_data !== null) {
        $post_it = json_decode($cache_data, true);
    } else {
        $post_it = new Post_It;
        $post_it = DB::all($post_it);
        $redis->setex($cache_key, 300, json_encode($post_it));
    }

    shuffle($post_it);

    echo json_encode($post_it);
?>