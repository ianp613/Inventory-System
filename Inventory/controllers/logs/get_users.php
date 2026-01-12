<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $user = new User;

    echo json_encode(DB::all($user));
?>