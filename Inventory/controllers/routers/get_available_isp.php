<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $router = new Routers;
    $router = $_SESSION["g_id"] ? DB::where($router,"gid","=",$_SESSION["g_id"]) : DB::all($router);

    $isp = new ISP;
    $isp = $_SESSION["g_id"] ? DB::where($isp,"gid","=",$_SESSION["g_id"]) : DB::all($isp);

    $isp_id = [];
    $isp_temp = [];
    
    // Get id of used isp
    foreach($router as $r){
        foreach ($isp as $i) {
            if($r["wan1"] == $i["id"] || $r["wan2"] == $i["id"]){
                array_push($isp_id,$i["id"]);
            }
        }
    }

    // Check if id of isp exist in used id of isp, push to new array if not
    foreach($isp as $i){
        if(!in_array($i["id"],$isp_id)){
            array_push($isp_temp,$i);
        }
    }

    $response = [
        "status" => true,
        "isp" => $isp_temp
    ];    
    echo json_encode($response);
?>