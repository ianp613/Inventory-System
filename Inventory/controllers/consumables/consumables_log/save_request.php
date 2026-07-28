<?php
    session_start();
    header('Content-Type: application/json');
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    // Invalidate every cached variant for this group (group view + all personal views)
    $g_id = $data["gid"] ?? "";
    $keys = $redis->keys("icore_consumable_request:{$g_id}:*");
    foreach ($keys as $key) {
        $redis->del($key);
    }

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
    
    if($consumables[0]["stock"] >= $data["requested_quantity"] && $data["requested_quantity"] != 0){
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
        // $consumable_temp = DB::prepare($consumable,$data["cid"]);
        // $consumable_temp->stock -= $data["requested_quantity"];
        // DB::update($consumable_temp);

        // Add consumable log
        $consumable_request = new Consumable_Request;
        $consumable_request->gid = $data["gid"];
        $consumable_request->uid = $users[0]["id"];
        $consumable_request->cid = $data["cid"];
        $consumable_request->date = $data["date_today"];
        $consumable_request->time = $data["time_today"];
        $consumable_request->requested_quantity = $data["requested_quantity"];
        $consumable_request->remarks = $data["remarks"] ? $data["remarks"] : "-";
        $consumable_request->status = "For Approval";
        $consumable_request->declined_remarks = "-";
        DB::save($consumable_request);

        $response["status"] = true;
        $response["message"] = "Request has been submitted.";
    }
    
    echo json_encode($response);

?>