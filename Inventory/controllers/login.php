<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $userid = $data['userid'];
        $password = $data['password'];
        $user = new User;
        $auth = DB::auth($user,$userid,$password);

        if($auth){
            $user = DB::where($user,"username","=",$userid);
            $_SESSION["auth"] = true;
            $_SESSION["userid"] = $user[0]["id"];
            $_SESSION["name"] = $user[0]["name"];
            $_SESSION["privileges"] = $user[0]["privileges"];


            // Check if user is part of a group

            $group = new User_Group;
            $group = DB::all($group);
            if($user[0]["privileges"] != "Administrator"){
                foreach ($group as $g) {
                    $id_temp = explode("|",$g["supervisors"]);
                    if(in_array($user[0]["id"],$id_temp)){
                        $_SESSION["g_member"] = true;
                        $_SESSION["g_name"] = $g["group_name"];
                        $_SESSION["g_id"] = $g["id"];
                        $_SESSION["g_type"] = $g["type"];
                    }
                }
                foreach ($group as $g) {
                    $id_temp = explode("|",$g["users"]);
                    if(in_array($user[0]["id"],$id_temp)){
                        $_SESSION["g_member"] = true;
                        $_SESSION["g_name"] = $g["group_name"];
                        $_SESSION["g_id"] = $g["id"];
                        $_SESSION["g_type"] = $g["type"];
                    }
                }
            }else{
                $_SESSION["g_member"] = false;
            }
            
            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $user[0]["id"];
            $log->log = $user[0]["name"]." has logged into the system.";
            DB::save($log);

            if($user[0]["privileges"] != "Administrator"){
                if($_SESSION["g_member"]){
                    $response = [
                        "status" => true,
                        "type" => "success",
                        "size" => null,
                        "message" => "Welcome",
                        "g_member" => true,
                        "user" => $user
                    ];    
                }else{
                    $response = [
                        "status" => true,
                        "type" => "info",
                        "size" => "lg",
                        "message" => "Your account is currently inactive, and is not assigned to any office yet. Please ask your supervisor for additional info.",
                        "g_member" => false,
                        "user" => $user
                        
                    ];
                    $_SESSION["auth"] = false;
                }
            }else{
                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Welcome",
                    "g_member" => true,
                    "user" => $user
                ];
            }
             
        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "g_member" => false,
                "message" => "Invalid User ID and Password"
            ];    
        }
    }
    echo json_encode($response);
?>
