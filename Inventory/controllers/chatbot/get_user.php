<?php
    include("../../includes.php");
    include("Identifier.php");
    session_start();

    $data = json_decode(file_get_contents('php://input'), true);

    $user = new User;
    $user = DB::where($user,"username","=",$data["userid"]);
    echo json_encode($user);
?>

