<?php
    $data = json_decode(file_get_contents('php://input'), true);

    $sourceBase = realpath("E:" . $data["source"]);
    $destBase   = realpath("E:" . $data["destination"]);

    $response = [
        "status" => true,
        "message" => "Copy operation completed",
        "results" => []
    ];

    // --- Helper: Rename if exists ---
    function getUniqueName($path) {
        if (!file_exists($path)) return $path;

        $dir = dirname($path);
        $name = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $i = 1;

        do {
            $newName = $name . " - Copy";
            if ($i > 1) $newName .= " ($i)";

            $newPath = $dir . DIRECTORY_SEPARATOR . $newName;
            if ($ext) $newPath .= "." . $ext;

            $i++;
        } while (file_exists($newPath));

        return $newPath;
    }

    // --- Recursive copy ---
    function copyRecursive($src, $dst) {

        if (is_dir($src)) {

            $dst = getUniqueName($dst);

            if (!is_dir($dst)) {
                if (!mkdir($dst, 0777, true)) {
                    return false;
                }
            }

            $files = array_diff(scandir($src), ['.', '..']);

            foreach ($files as $file) {
                if (!copyRecursive(
                    $src . DIRECTORY_SEPARATOR . $file,
                    $dst . DIRECTORY_SEPARATOR . $file
                )) {
                    return false;
                }
            }

            return true;

        } else {

            $dst = getUniqueName($dst);
            return copy($src, $dst);
        }
    }

    // --- Start copying ---
    foreach ($data["targets"] as $item) {

        $src = $sourceBase . "\\" . $item;
        $dst = $destBase . "\\" . $item;

        $result = [
            "name" => $item,
            "status" => true,
            "message" => "Copied successfully"
        ];

        // Validation
        if (!file_exists($src)) {
            $result["status"] = false;
            $result["message"] = "Not found";

        } elseif (!is_readable($src)) {
            $result["status"] = false;
            $result["message"] = "Permission denied";

        } else {
            if (!copyRecursive($src, $dst)) {
                $result["status"] = false;
                $result["message"] = "Copy failed";
            }
        }

        $response["results"][] = $result;
    }

    echo json_encode($response);
?>