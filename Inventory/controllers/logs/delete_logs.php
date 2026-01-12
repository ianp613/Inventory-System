<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);
    $log = new Logs;

    if($data["uid"] == "All"){
        DB::wipe($log);
        // $temp = DB::all($log);
        // foreach ($temp as $t) {
        //     DB::delete($log,$t["id"]);
        // }
    }else{
        $temp = DB::where($log,"uid","=",$data["uid"]);
        foreach ($temp as $t) {
            DB::delete($log,$t["id"]);
        }
    }
    $response = [
        "status" => true,
        "message" => "Logs has been cleared for user width ID ".$data["uid"]
    ];

    echo json_encode($response);
?>