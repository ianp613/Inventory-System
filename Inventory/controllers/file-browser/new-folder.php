<?php
    $conf = json_decode(file_get_contents("../../file-browser.conf"));
    $data = json_decode(file_get_contents('php://input'), true);

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