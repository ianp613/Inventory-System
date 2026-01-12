<?php
    session_start();
    header('Content-Type: application/json');
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $response = [
        "status" => false,
        "message" => "Something went wrong."
    ];

    $pass1 = false;
    $pass2 = false;

    // check if user exist
    $user = new User;
    $users = DB::where($user,"username","=",$data["user_id"]);

    if(count($users)){
        if($users[0]["passkey"] == $data["passkey"]){
            $pass1 = true;
        }else{
            $response["message"] = "Passkey is invalid.";    
        }
    }else{
        $response["message"] = "User not found";
    }

    // check if have enought stock
    $consumable = new Consumables;
    $consumables = DB::find($consumable,$data["cid"]);
    
    if($consumables[0]["stock"] >= $data["quantity_deduction"] && $data["quantity_deduction"] != 0){
        $pass2 = true;
    }else{
        $response["message"] = "Insufficient stock for the quantity entered.";
    }

    if($pass1 && $pass2){
        // $pgroup = false;
        // // check if user is part of this group
        // $user_group = new User_Group;
        // $user_groups = DB::find($user_group,$data["gid"]);

        // // check supervisors
        // $supid = explode("|",$user_groups[0]["supervisors"]);
        // if(in_array($users[0]["id"],$supid)){
        //     $pgroup = true;
        // }
        // // check users
        // $useid = explode("|",$user_groups[0]["users"]);
        // if(in_array($users[0]["id"],$useid)){
        //     $pgroup = true;
        // }

        // if($pgroup){
        //     $response["message"] = "All Pass.";
        // }else{
        //     $response["message"] = "Invalid user.";
        // }


        // Update consumable stock
        $consumable_temp = DB::prepare($consumable,$data["cid"]);
        $consumable_temp->stock -= $data["quantity_deduction"];
        DB::update($consumable_temp);

        // Add consumable log
        $consumable_log = new Consumable_Log;
        $consumable_log->gid = $data["gid"];
        $consumable_log->uid = $users[0]["id"];
        $consumable_log->cid = $data["cid"];
        $consumable_log->date = $data["date_today"];
        $consumable_log->time = $data["time_today"];
        $consumable_log->quantity_deduction = $data["quantity_deduction"];
        $consumable_log->remarks = $data["remarks"] ? $data["remarks"] : "-";
        DB::save($consumable_log);

        $response["status"] = true;
        $response["message"] = "Log has been saved.";
    }
    
    echo json_encode($response);

?>