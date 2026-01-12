<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($data) {
        $bol = true;
        $user_account = new User;
        $group = new User_Group;
        $group = DB::prepare($group,$data["id"]);
        $group_temp = DB::where($group,"group_name","=",$data["group_name"]);

        if(count($group_temp)){
            if($group_temp[0]["id"] != $data["id"]){
                $bol = false;
            }
        }

        if($bol){
            // Supervisors
            $supervisors_temp = [];
            $supervisors = [];
            foreach ($data["supervisor"] as $sud) {
                $su_ex = explode(" - ",$sud)[0];
                array_push($supervisors_temp,$su_ex);
            }
            foreach ($supervisors_temp as $sut) {
                $user_account_temp = DB::where($user_account,"username","=",$sut);
                if(count($user_account_temp)){
                    if(strtolower($user_account_temp[0]["privileges"]) != "supervisor"){
                        $utu = DB::prepare($user_account,$user_account_temp[0]["id"]);
                        $utu->privileges = "Supervisor";
                        DB::update($utu);
                    }
                    array_push($supervisors,$user_account_temp[0]["id"]);
                }
            }

            // Users
            $users_temp = [];
            $users = [];
            foreach ($data["user"] as $sud) {
                $su_ex = explode(" - ",$sud)[0];
                array_push($users_temp,$su_ex);
            }
            foreach ($users_temp as $sut) {
                $user_account_temp = DB::where($user_account,"username","=",$sut);
                if(count($user_account_temp)){
                    if(strtolower($user_account_temp[0]["privileges"]) != "user"){
                        $utu = DB::prepare($user_account,$user_account_temp[0]["id"]);
                        $utu->privileges = "User";
                        DB::update($utu);
                    }
                    array_push($users,$user_account_temp[0]["id"]);
                }
            }

            $group->group_name = $data["group_name"];
            $group->type = $data["type"];
            // $group->supervisors = implode("|",$supervisors) ? implode("|",$supervisors) : "|";
            // $group->users = implode("|",$users) ? implode("|",$users) : "|";
            DB::update($group);

            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "Group has been updated."
            ];    
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => null,
                "message" => "Group already exist."
            ];
        }
    }else{
        $response = [
            "status" => false,
            "type" => "error",
            "size" => null,
            "message" => "Something went wrong."
        ];
    }
    echo json_encode($response);
?>