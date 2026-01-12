<?php
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data["image"])) {
        $image = $data["image"];
        $image = str_replace("data:image/png;base64,", "", $image);
        $image = str_replace(" ", "+", $image);
        $imageData = base64_decode($image);

        $site = new CCTV_Location;
        $site = DB::find($site,$data["id"])[0]["floorplan"];
        $site = explode("/",$site);
        $filename = end($site);

        // $filename = $data["map_name"].".png";
        $filepath = "../../assets/img/maps_output/".$filename;
        
        file_exists($filepath) ? unlink($filepath) : null;

        if (file_put_contents($filepath, $imageData)) {
            echo json_encode("Saved as ".$filename);
        } else {
            echo json_encode("Failed to save image");
        }
    } else {
        echo json_encode("No image data received");
    }
?>
