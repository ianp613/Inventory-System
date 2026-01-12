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
        $setting->sound = $_SESSION["userid"] == "login" ? "1" : "1";
        $setting->theme = $_SESSION["userid"] == "login" ? "1" : "0";
        DB::save($setting);
    }
    echo json_encode(DB::where($setting,"uid","=",$_SESSION["userid"])[0]);
?>
