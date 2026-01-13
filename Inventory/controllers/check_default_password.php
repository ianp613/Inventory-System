<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    echo json_encode(Data::decrypt("12345",$data["password"]));
?>
