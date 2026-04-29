<?php
    header('Content-Type: application/json');
    session_start();
    include("../../includes.php");
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $userid = $data['userid'];
        $password = $data['password'];
        $user = new User;
        // $auth = DB::auth($user,$userid,$password);
        $auth = false;

        $users = DB::where($user,"username","=",$userid);

        if(count($users) == 1){
            $hash = $users[0]["password"];
            if(Data::decrypt($password,$hash)){
                $auth = true;
            }
        }
        
        if($auth){
            $user = DB::where($user,"username","=",$userid);
            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "Welcome ",
                "user" => $user
            ];
             
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
