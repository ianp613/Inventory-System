<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    if(!array_key_exists("operate_as_group",$_SESSION)){
        $_SESSION["operate_as_group"] = null;
    }
    
    $response = [
        "operate_as" => $_SESSION["operate_as_group"]
    ];

    echo json_encode($response);
?>