<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $post_it = new Post_It;
    $post_it = DB::all($post_it);
    shuffle($post_it);

    echo json_encode($post_it);
?>