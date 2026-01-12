<?php
    session_start();
    header('Content-Type: application/json');
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => false
    ];
    if(file_exists("../links/".$data["link"])){
        $link_data = json_decode(file_get_contents("../links/".$data["link"]), true);
        $_SESSION["g_id_log"] = $link_data["g_id"];
        $response = [
            "status" => true,
            "g_id" => $link_data["g_id"],
            "g_name" => $link_data["g_name"]
        ];
    }
    echo json_encode($response);

?>