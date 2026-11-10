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
<link rel="stylesheet" href="<?= h($base) ?>assets/style.css">
</head>
<?php if (!$user): ?>
<body class="login-page">
<form class="login-card" method="post" autocomplete="off">
    <h1>File Manager</h1>
    <p class="muted">Sign in to continue</p>
    <?php if ($error !== ''): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label>Username<input type="text" name="username" required autofocus></label>
    <label>Password<input type="password" name="password" required></label>
    <button type="submit" class="primary">Sign in</button>
</form>
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

<script src="<?= h($base) ?>assets/app.js"></script>
</body>
</html>
