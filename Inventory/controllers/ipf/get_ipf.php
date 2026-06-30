<?php
    $ipf = json_decode(file_get_contents("../../ip-filter.conf"));
    echo json_encode($ipf);