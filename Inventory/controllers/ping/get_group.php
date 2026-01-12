<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $ping = file_get_contents("../../assets/files/ping.json");
    $ping_temp = json_decode($ping, true);
    $groups = array_keys($ping_temp);
    echo json_encode($groups);
?>