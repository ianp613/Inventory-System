<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $ping = file_get_contents("../../assets/files/ping.json");
    $ping_temp = json_decode($ping);
    $group = $data["group"];
    
    echo json_encode($ping_temp->$group);
?>