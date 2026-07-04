<?php
    // Allow requests from your local development server
    header("Access-Control-Allow-Origin: http://localhost:8889");

    // Specify which HTTP methods are allowed
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    // Allow standard request headers (like Content-Type, Authorization)
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Handle preflight OPTIONS requests immediately
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }
    echo json_encode("Hello World");