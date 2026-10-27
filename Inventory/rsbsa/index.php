<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator Login | Palo RSBSA</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page" data-page="login">

  <main class="login-card" aria-labelledby="login-title">
    <header class="login-header">
      <div class="logo-mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22V10"/>
          <path d="M12 10C12 6 9 4 5 4c0 4 2 6 7 6z"/>
          <path d="M12 14c0-3.5 2.5-5.5 7-5.5 0 3.5-2 5.5-7 5.5z"/>
        </svg>
      </div>
      <h1 id="login-title">Palo Registry System for Basic Sectors in Agriculture</h1>
      <p class="subtitle">(RSBSA) &middot; Administrator Login</p>
    </header>

    <div id="alert" class="alert" role="alert" hidden></div>

    <form id="login-form" novalidate>
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username"
               autocapitalize="none" spellcheck="false" required autofocus>
        <small class="field-error" id="username-error"></small>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="password-wrap">
          <input type="password" id="password" name="password" autocomplete="current-password" required>
          <button type="button" id="toggle-password" class="toggle-btn" aria-label="Show password">Show</button>
        </div>
        <small class="field-error" id="password-error"></small>
      </div>

      <div class="row">
        <label class="checkbox">
          <input type="checkbox" id="remember">
          <span>Remember my username</span>
        </label>
      </div>

      <button type="submit" id="submit-btn" class="btn-primary">
        <span class="btn-text">Log In</span>
        <span class="spinner" aria-hidden="true"></span>
      </button>
    </form>

    <footer class="login-footer">
      Authorized administrators only.
    </footer>
  </main>

  <script src="assets/js/script.js"></script>
</body>
</html>