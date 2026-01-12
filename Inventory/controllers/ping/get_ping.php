<?php
    header('Content-Type: application/json');
    include("../../includes.php");

    $data = json_decode(file_get_contents('php://input'), true);
    $group = $data["group"];

    $ping = file_get_contents("../../assets/files/ping.json");
    $ping_temp = json_decode($ping, true);
    $ping = json_decode($ping);

    $reply = [];

    foreach ($ping->$group as $target) {
        $res = shell_exec('ping '.$target.' -n 1 -w 1000');
        // $response = shell_exec('ping '.$target.' -n 1 -w 1000 | find "Reply"');
        if(strpos($res,"TTL")){

            preg_match('/bytes\s*([=<>])\s*(\d+)/', $res, $bytes);
            preg_match('/time\s*([=<>])\s*(\d+)/', $res, $time);
            preg_match('/TTL\s*([=<>])\s*(\d+)/', $res, $ttl);

            $bytes_op = $bytes[1];
            $bytes = $bytes[2] ?? null;

            $time_op = $time[1];
            $time = $time[2] ?? null;

            $ttl_op = $ttl[1];
            $ttl = $ttl[2] ?? null;

            $temp = ["bytes"=>$bytes_op."|".$bytes,"time"=>$time_op."|".$time,"ttl"=>$ttl_op."|".$ttl];
        }else if(strpos($res,"Request timed out")){
            $temp = "Request time out.";
        }else if(strpos($res,"unreachable")){
            $temp = "Destination host unreachable.";
        }else if(strpos($res,"General")){
            $temp = "General Error.";
        }

        array_push($reply, $res ? [$target => $temp] : false);
    }

    $response = [
        "target" => $ping->$group,
        "reply" => $reply
    ];
        

        
    echo json_encode($response);
?>