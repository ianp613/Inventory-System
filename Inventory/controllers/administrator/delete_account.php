<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $models_ = [
        "Consumable_Request",
        "Settings"
    ];

    foreach($models_ as $m){
        $model = new $m;
        $model_data = DB::where($model,"uid","=",$data["id"]);
        foreach($model_data as $md){
            DB::delete($model,$md["id"]);
        }
    }

    $group_ = new User_Group;
    $group = DB::all($group_);
    foreach ($group as $g) {
        $id_temp = explode("|",$g["supervisors"]);
        $key = array_search($data["id"], $id_temp);
        if ($key !== false) {
            unset($id_temp[$key]);

            $group_prep = DB::prepare($group_,$g["id"]);
            $group_prep->supervisors = implode("|",$id_temp) ? implode("|",$id_temp) : "|";
            DB::update($group_prep);
        }
    }
    
    foreach ($group as $g) {
        $id_temp = explode("|",$g["users"]);
        $key = array_search($data["id"], $id_temp);
        if ($key !== false) {
            unset($id_temp[$key]);

            $group_prep = DB::prepare($group_,$g["id"]);
            $group_prep->users = implode("|",$id_temp) ? implode("|",$id_temp) : "|";
            DB::update($group_prep);
        }
    }

    if($data) {
        $user = new User;
        DB::delete($user,$data["id"]);
        
        $response = [
            "status" => true,
            "type" => "info",
            "size" => null,
            "message" => "User account has been deleted.",
        ];
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