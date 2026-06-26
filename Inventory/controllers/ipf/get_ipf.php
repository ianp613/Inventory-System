<?php
    $ipf = json_decode(file_get_contents("../../ip-filter.conf"));
    echo json_encode([$ipf,"<h1>ERROR 403 - Forbidden</h1><br><hr style=\"padding:0px; margin: 0px;\"><br><h2>You don't have permission to access this website.</h2>"]);