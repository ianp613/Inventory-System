<?php
    include("../../includes.php");
    $cache_key = "icore_ipf";
    $cache_data = $redis->get($cache_key);

    if($cache_data !== null){
        echo $cache_data;
        exit;
    }

    $ipf = json_decode(file_get_contents("../../ip-filter.conf"));

    $redis->setex($cache_key,300,json_encode($ipf));
    echo json_encode($ipf);