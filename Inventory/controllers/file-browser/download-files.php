<?php
    session_start();

    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
    error_reporting(E_ALL);

    while (ob_get_level()) ob_end_clean();

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!$data || !isset($data['folder'], $data['targets']) || empty($data['targets'])) {
        http_response_code(400);
        exit('Invalid input');
    }

    $conf = null;
    if (isset($_SESSION["ff_privileges"]) && $_SESSION["ff_privileges"] != false) {
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

    $folderPath = $conf->location . $data["folder"];
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    include("../../includes.php");

    use ZipStream\ZipStream;

    set_time_limit(0);

    $baseDir = realpath($folderPath);
    if (!$baseDir) {
        http_response_code(400);
        exit('Invalid folder');
    }

    $resolved = [];
    foreach ($data['targets'] as $item) {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $item);
        $target = $baseDir . DIRECTORY_SEPARATOR . $normalized;
        $realTarget = realpath($target);

        if ($realTarget === false) continue;

        $normBase = rtrim($baseDir, DIRECTORY_SEPARATOR);
        $normReal = rtrim($realTarget, DIRECTORY_SEPARATOR);

        if (strpos($normReal, $normBase) === 0) {
            $resolved[] = ['name' => $normalized, 'path' => $realTarget];
        }
    }

    if (empty($resolved)) {
        http_response_code(400);
        exit('No valid targets');
    }

    // Single real file -> stream directly, no zip
    if (count($resolved) === 1 && is_file($resolved[0]['path'])) {
        $file = $resolved[0]['path'];
        $filename = basename($file);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');

        readfile($file);
        exit;
    }

    // --- Multiple items or a folder: build the zip to a temp file first ---
    function addToZip(ZipStream $zip, string $path, string $zipBasePath): void
    {
        if (is_dir($path)) {
            $items = array_diff(scandir($path), ['.', '..']);
            foreach ($items as $item) {
                addToZip($zip, $path . DIRECTORY_SEPARATOR . $item, $zipBasePath . DIRECTORY_SEPARATOR . $item);
            }
        } else {
            $localName = str_replace('\\', '/', $zipBasePath);
            $localName = ltrim($localName, '/');
            $zip->addFileFromPath(
                fileName: $localName,
                path: $path
            );
        }
    }

    $tmpZipPath = tempnam(sys_get_temp_dir(), 'ffzip_');
    $tmpHandle = fopen($tmpZipPath, 'w+b');

    if (!$tmpHandle) {
        http_response_code(500);
        error_log('[download] failed to open temp file for zip: ' . $tmpZipPath);
        exit('Could not create archive');
    }

    // sendHttpHeaders: false — we control headers ourselves once the file is complete
    $zip = new ZipStream(
        outputStream: $tmpHandle,
        sendHttpHeaders: false
    );

    foreach ($resolved as $t) {
        addToZip($zip, $t['path'], $t['name']);
    }

    $zip->finish();
    fclose($tmpHandle);

    $zipSize = filesize($tmpZipPath);
    error_log('[download] temp zip built at ' . $tmpZipPath . ' | size: ' . $zipSize);

    if (!$zipSize) {
        error_log('[download] temp zip is 0 bytes, aborting');
        @unlink($tmpZipPath);
        http_response_code(500);
        exit('Archive build failed');
    }

    // Pick a sensible download name
    $downloadName = ($data['folder'] === '/' || $data['folder'] === '')
        ? preg_replace('/\.zip$/i', '', basename(rtrim($conf->root_name, '/'))) . '.zip'
        : preg_replace('/\.zip$/i', '', basename(rtrim($data['folder'], '/'))) . '.zip';

    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $downloadName . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . $zipSize);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');

    readfile($tmpZipPath);
    @unlink($tmpZipPath);
    exit;