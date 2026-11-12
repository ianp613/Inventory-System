<?php
declare(strict_types=1);
require __DIR__ . '/src/bootstrap.php';

if (!Auth::user()) {
    json_out(['error' => 'Not signed in.'], 401);
}

$action = (string)($_GET['action'] ?? '');
$readActions = ['list', 'foldersize', 'search', 'download', 'preview'];
$writeActions = ['mkdir', 'rename', 'delete', 'copy', 'move', 'upload', 'uploadchunk', 'uploadabort', 'mkpath', 'zip'];

$in = [];
if (in_array($action, $writeActions, true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_out(['error' => 'POST required.'], 405);
    }
    if (!csrf_valid($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? null))) {
        json_out(['error' => 'Session expired. Please reload the page.'], 403);
    }
    $in = $_POST;
    if ($action === 'uploadchunk' || $action === 'uploadabort') {
        $in = $_GET; // the request body is the raw chunk
    }
    if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === 0) {
        $in = json_decode(file_get_contents('php://input'), true) ?: [];
    }
} elseif (in_array($action, $readActions, true)) {
    $in = $_GET;
} else {
    json_out(['error' => 'Unknown action.'], 400);
}

$path = (string)($in['path'] ?? '');

try {
    $fs = Auth::storage();

    switch ($action) {
        case 'list':
            json_out(['items' => $fs->list($path)]);

        case 'foldersize':
            json_out($fs->folderSize($path));

        case 'search':
            json_out(['items' => $fs->search($path, (string)($in['q'] ?? ''))]);

        case 'mkdir':
            $fs->mkdir($path, (string)($in['name'] ?? ''));
            json_out(['ok' => true]);

        case 'rename':
            $fs->rename($path, (string)($in['name'] ?? ''));
            json_out(['ok' => true]);

        case 'delete':
            foreach ((array)($in['paths'] ?? []) as $p) {
                $fs->delete((string)$p);
            }
            json_out(['ok' => true]);

        case 'copy':
            foreach ((array)($in['paths'] ?? []) as $p) {
                $fs->copy((string)$p, (string)($in['dest'] ?? ''));
            }
            json_out(['ok' => true]);

        case 'move':
            foreach ((array)($in['paths'] ?? []) as $p) {
                $fs->move((string)$p, (string)($in['dest'] ?? ''));
            }
            json_out(['ok' => true]);

        case 'upload':
            if (empty($_FILES['files'])) {
                throw new RuntimeException('No files received (check upload_max_filesize / post_max_size in php.ini).');
            }
            json_out(['saved' => $fs->upload($path, $_FILES['files'])]);

        case 'uploadchunk':
            session_write_close(); // do not lock the session while the chunk arrives
            $body = fopen('php://input', 'rb');
            json_out($fs->uploadChunk(
                $path,
                (string)($in['sub'] ?? ''),
                (string)($in['id'] ?? ''),
                (string)($in['name'] ?? ''),
                (int)($in['offset'] ?? 0),
                (int)($in['total'] ?? 0),
                ($in['last'] ?? '0') === '1',
                $body
            ));

        case 'uploadabort':
            $fs->uploadAbort($path, (string)($in['sub'] ?? ''), (string)($in['id'] ?? ''));
            json_out(['ok' => true]);

        case 'mkpath':
            $fs->mkpath($path, (string)($in['sub'] ?? ''));
            json_out(['ok' => true]);

        case 'zip':
            $sel = $in['paths'] ?? [];
            if (is_string($sel)) {
                $sel = json_decode($sel, true) ?: [];
            }
            $plan = $fs->zipPlan((array)$sel); // validates everything before the first byte is sent
            session_write_close();
            @set_time_limit(0);
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            $zname = $plan['name'];
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . rawurlencode($zname) . '"; filename*=UTF-8\'\'' . rawurlencode($zname));
            header('X-Content-Type-Options: nosniff');
            header('X-Accel-Buffering: no'); // nginx: do not buffer the stream
            header('Cache-Control: no-store');
            $zip = new ZipStream();
            foreach ($fs->zipEntries($plan['items']) as $e) {
                if ($e['dir']) {
                    $zip->addDir($e['name'], $e['mtime']);
                } else {
                    $zip->addFile($e['abs'], $e['name'], $e['size'], $e['mtime']);
                }
            }
            $zip->finish();
            exit;

        case 'download':
        case 'preview':
            $file = $fs->absFile($path);
            $name = basename($file);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $previewTypes = [
                'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'gif' => 'image/gif', 'webp' => 'image/webp', 'pdf' => 'application/pdf',
                'txt' => 'text/plain; charset=utf-8', 'log' => 'text/plain; charset=utf-8',
                'csv' => 'text/plain; charset=utf-8', 'json' => 'text/plain; charset=utf-8',
                'mp4' => 'video/mp4', 'm4v' => 'video/mp4', 'webm' => 'video/webm',
                'ogv' => 'video/ogg', 'mov' => 'video/quicktime', 'mkv' => 'video/x-matroska',
            ];
            if ($action === 'preview' && isset($previewTypes[$ext])) {
                send_file($file, $previewTypes[$ext], 'inline; filename="' . rawurlencode($name) . '"');
            }
            send_file(
                $file,
                'application/octet-stream',
                'attachment; filename="' . rawurlencode($name) . "\"; filename*=UTF-8''" . rawurlencode($name)
            );
    }
} catch (RuntimeException $e) {
    if (headers_sent()) {
        exit; // a zip was already streaming; nothing sensible can be added
    }
    json_out(['error' => $e->getMessage()], 400);
} catch (Throwable $e) {
    error_log($e->getMessage());
    if (headers_sent()) {
        exit;
    }
    json_out(['error' => 'Server error.'], 500);
}
