<?php
    session_start();
    unset($_SESSION["ff_auth"]);
    echo json_encode(isset($_SESSION["ff_auth"]));
?>