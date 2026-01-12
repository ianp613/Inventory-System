<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");

    if(!is_dir("../assets/temp/")){
        mkdir("../assets/temp/");
    }

    $response = DB::export("../assets/temp/");

    echo json_encode($response);
?>