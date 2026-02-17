<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);
    $log = new Logs;
    $logs = [];

    if($data["logs"] != "All" && $data["logs"] != "Others"){
        $logs = DB::where($log,"uid","=",$data["logs"],"created_at","desc");
    }else{
        if($_SESSION["privileges"] == "Administrator"){
            $logs = DB::all($log,"created_at","desc");
        }else{
            if($_SESSION["g_id"]){
                $group = new User_Group;
                $group = DB::find($group,$_SESSION["g_id"]);
                
                $supervisor = explode("|",$group[0]["supervisors"]);
                $user = explode("|",$group[0]["users"]);
                
                $users = array_merge($supervisor,$user);

                // get logs and user that is a group member
                foreach ($users as $use) {
                    $logs = array_merge($logs,DB::where($log,"uid","=",$use,"created_at","desc"));
                }

                // get log ids of logs of the users that are a group member
                $temp_logs_id = [];
                foreach($logs as $l){
                    array_push($temp_logs_id,$l["id"]);
                }

                // get all logs with the same g_id, and separate all logs that are not present in the user members
                $log_others_ = [];
                $log_ = DB::where($log,"gid","=",$_SESSION["g_id"]);
                foreach ($log_ as $l_) {
                    if(!in_array($l_["id"],$temp_logs_id) && stripos($l_["log"], "administrator") === false){
                        array_push($log_others_,$l_);
                    }
                }
                if($data["logs"] != "Others"){
                    $logs = array_merge($logs,$log_others_);
                }else{
                    $logs = $log_others_;
                }
            }
        }
    }
    $response = [
        "status" => true,
        "logs" => $logs
    ];
    echo json_encode($response);
?>