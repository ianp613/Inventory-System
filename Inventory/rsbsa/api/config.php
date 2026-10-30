<?php
/**
 * Palo RSBSA - server settings. Edit the values below for your server.
 * This file only returns an array, so opening it in a browser shows nothing.
 */
return [
    'db' => [
        // 'sqlite' (default, one file, nothing to install) or 'mysql'
        'driver'      => 'sqlite',

        // SQLite: the database file. A relative path starts from the project folder.
        // Best kept OUTSIDE the public web folder, for example '/var/data/rsbsa.sqlite'.
        'sqlite_path' => 'database/rsbsa.sqlite',

        // MySQL (only used when driver is 'mysql'; import database/schema.mysql.sql first)
        'host'        => '127.0.0.1',
        'port'        => 3306,
        'name'        => 'rsbsa',
        'user'        => 'root',
        'pass'        => '',
        'charset'     => 'utf8mb4',

        'dsn'         => null,           // optional full PDO DSN; overrides everything above
    ],

    'timezone'             => 'Asia/Manila',

    // Uploaded tax declarations and supporting documents. Keep this folder out of public access
    // (the storage/.htaccess file already blocks it on Apache). Files are served through api/file.php.
    'storage_dir'          => dirname(__DIR__) . '/storage',
    'max_upload_bytes'     => 5 * 1024 * 1024,     // per file; keep in sync with MAX_UPLOAD_MB in script.js

    'login_max_attempts'   => 5,                   // failed logins allowed per IP + username ...
    'login_window_minutes' => 15,                  // ... within this many minutes
    'session_idle_minutes' => 120,                 // auto logout after this much inactivity

    'rsbsa_prefix'         => 'RSBSA',             // auto-generated numbers look like RSBSA-2026-000123

    'debug'                => false,               // true shows error messages in API responses (development only)
];
