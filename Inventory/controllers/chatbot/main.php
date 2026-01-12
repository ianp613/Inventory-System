<?php
    include("../../includes.php");
    include("Identifier.php");
    session_start();

    $data = json_decode(file_get_contents('php://input'), true);

    $message = $data['message'];
    echo json_encode([Identifier::main($message),uniqid()], JSON_UNESCAPED_UNICODE);
?>

