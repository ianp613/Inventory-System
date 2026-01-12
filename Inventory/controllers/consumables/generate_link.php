<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data["type"] == "regenerate"){
            if(file_exists("links/".$data["link"])){
                unlink("links/".$data["link"]);
            }
        }
        
        #check if link exist
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
        $link = ["g_id" => $_SESSION["g_id"], "g_name" => $_SESSION["g_name"]];
        $filename = Data::generate(50,"alphanumeric").$_SESSION["g_id"].Data::generate(20,"alphanumeric")."====";
        if(!in_array($_SESSION["g_id"],$ids)){
            file_put_contents("links/".$filename,json_encode($link));
            echo json_encode($filename);
        }else{
            $position = array_search($_SESSION["g_id"],$ids);
            $link = explode("/",glob("links/*")[$position]);
            echo json_encode(end($link));
        }
    }
?>