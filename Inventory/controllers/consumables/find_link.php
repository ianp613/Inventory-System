<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $response = [
        "status" => false,
        "link" => "",
        "groups" => [],
        "links" => []
    ];

    $links = glob("links/*");


    if($_SESSION["g_member"]){
        $group = new User_Group;
        $groups = DB::all($group);

        $response["groups"] = $groups;

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
        foreach ($groups as $gr) {
            if(in_array($gr["id"],$ids)){
                $position = array_search($gr["id"],$ids);
                $link = explode("/",glob("links/*")[$position]);
                array_push($response["links"],[$gr["id"],end($link)]);
            }
        }
    }

    

    echo json_encode($response);
?>