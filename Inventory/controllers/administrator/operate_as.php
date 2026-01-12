<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $_SESSION["operate_as_group"] = true;
        $_SESSION["g_member"] = true;
        $_SESSION["privileges"] = "Supervisor";

        $group = new User_Group;
        $group = DB::find($group,$data["gid"]);
        $_SESSION["g_name"] = $group[0]["group_name"];
        $_SESSION["g_id"] = $group[0]["id"];
        $_SESSION["g_type"] = $group[0]["type"];

        $response = [
            "status" => true,
            "type" => "success",
            "size" => null,
            "message" => "Applying membership ..."
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