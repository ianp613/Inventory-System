<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $t = new Terminals;
    echo json_encode(DB::all($t,"project","asc"));
?>