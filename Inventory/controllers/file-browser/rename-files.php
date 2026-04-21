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

    // sanitize new name (VERY important for Windows)
    $data["new"] = rtrim($data["new"], ". ");

    // resolve base directory safely
    $baseDir = realpath($conf->location . $data["folder"]);

    $type = $data["type"] == "dir" ? "Folder" : "File";

    $response = [
        "status" => true,
        "type" => "success",
        "message" => $type." renamed successfully!",
    ];

    // check if path is valid
    if ($baseDir === false) {
        echo json_encode([
            "status" => false,
            "type" => "error",
            "message" => "Invalid base directory."
        ]);
        exit;
    }

    $old = $baseDir . "\\" . $data["old"];
    $new = $baseDir . "\\" . $data["new"];



    if (!file_exists($old)) {
        $response["status"] = false;
        $response["type"] = "warning";
        $response["message"] = $type." does not exist.";
    } elseif (!is_readable($old)) {
        $response["status"] = false;
        $response["type"] = "warning";
        $response["message"] = $type." is not readable.";
    } elseif (!is_writable(dirname($old))) {
        $response["status"] = false;
        $response["type"] = "warning";
        $response["message"] = $type." is not writable.";
    } elseif (file_exists($new)) {
        $response["status"] = false;
        $response["type"] = "info";
        $response["message"] = $type." name already exists.";
    } else {
        // suppress warning and capture real error
        if (!@rename($old, $new)) {
            $error = error_get_last();

            $response["status"] = false;
            $response["type"] = "error";
            $response["message"] = $error["message"] ?? "Rename failed.";
        }
    }

    echo json_encode($response);
?>