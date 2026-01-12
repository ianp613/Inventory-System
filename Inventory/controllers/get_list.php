<?php
    $list = json_decode(file_get_contents("../assets/json/list.json"));
    echo json_encode($list);
?>