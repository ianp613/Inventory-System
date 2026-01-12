<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if(file_exists("links/".$data["link"])){
            unlink("links/".$data["link"]);
        }
    }
    echo json_encode(["status" => true]);
?>