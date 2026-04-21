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
                    "location" : "'.$conf_temp->location.'/InvSys_'.$_SESSION["ff_g_name"].'"
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

    $response = [
        "status" => true,
        "type" => "success",
        "message" => "Delete operation completed.",
        "results" => []
    ];

    // validate base path
    if ($baseDir === false) {
        echo json_encode([
            "status" => false,
            "type" => "error",
            "message" => "Invalid base directory."
        ]);
        exit;
    }

    // recursive delete
    function deleteRecursive($path) {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);

            foreach ($files as $file) {
                deleteRecursive($path . DIRECTORY_SEPARATOR . $file);
            }

            return rmdir($path);
        } else {
            return unlink($path);
        }
    }

    foreach ($data["targets"] as $item) {

        $target = $baseDir . "\\" . $item;

        $result = [
            "name" => $item,
            "status" => true,
            "message" => "Deleted successfully"
        ];

        // امنیت check (VERY IMPORTANT)
        $realTarget = realpath($target);
        if ($realTarget === false || strpos($realTarget, $baseDir) !== 0) {
            $result["status"] = false;
            $result["message"] = "Invalid path";
        } elseif (!file_exists($target)) {
            $result["status"] = false;
            $result["message"] = "Not found";
        } elseif (!is_writable(dirname($target))) {
            $result["status"] = false;
            $result["message"] = "Permission denied";
        } else {
            if (!@deleteRecursive($target)) {
                $error = error_get_last();
                $result["status"] = false;
                $result["message"] = $error["message"] ?? "Delete failed";
            }
        }

        $response["results"][] = $result;
    }

    echo json_encode($response);
?>