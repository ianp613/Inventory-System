<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $response = [
        "status" => false,
        "link" => ""
    ];

    if($_SESSION["g_member"]){
        $links = glob("links/*");
        $glog = [];
        foreach($links as $link){
            $temp = explode("/",$link);
            array_push($glog,end($temp));
        }
        $ids = [];
        foreach($glog as $g){
            if(strlen($g) == 75){
                $temp = str_split($g,1);
                array_push($ids,$temp[50]);
            }
        }
        if(in_array($_SESSION["g_id"],$ids)){
            $position = array_search($_SESSION["g_id"],$ids);
            $link = explode("/",glob("links/*")[$position]);
            $response = [
                "status" => true,
                "link" => end($link)
            ];
        }
    }

    

    echo json_encode($response);
?>