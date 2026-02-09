<?php
    $location = json_decode(file_get_contents("../../assets/files/equipment_location_preset.json"));
    echo json_encode($location);
?>