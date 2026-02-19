<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // hide raw errors

    set_error_handler(function ($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error !== null) {
            $response = [
                "site" => ["System Error"],
                "status" => ["danger"],
                "message" => [$error['message']]
            ];
            echo json_encode($response);
        }
    });
?>