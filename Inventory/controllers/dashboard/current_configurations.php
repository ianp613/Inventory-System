<?php
    header('Content-Type: application/json');
    include("../../includes.php");

    $isp = new ISP;
    $router = new Routers;
    $ip_network = new IP_Network;

    $isp = DB::all($isp);
    $router = DB::all($router);
    $ip_network = DB::all($ip_network);

    $config_isp = [];
    $config_router = [];
    
    foreach ($isp as $i) {
        foreach($router as $r){
            if($i["id"] == $r["wan1"]){
                if($i["id"] == $r["active"]){
                    array_push($config_isp,"<b>\"".$i["name"]." - ".$i["isp_name"]."\"</b> with IP <b>\"".$i["wan_ip"]."\"</b>"." is assinged to <b>\"".$r["name"]."\"</b>"." as <b>WAN 1</b> and is <b>Active</b>");
                }else{
                    array_push($config_isp,"<b>\"".$i["name"]." - ".$i["isp_name"]."\"</b> with IP <b>\"".$i["wan_ip"]."\"</b>"." is assinged to <b>\"".$r["name"]."\"</b> as <b>WAN 1</b>");
                }
            }
            if($i["id"] == $r["wan2"]){
                if($i["id"] == $r["active"]){
                    array_push($config_isp,"<b>\"".$i["name"]." - ".$i["isp_name"]."\"</b> with IP <b>\"".$i["wan_ip"]."\"</b>"." is assinged to <b>\"".$r["name"]."\"</b>"." as <b>WAN 2</b> and is <b>Active</b>");
                }else{
                    array_push($config_isp,"<b>\"".$i["name"]." - ".$i["isp_name"]."\"</b> with IP <b>\"".$i["wan_ip"]."\"</b>"." is assinged to <b>\"".$r["name"]."\"</b> as <b>WAN 2</b>");
                }
            }
        }
    }
    
    foreach ($router as $r) {
        foreach ($ip_network as $i) {
            if($r["id"] == $i["rid"]){
                array_push($config_router,"<b>\"".$r["name"]."\"</b> with IP <b>\"".$r["ip"]."\"</b> is connected to network <b>\"".$i["name"]."\"</b>");
            }
        }
    }
    $response = [
        "active_isp" => $config_isp,
        "routers" => $config_router
    ];

    echo json_encode($response);
?>