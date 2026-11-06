<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super User | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page super-page" data-page="super">

  <div class="app">

    <!-- ===== Sidebar ===== -->
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
      <div class="sidebar-brand">
        <img class="brand-seal" src="assets/img/palo-seal.png" alt="Municipality of Palo seal" width="40" height="40">
        <div>
          <strong>Palo RSBSA</strong>
          <small>Super User</small>
        </div>
      </div>

      <nav class="nav">
        <a href="#accounts" data-nav="accounts"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Accounts</span></a>
        <a href="#log" data-nav="log"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/></svg><span>Activity Log</span></a>
        <a href="#me" data-nav="me"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg><span>My Account</span></a>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user" id="user-name">Super User</div>
        <button type="button" class="btn-ghost" id="logout-btn"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg><span>Log Out</span></button>
      </div>
    </aside>
    <div class="scrim" id="scrim" hidden></div>

    <!-- ===== Main ===== -->
    <div class="main">
      <header class="topbar">
        <button type="button" class="menu-btn" id="menu-btn" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 id="sp-heading">Accounts</h1>
        <div class="topbar-actions"><span class="super-chip" title="The super user username cannot be changed"><svg class="nav-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg><span id="sp-chip-name">rsbsa_super_user</span></span></div>
      </header>

      <main class="content">

        <!-- ===== Accounts ===== -->
        <section class="panel" id="tab-accounts" aria-labelledby="ac-title">
          <div class="panel-head">
            <div>
              <h2 id="ac-title">Administrator accounts</h2>
              <p class="hint">Add an account, change its name or username, or reset its password.</p>
            </div>
            <button type="button" class="btn-solid" id="ac-add">Add administrator</button>
          </div>
          <div id="ac-default" class="notice warn" hidden>You are still using the default super user password. <a href="#me" data-goto="me">Change it now</a>.</div>
          <div id="ac-alert" class="alert" role="alert" hidden></div>
          <div class="table-wrap">
            <table class="data-table" id="ac-table">
              <thead>
                <tr><th>Name</th><th>Username</th><th>Last login</th><th>Status</th><th class="num">Actions</th></tr>
              </thead>
              <tbody id="ac-body"><tr><td colspan="5" class="muted">Loading...</td></tr></tbody>
            </table>
          </div>
        </section>

        <!-- ===== Activity log ===== -->
        <section class="panel" id="tab-log" aria-labelledby="lg-title" hidden>
          <div class="panel-head">
            <div>
              <h2 id="lg-title">Super user activity log</h2>
              <p class="hint">What the super user did. Only the super user can see this log. It is not part of the backups and it cannot be cleared.</p>
            </div>
          </div>
          <div id="lg-alert" class="alert" role="alert" hidden></div>
          <div class="table-wrap">
            <table class="data-table" id="lg-table">
              <thead><tr><th>Date and time</th><th>Action</th><th>Account</th><th>Details</th><th>Computer (IP)</th></tr></thead>
              <tbody id="lg-body"><tr><td colspan="5" class="muted">Loading...</td></tr></tbody>
            </table>
          </div>
          <div class="pager" id="lg-pager" hidden>
            <button type="button" class="btn-outline btn-sm" id="lg-prev">Previous</button>
            <span id="lg-info" class="muted"></span>
            <button type="button" class="btn-outline btn-sm" id="lg-next">Next</button>
          </div>
        </section>

        <!-- ===== My account ===== -->
        <div id="tab-me" hidden>
          <section class="panel account-card" aria-labelledby="me-title">
            <h2 id="me-title">Your username</h2>
            <p class="hint">This is the super user username. It is fixed and cannot be changed. Only the password can be changed.</p>
            <div class="field">
              <label><span class="lbl">Username</span>
                <input type="text" id="sp-username" value="rsbsa_super_user" readonly aria-readonly="true" tabindex="-1"></label>
            </div>
            <div id="sp-default" class="notice warn" hidden>You are still using the default password. Please change it below.</div>
          </section>

          <section class="panel account-card" aria-labelledby="pw-title">
            <h2 id="pw-title">Change your password</h2>
            <p class="hint">Use at least 8 characters. After changing it you stay logged in, and the new password is needed the next time you log in.</p>

            <div id="pw-alert" class="alert" role="alert" hidden></div>

            <form id="pw-form" novalidate autocomplete="off">
              <div class="field">
                <label><span class="lbl">Current password</span>
                  <input type="password" id="pw-current" autocomplete="current-password"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">New password</span>
                  <input type="password" id="pw-new" autocomplete="new-password"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Confirm new password</span>
                  <input type="password" id="pw-confirm" autocomplete="new-password"></label>
                <small class="field-error"></small>
              </div>

              <label class="checkbox"><input type="checkbox" id="pw-show"><span>Show passwords</span></label>

              <div class="report-actions">
                <button type="submit" class="btn-primary btn-inline" id="pw-submit">
                  <span class="btn-text">Change Password</span>
                  <span class="spinner" aria-hidden="true"></span>
                </button>
              </div>
            </form>
          </section>
        </div>

      </main>
    </div>
  </div>

  <script src="assets/js/script.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/script.js') ?>"></script>
</body>
</html>
