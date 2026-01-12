<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $groups = new User_Group;
    $groups = DB::all($groups);
    $response = [
        "status" => true,
        "groups" => $groups
    ];

    echo json_encode($response);
?>