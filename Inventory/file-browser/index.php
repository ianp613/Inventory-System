<?php
declare(strict_types=1);
require __DIR__ . '/src/bootstrap.php';

// Folder URL of this app, e.g. "/file-browser/" (works for any sub-folder or the site root)
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';

$user = Auth::user();
$error = '';

if (!$user && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $error = 'Session expired. Please try again.';
    } else {
        try {
            $row = Auth::attempt(trim((string)($_POST['username'] ?? '')), (string)($_POST['password'] ?? ''));
            if ($row === null) {
                usleep(600000); // slow down password guessing
                $error = 'Invalid username or password.';
            } else {
                [$root, $label] = Auth::resolveRoot($row);
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'privileges' => $row['privileges'],
                ];
                $_SESSION['root'] = $root;
                $_SESSION['root_label'] = $label;
                csrf_token();
                header('Location: ' . $base . 'index.php');
                exit;
            }
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = 'Could not sign in right now. Please try again later.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $user ? 'File Manager' : 'Sign in - File Manager' ?></title>
<link rel="stylesheet" href="<?= h($base) ?>assets/style.css?v=<?= (int)@filemtime(__DIR__ . '/assets/style.css') ?>">
</head>
<?php if (!$user): ?>
<body class="login-page">
<form class="login-card" method="post" autocomplete="off">
    <h1>LOGIN TO FILE BROWSER</h1>
    <?php if ($error !== ''): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="text" name="username" placeholder="User ID" required autofocus autocomplete="username">
    <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
    <button type="submit" class="login-btn">
        <svg viewBox="0 0 512 512" width="13" height="13" fill="currentColor" aria-hidden="true"><path d="M217.9 105.9 340.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L217.9 406.1c-6.4 6.4-15 9.9-24 9.9-18.7 0-33.9-15.2-33.9-33.9V320H32c-17.7 0-32-14.3-32-32V224c0-17.7 14.3-32 32-32h128V129.9C160 111.2 175.2 96 193.9 96c9 0 17.6 3.6 24 9.9zM352 416h64c17.7 0 32-14.3 32-32V128c0-17.7-14.3-32-32-32H352c-17.7 0-32-14.3-32-32s14.3-32 32-32h64c53 0 96 43 96 96V384c0 53-43 96-96 96H352c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/></svg>LOGIN
    </button>
    <p class="login-note">Integrated with DDC LEYTE ICORE Credentials<br><span>File Browser v1.2</span></p>
</form>
<div class="login-foot">Copyright 2025 @ Wifi Team | Ian Paul</div>
</body>
</html>
<?php return; endif; ?>
<body data-csrf="<?= h(csrf_token()) ?>" data-base="<?= h($base) ?>">
<header class="topbar">
    <div class="brand">File Manager</div>
    <div class="who">
        <span><?= h($user['name']) ?> &middot; <span class="muted"><?= h($_SESSION['root_label'] ?? '') ?></span></span>
        <a href="<?= h($base) ?>logout.php" class="btn">Sign out</a>
    </div>
</header>

<main>
    <nav id="crumbs" class="crumbs"></nav>

    <div class="toolbar">
        <button id="btnUpload" class="primary">Upload</button>
        <input type="file" id="fileInput" multiple hidden>
        <button id="btnMkdir">New folder</button>
        <span class="sep"></span>
        <button id="btnCopy" disabled>Copy</button>
        <button id="btnCut" disabled>Move</button>
        <button id="btnPaste" disabled>Paste here</button>
        <button id="btnDelete" class="danger" disabled>Delete</button>
        <span class="grow"></span>
        <input type="search" id="search" placeholder="Search in this folder…">
    </div>

    <div id="notice" class="notice" hidden></div>

    <div id="uploads" class="uploads" hidden>
        <div class="uploads-head">
            <strong>Uploads</strong>
            <span id="uploadSummary" class="muted"></span>
            <span class="grow"></span>
            <button id="uploadClear" class="small">Clear finished</button>
            <button id="uploadHide" class="small">Hide</button>
        </div>
        <ul id="uploadList"></ul>
    </div>

    <div id="dropzone" class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="c-check"><input type="checkbox" id="checkAll" title="Select all"></th>
                    <th data-sort="name">Name</th>
                    <th data-sort="size" class="c-size">Size</th>
                    <th data-sort="modified" class="c-mod">Modified</th>
                    <th class="c-act"></th>
                </tr>
            </thead>
            <tbody id="rows"></tbody>
        </table>
        <div id="empty" class="empty" hidden>This folder is empty.</div>
    </div>
    <div id="status" class="status"></div>
</main>

<div id="modal" class="modal" hidden>
    <div class="modal-box">
        <div class="modal-head"><strong id="modalTitle"></strong><button id="modalClose">Close</button></div>
        <div id="modalBody" class="modal-body"></div>
    </div>
</div>

<script src="<?= h($base) ?>assets/app.js?v=<?= (int)@filemtime(__DIR__ . '/assets/app.js') ?>"></script>
</body>
</html>
