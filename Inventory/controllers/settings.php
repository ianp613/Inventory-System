<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    !$_SESSION["userid"] ? $_SESSION["userid"] = "login" : null;
    $setting = new Settings;
    $temp = DB::where($setting,"uid","=",$_SESSION["userid"]);
    if(!count($temp)){
        $setting->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $setting->uid = $_SESSION["userid"];
        $setting->sound = "1";
        $setting->theme = "Legacy";
        DB::save($setting);
    }
    echo json_encode(DB::where($setting,"uid","=",$_SESSION["userid"])[0]);
?>
