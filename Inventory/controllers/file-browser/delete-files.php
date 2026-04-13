<?php
    $conf = json_decode(file_get_contents("../../file-browser.conf"));
    $data = json_decode(file_get_contents('php://input'), true);
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