<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    try {
        $cctv_location = new CCTV_Location;
        $bol = DB::validate($cctv_location,"map_location",$_POST["map_location"]);
        if($bol){
            $file = $_FILES["file"];
            $fileName = basename($file["name"]);
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            if(!is_dir("../../assets/img/maps/")){
                mkdir("../../assets/img/maps/");
            }

            $filePath = "../../assets/img/maps/" . uniqid() . "." . $extension;
            
            if(move_uploaded_file($file["tmp_name"], $filePath)) {
                $cctv_location->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $cctv_location->uid = $_POST["uid"];
                $cctv_location->map_location = $_POST["map_location"];
                $cctv_location->floorplan = $filePath;
                $cctv_location->remarks = $_POST["map_remarks"] ? $_POST["map_remarks"] : "-";
                $cctv_location->camera_size = "25";
                DB::save($cctv_location);

                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has added a map \"".$_POST["map_location"]."\".";
                if($_SESSION["log"] != $log->log){
                    $_SESSION["log"] = $log->log;
                    DB::save($log);
                }

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
                    "message" => "Map has been added."
                ];

            } else {
                $response = [
                    "status" => false,
                    "type" => "error",
                    "size" => null,
                    "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
                    "message" => "Something went wrong, please try again."
                ];
            }
        }else{
            $response = [
                "status" => false,
                "type" => "warning",
                "size" => null,
                "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
                "message" => "Site location already exist."
            ];
        }
        
        echo json_encode($response);
    } catch (\Throwable $th) {
        $response = [
            "status" => false,
            "type" => "error",
            "size" => null,
            "message" => "Add map error."
        ];     
    }