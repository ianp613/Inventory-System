<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);
    $log = new Logs;
    $logs = [];

    if($data["logs"] != "All"){
        $logs = DB::where($log,"uid","=",$data["logs"],"created_at","desc");
    }else{
        // $logs = $_SESSION["privileges"] == "Administrator" ? DB::all($log,"created_at","desc") : DB::where($log,"uid","=",$_SESSION["userid"],"created_at","desc");
        if($_SESSION["privileges"] == "Administrator"){
            $logs = DB::all($log,"created_at","desc");
        }elseif($_SESSION["privileges"] == "Supervisor"){
            if($_SESSION["g_id"]){
                $group = new User_Group;
                $group = DB::find($group,$_SESSION["g_id"]);
                
                $supervisor = explode("|",$group[0]["supervisors"]);
                $user = explode("|",$group[0]["users"]);
                
                $users = array_merge($supervisor,$user);

                foreach ($users as $use) {
                    $logs = array_merge($logs,DB::where($log,"uid","=",$use,"created_at","desc"));
                }
            }
        }else{
            $logs = DB::where($log,"uid","=",$_SESSION["userid"],"created_at","desc");
        }
    }
    $response = [
        "status" => true,
        "logs" => $logs
    ];

    echo json_encode($response);
?>