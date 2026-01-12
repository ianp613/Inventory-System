<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    try {
        $cctv_location = new CCTV_Location;

        $location1 = DB::where($cctv_location,"map_location","=",$_POST["map_location"]);
        $location2 = DB::prepare($cctv_location,$_POST["id"]);

        if(count($location1)){
            if($location1[0]["id"] == $location2->id){
                $file_upload = false;
                $file_uploaded = true;
                if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
                    $file = $_FILES["file"];
                    $fileName = basename($file["name"]);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $filePath = "../../assets/img/maps/" . uniqid() . "." . $extension;
                    $file_upload = true;
                } else {
                    $filePath = $location2->floorplan;
                    $file_upload = false;
                }
                
                if($file_upload){
                    if(move_uploaded_file($file["tmp_name"], $filePath)){
                        if(file_exists($location2->floorplan)){
                            unlink($location2->floorplan);
                        }
                        $file_uploaded = true;
                    }else{
                        $file_uploaded = false;
                    }
                }

                if(file_exists(str_replace("maps","maps_output",$location2->floorplan))){
                    unlink(str_replace("maps","maps_output",$location2->floorplan));
                }

                if($file_uploaded){
                    $location2->map_location = $_POST["map_location"];
                    $location2->floorplan = $filePath;
                    $location2->remarks = $_POST["map_remarks"] ? $_POST["map_remarks"] : "-";
                    DB::update($cctv_location);

                    $response = [
                        "status" => true,
                        "type" => "success",
                        "size" => null,
                        "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
                        "message" => "Map has been updated."
                    ];
                }else{
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
        }else{
            $file_upload = false;
            $file_uploaded = true;
            if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
                $file = $_FILES["file"];
                $fileName = basename($file["name"]);
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $filePath = "../../assets/img/maps/" . uniqid() . "." . $extension;
                $file_upload = true;
            } else {
                $filePath = $location2->floorplan;
                $file_upload = false;
            }
            
            if($file_upload){
                if(move_uploaded_file($file["tmp_name"], $filePath)){
                    if(file_exists($location2->floorplan)){
                        unlink($location2->floorplan);
                    }
                    
                    $file_uploaded = true;
                }else{
                    $file_uploaded = false;
                }
            }

            if(file_exists(str_replace("maps","maps_output",$location2->floorplan))){
                unlink(str_replace("maps","maps_output",$location2->floorplan));
            }
            
            if($file_uploaded){
                $location2->map_location = $_POST["map_location"];
                $location2->floorplan = $filePath;
                $location2->remarks = $_POST["map_remarks"] ? $_POST["map_remarks"] : "-";
                DB::update($cctv_location);

                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
                    "message" => "Map has been updated."
                ];
            }else{
                $response = [
                    "status" => false,
                    "type" => "error",
                    "size" => null,
                    "cctvs" => $_SESSION["g_id"] ? DB::where($cctv_location,"gid","=",$_SESSION["g_id"]) : DB::all($cctv_location),
                    "message" => "Something went wrong, please try again."
                ];
            }
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