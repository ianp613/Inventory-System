<?php
    session_start();
    echo json_encode(isset($_SESSION["ff_auth"]));
?>