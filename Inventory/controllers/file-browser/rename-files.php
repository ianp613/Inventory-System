<?php
    $conf = json_decode(file_get_contents("../../file-browser.conf"));
    $data = json_decode(file_get_contents('php://input'), true);

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