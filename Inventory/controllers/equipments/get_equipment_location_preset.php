<?php
    include("../../includes.php");

    $cache_key = "icore_equipment_location_preset";
    $cache_data = $redis->get($cache_key);

    if ($cache_data !== null) {
        echo $cache_data;
        exit;
    }

    $location = json_decode(file_get_contents("../../assets/files/equipment_location_preset.json"));

    $redis->setex($cache_key, 3600, json_encode($location)); // 1 hour — this is static preset data

    echo json_encode($location);
?>