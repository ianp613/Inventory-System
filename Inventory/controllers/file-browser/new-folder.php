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
    
    // sanitize folder name (Windows-safe)
    $data["name"] = rtrim($data["name"], ". ");

    // resolve base directory
    $baseDir = realpath($conf->location . $data["folder"]);

    $response = [
        "status" => true,
        "type" => "success",
        "message" => "Folder created successfully!",
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

    $newFolder = $baseDir . "\\" . $data["name"];

    if (empty($data["name"])) {
        $response["status"] = false;
        $response["type"] = "warning";
        $response["message"] = "Folder name cannot be empty.";
    } elseif (file_exists($newFolder)) {
        $response["status"] = false;
        $response["type"] = "info";
        $response["message"] = "Folder already exists.";
    } elseif (!is_writable($baseDir)) {
        $response["status"] = false;
        $response["type"] = "warning";
        $response["message"] = "Directory is not writable.";
    } else {
        // try creating folder
        if (!@mkdir($newFolder, 0777, true)) {
            $error = error_get_last();

            $response["status"] = false;
            $response["type"] = "error";
            $response["message"] = $error["message"] ?? "Failed to create folder.";
        }
    }

    echo json_encode($response);
?>