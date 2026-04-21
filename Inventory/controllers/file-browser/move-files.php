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

    $sourceBase = realpath($conf->location . $data["source"]);
    $destBase   = realpath($conf->location . $data["destination"]);

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

    // --- Recursive delete (for fallback) ---
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

    // --- Recursive move ---
    function moveRecursive($src, $dst) {

        $dst = getUniqueName($dst);

        // Try fast move first (same drive)
        if (@rename($src, $dst)) {
            return true;
        }

        // Fallback: copy + delete
        if (is_dir($src)) {

            if (!is_dir($dst)) {
                mkdir($dst, 0777, true);
            }

            $files = array_diff(scandir($src), ['.', '..']);

            foreach ($files as $file) {
                moveRecursive(
                    $src . DIRECTORY_SEPARATOR . $file,
                    $dst . DIRECTORY_SEPARATOR . $file
                );
            }

            return rmdir($src);

        } else {

            if (copy($src, $dst)) {
                return unlink($src);
            }
        }

        return false;
    }

    // --- Start moving ---
    $results = [];

    foreach ($data["targets"] as $item) {

        $src = $sourceBase . "\\" . $item;
        $dst = $destBase . "\\" . $item;

        $result = [
            "name" => $item,
            "status" => true,
            "message" => "Moved successfully"
        ];

        if (!file_exists($src)) {
            $result["status"] = false;
            $result["message"] = "Not found";
        } else {
            if (!moveRecursive($src, $dst)) {
                $result["status"] = false;
                $result["message"] = "Move failed";
            }
        }

        $results[] = $result;
    }

    echo json_encode([
        "status" => true,
        "message" => "Move operation completed",
        "results" => $results
    ]);
?>