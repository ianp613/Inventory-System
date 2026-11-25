<?php
declare(strict_types=1);
require __DIR__ . '/../../app/lib.php';
header('Cache-Control: no-store');
header('X-Frame-Options: SAMEORIGIN');
$logged = admin_logged_in();
$v = max(filemtime(__DIR__ . '/assets/app.js'), filemtime(__DIR__ . '/assets/app.css'));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>WiFi Portal Manager</title>
<?php if ($logged): ?><meta name="csrf" content="<?= h($_SESSION['csrf']) ?>"><meta name="admin-user" content="<?= h($_SESSION['admin_user']) ?>"><?php endif; ?>
<link rel="stylesheet" href="assets/app.css?v=<?= $v ?>">
</head>
<body class="<?= $logged ? '' : 'login-page' ?>">
<?php if (!$logged): ?>
  <form id="login" class="login-card">
    <h1>WiFi Portal Manager</h1>
    <p class="muted">Sign in to manage vouchers, users and the captive portal.</p>
    <label>Username</label><input name="username" autocomplete="username" required autofocus>
    <label>Password</label><input name="password" type="password" autocomplete="current-password" required>
    <div class="alert error hidden" id="lerr"></div>
    <button class="btn primary block" type="submit">Sign in</button>
  </form>
  <script>
  document.getElementById('login').addEventListener('submit', async function (e) {
    e.preventDefault();
    var f = e.target, err = document.getElementById('lerr');
    err.classList.add('hidden');
    var r = await fetch('api.php?action=login', {method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({username: f.username.value, password: f.password.value})});
    var j = await r.json();
    if (j.ok) location.reload(); else { err.textContent = j.error || 'Login failed'; err.classList.remove('hidden'); }
  });
  </script>
<?php else: ?>
  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <div class="brand">&#x1F4F6; WiFi Portal</div>
      <nav>
        <a href="#dashboard" data-nav="dashboard">Dashboard</a>
        <a href="#vouchers" data-nav="vouchers">Vouchers</a>
        <a href="#users" data-nav="users">User List</a>
        <div class="nav-sep">Settings</div>
        <a href="#captive" data-nav="captive">Captive Settings</a>
        <a href="#usersettings" data-nav="usersettings">User Settings</a>
        <a href="#networks" data-nav="networks">Networks</a>
        <a href="#account" data-nav="account">Account</a>
      </nav>
      <div class="side-foot"><?= h($_SESSION['admin_user']) ?> &middot; <a href="#" id="logout">Sign out</a></div>
    </aside>
    <main class="content">
      <button class="menu-btn" id="menu" aria-label="Menu">&#9776;</button>
      <div id="banner"></div>
      <div id="view"><p class="muted">Loading&hellip;</p></div>
    </main>
  </div>
  <div id="toasts"></div>
  <div id="modal-root"></div>
  <script src="assets/app.js?v=<?= $v ?>"></script>
<?php endif; ?>
</body>
</html>
