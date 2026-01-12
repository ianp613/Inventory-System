<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $setting = new Settings;
    if($data["type"] == "sound"){
        $sound = DB::prepare($setting,$data["id"]);
        $sound->sound = $sound->sound == 1 ? 0 : 1;
        DB::update($sound);
    }

    if($data["type"] == "theme"){
        $theme = DB::prepare($setting,$data["id"]);
        $theme->theme = $theme->theme == 1 ? 0 : 1;
        DB::update($theme);
    }
    

    echo json_encode(DB::where($setting,"uid","=",$_SESSION["userid"])[0]);
?>
