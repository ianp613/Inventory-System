<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data["exit"]) {
        $_SESSION["operate_as_group"] = null;
        $_SESSION["g_member"] = null;
        $_SESSION["privileges"] = "Administrator";

        $_SESSION["g_name"] = null;
        $_SESSION["g_id"] = null;
        $_SESSION["g_type"] = null;

        $response = [
            "status" => true,
            "type" => "success",
            "size" => null,
            "message" => "Removing membership ..."
        ];
    }else{
        $response = [
           "status" => true,
            "type" => "error",
            "size" => null,
            "message" => "Something went wrong." 
        ];
    }
    echo json_encode($response);
?>