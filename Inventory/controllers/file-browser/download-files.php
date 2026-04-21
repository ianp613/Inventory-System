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

    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
    error_reporting(E_ALL);

    // Clean any output buffer
    while (ob_get_level()) ob_end_clean();

    include("../../includes.php");

    use ZipStream\ZipStream;

    set_time_limit(0);

    if (!$data || !isset($data['folder'], $data['targets'])) {
        http_response_code(400);
        exit('Invalid input');
    }

    // Resolve base folder
    $baseDir = realpath($conf->location . $data['folder']);
    if (!$baseDir) {
        http_response_code(400);
        exit('Invalid folder');
    }

    // Create ZIP stream (v3)
    // use named args for outputName and sendHttpHeaders
    $zip = new ZipStream(
        outputName: 'files.zip',
        sendHttpHeaders: true
    );

    // Recursive function to add files
    function addToZip($zip, $path, $baseDir) {
        if (is_dir($path)) {
            foreach (array_diff(scandir($path), ['.', '..']) as $file) {
                addToZip($zip, $path . DIRECTORY_SEPARATOR . $file, $baseDir);
            }
        } else {
            $localName = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $path);
            $zip->addFileFromPath(
                fileName: $localName,
                path: $path
            );
        }
    }

    // Add selected files/folders
    foreach ($data['targets'] as $item) {
        $target = $baseDir . "\\" . $item;
        $realTarget = realpath($target);
        if ($realTarget && strpos($realTarget, $baseDir) === 0) {
            addToZip($zip, $realTarget, $baseDir);
        }
    }

    // Finish ZIP stream
    $zip->finish();