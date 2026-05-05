<?php
    session_start();

    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
    error_reporting(E_ALL);

    // Clean any output buffer
    while (ob_get_level()) ob_end_clean();

    // Parse input first
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['folder'], $data['targets']) || empty($data['targets'])) {
        http_response_code(400);
        exit('Invalid input');
    }

    // Auth and config
    $conf = null;
    if ($_SESSION["ff_privileges"] != false) {
        if ($_SESSION["ff_privileges"] == "Administrator") {
            $conf = json_decode(file_get_contents("../../file-browser.conf"));
        } else {
            $conf_temp = json_decode(file_get_contents("../../file-browser.conf"));
            if ($_SESSION["ff_g_member"]) {
                $conf = (object)[
                    'browser_name' => $_SESSION["ff_g_name"],
                    'root_name' => $_SESSION["ff_g_name"],
                    'location' => $conf_temp->location . '/InvSys_' . $_SESSION["ff_g_name"]
                ];
            } else {
                http_response_code(403);
                exit('Access denied');
            }
        }
    } else {
        http_response_code(403);
        exit('Access denied');
    }

    // Create folder if needed (with recursive flag)
    $folderPath = $conf->location . $data["folder"];
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    include("../../includes.php");

    use ZipStream\ZipStream;

    set_time_limit(0);

    // Resolve base folder
    $baseDir = realpath($folderPath);
    if (!$baseDir) {
        http_response_code(400);
        exit('Invalid folder');
    }

    $zip = new ZipStream(
        outputName: 'files.zip',
        sendHttpHeaders: true
    );

    /**
     * Add files/folders to ZIP while preserving structure relative to a reference point
     * 
     * @param ZipStream $zip
     * @param string $path - Full absolute path to the file/folder
     * @param string $zipBasePath - The path prefix to use inside the ZIP
     */
    function addToZip(ZipStream $zip, string $path, string $zipBasePath): void
    {
        if (is_dir($path)) {
            $items = array_diff(scandir($path), ['.', '..']);
            foreach ($items as $item) {
                addToZip($zip, $path . DIRECTORY_SEPARATOR . $item, $zipBasePath . DIRECTORY_SEPARATOR . $item);
            }
        } else {
            // Normalize path separators for ZIP (always use forward slashes)
            $localName = str_replace('\\', '/', $zipBasePath);
            // Remove leading slash if present
            $localName = ltrim($localName, '/');
            
            $zip->addFileFromPath(
                fileName: $localName,
                path: $path
            );
        }
    }

    // Add selected files/folders
    foreach ($data['targets'] as $item) {
        // Normalize separators
        $item = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $item);
        $target = $baseDir . DIRECTORY_SEPARATOR . $item;
        $realTarget = realpath($target);
        
        // Security check: ensure target is within base directory
        if ($realTarget && strpos($realTarget, $baseDir) === 0) {
            // Use the item name as the ZIP base path to preserve the folder name
            addToZip($zip, $realTarget, $item);
        }
    }

    $zip->finish();
