<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    !$_SESSION["userid"] ? $_SESSION["userid"] = "login" : null;

    $cache_key = "icore_setting".$_SESSION["userid"];
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $setting = new Settings;
    $temp = DB::where($setting,"uid","=",$_SESSION["userid"]);
    if(!count($temp)){
        $setting->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $setting->uid = $_SESSION["userid"];
        $setting->sound = "1";
        $setting->theme = "Dark";
        $setting->inform = "Yes";
        DB::save($setting);
    }

    $response = DB::where($setting,"uid","=",$_SESSION["userid"])[0];
    $redis->setex($cache_key, 300, json_encode($response));
    echo json_encode($response);
?>
