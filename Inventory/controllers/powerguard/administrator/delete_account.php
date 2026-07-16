<?php
    header('Content-Type: application/json');
    session_start();
    include("../../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $pg_user = new PG_User;
    $pg_user_ = DB::find($pg_user,$data["user_id"]);
    if($pg_user_[0]["privileges"] == "supervisor"){
        $pg_dept = new PG_Department;
        $pg_dept_ = DB::where($pg_dept,"sup_id","=",$pg_user_[0]["id"]);
        foreach($pg_dept_ as $pgt){
            $pgt_ = DB::prepare($pg_dept,$pgt["id"]);
            $pgt_->sup_id = "-";
            DB::update($pgt_);
        }
    }

    if($pg_user_[0]["privileges"] == "technician"){
        $pg_ws = new PG_WS;
        $pg_ws_ = DB::where($pg_ws,"tech_id","=",$pg_user_[0]["id"]);
        foreach($pg_ws_ as $pgw){
            $pgw_ = DB::prepare($pg_ws,$pgw["id"]);
            $pgw_->tech_id = "-";
            DB::update($pgw_);
        }
    }

    DB::delete($pg_user,$data["user_id"]);

    $response = [
        "status" => true,
        "title" => "Account Deleted!",
        "type" => "info",
        "message" => "Account of ".$pg_user_[0]["fname"][0].". ".$pg_user_[0]["lname"]." has been deleted."
    ];

    echo json_encode($response);