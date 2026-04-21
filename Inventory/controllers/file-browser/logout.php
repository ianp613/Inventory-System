<?php
    session_start();
    unset($_SESSION["ff_auth"]);
    unset($_SESSION["ff_g_member"]);
    unset($_SESSION["ff_g_name"]);
    unset($_SESSION["ff_g_id"]);
    unset($_SESSION["ff_privileges"]);
    echo json_encode(isset($_SESSION["ff_auth"]));
?>