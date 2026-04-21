<?php
    session_start();
    $auth = isset($_SESSION["ff_auth"]) ? $_SESSION["ff_auth"] == true ? true : false : false;
    echo json_encode($auth);
?>