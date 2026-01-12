<?php
    header('Content-Type: application/json');
    include("../../includes.php");

    $users = new User;
    $response = DB::all($users);

    echo json_encode($response);
?>