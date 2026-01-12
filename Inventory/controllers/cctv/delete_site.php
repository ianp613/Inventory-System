<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $cctv_location = new CCTV_Location;
    $cctv_camera = new CCTV_Camera;

    $camera = DB::where($cctv_camera,"id","=",$data["id"]);
    foreach ($camera as $cam) {
        DB::delete($cctv_camera,$cam["id"]);
    }

    $location = DB::find($cctv_location,$data["id"]);
    if(file_exists($location[0]["floorplan"])){
        unlink($location[0]["floorplan"]);
    }
    if(file_exists(str_replace("maps","maps_output",$location[0]["floorplan"]))){
        unlink(str_replace("maps","maps_output",$location[0]["floorplan"]));
    }

    DB::delete($cctv_location,$data["id"]);

    $response = [
        "status" => true,
        "type" => "info",
        "size" => null,
        "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
        "message" => "Map has been deleted."
    ];

    echo json_encode($response);