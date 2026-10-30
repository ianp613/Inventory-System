<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator Login | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="login-page" data-page="login">

  <main class="login-card" aria-labelledby="login-title">
    <header class="login-header">
      <img class="login-seal" src="assets/img/palo-seal.png" alt="Municipality of Palo official seal" width="96" height="96">
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
