<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");

    if(!array_key_exists("operate_as_group",$_SESSION)){
        $_SESSION["operate_as_group"] = null;
    }
    
    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Invalid authentication!"
    ]; 
    if(array_key_exists("auth",$_SESSION)){
        if($_SESSION["auth"]){
            $group = null;
            if($_SESSION["g_member"]){
                $group = new User_Group;
                $group = DB::find($group,$_SESSION["g_id"]);
                $_SESSION["g_name"] = $group[0]["group_name"];
                $_SESSION["g_id"] = $group[0]["id"];
                $_SESSION["g_type"] = $group[0]["type"];
            }

            $user = new User;
            $user = DB::find($user, $_SESSION["userid"]);
            if(!$_SESSION["operate_as_group"]){
                $_SESSION["privileges"] = $user[0]["privileges"];    
            }
            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "Welcome",
                "g_member" => $_SESSION["g_member"],
                "user" => $user,
                "group" => $group
            ]; 
        }    
    }
    echo json_encode($response);
?>
