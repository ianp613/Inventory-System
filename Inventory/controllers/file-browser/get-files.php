<?php
    session_start();
    $data = json_decode(file_get_contents('php://input'), true);
    $conf = null;
    if($_SESSION["ff_privileges"] != false){
        if($_SESSION["ff_privileges"] == "Administrator"){
            $conf = json_decode(file_get_contents("../../file-browser.conf"));
        }else{
            $conf_temp = json_decode(file_get_contents("../../file-browser.conf"));
            if($_SESSION["ff_g_member"]){
                $conf = json_decode('{
                    "browser_name" : "'.$_SESSION["ff_g_name"].'",
                    "root_name" : "'.$_SESSION["ff_g_name"].'",
                    "location" : "'.$conf_temp->location.'/'.$_SESSION["ff_g_name"].'"
                }');
            }else{
                return;
            }
        }
    }else{
        return;
    }

    if(!is_dir($conf->location . $data["folder"])){
        mkdir($conf->location . $data["folder"]);
    }
    
    $baseDir = realpath($conf->location . $data["folder"]);
    $response = [];

    // Check base directory
    if (!$baseDir || !is_dir($baseDir)) {
        // Just return empty array if folder is invalid
        echo json_encode($response);
        return;
    }

    $scanned = @scandir($baseDir);
    if ($scanned === false) $scanned = [];

    // Remove . and ..
    $scanned = array_diff($scanned, ['.', '..']);

    function formatSize($bytes) {
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2) . " MB";
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . " KB";
        } else {
            return number_format($bytes) . " B";
        }
    }

    function getFolderSize($dir) {
        $size = 0;

        $files = @scandir($dir);
        if ($files === false) return 0; // Skip unreadable directories

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            // Skip anything unreadable
            if (!is_readable($path)) continue;

            if (is_dir($path)) {
                $size += getFolderSize($path);
            } elseif (is_file($path)) {
                $size += @filesize($path) ?: 0;
            }
        }

        return $size;
    }

    foreach ($scanned as $scan) {
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . $scan;

        if (!is_readable($fullPath)) continue; // Skip inaccessible items

        if (is_dir($fullPath)) {
            $size = formatSize(getFolderSize($fullPath));
            $response[] = [$scan, "dir", $size];
        } elseif (is_file($fullPath)) {
            $size = formatSize(@filesize($fullPath) ?: 0);
            $response[] = [$scan, "file", $size];
        }
    }

    echo json_encode([$response,$conf]);
?>