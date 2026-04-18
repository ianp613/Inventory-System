<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);
    $log = new Logs;
    $user = new User;

    $ids_all = "";
    $ids_user = "";

    if($data["uid"] == "All"){
        if($_SESSION["g_member"]){
            $users = [];
            $group = new User_Group;
            $group = DB::find($group,$_SESSION["g_id"])[0];
            $supervisors = explode("|",$group["supervisors"]);
            foreach ($supervisors as $sup) {
                $temp = DB::find($user,$sup);
                if(count($temp)){
                    array_push($users,$temp[0]);
                }
            }
            $users_ = explode("|",$group["users"]);
            foreach ($users_ as $use) {
                $temp = DB::find($user,$use);
                if(count($temp)){
                    array_push($users,$temp[0]);
                }
            }

            foreach ($users as $user) {
                $temp = DB::where($log,"uid","=",$user["id"]);
                $count_ = 1;
                foreach ($temp as $t) {
                    $ids_all .= $t["id"];
                    if($count_ != count($temp)) $ids_all .= ",";
                    $count_++;
                }

                if($ids_all){
                    $sql = "DELETE FROM `sql_table` WHERE `id` IN (".$ids_all.")";
                    DB::sql($log,$sql);
                }
            }
        }else{
            DB::wipe($log);
        }
    }else{
        $temp = DB::where($log,"uid","=",$data["uid"]);
        $count_ = 1;
        foreach ($temp as $t) {
            $ids_user .= $t["id"];
            if($count_ != count($temp)) $ids_user .= ",";
            $count_++;
        }
        if($ids_user){
            $sql = "DELETE FROM `sql_table` WHERE `id` IN (".$ids_user.")";
            DB::sql($log,$sql);
        }
    }
    $response = [
        "status" => true,
        "message" => "Logs has been cleared for user width ID ".$data["uid"]
    ];

    echo json_encode($response);
?>