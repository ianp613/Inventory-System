<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page" data-page="settings">

  <div class="app">

    <!-- ===== Sidebar ===== -->
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
      <div class="sidebar-brand">
        <img class="brand-seal" src="assets/img/palo-seal.png" alt="Municipality of Palo seal" width="40" height="40">
        <div>
          <strong>Palo RSBSA</strong>
          <small>Administrator</small>
        </div>
      </div>

      <nav class="nav">
        <a href="dashboard.php" data-nav="dashboard"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg><span>Dashboard</span></a>
        <a href="farmers.php" data-nav="farmers"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Farmers</span></a>
        <a href="register.php" data-nav="register"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M16 11h6"/></svg><span>Register Farmer</span></a>
        <a href="duplicates.php" data-nav="duplicates"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21V4"/><path d="M4 4h12l-2 4 2 4H4"/></svg><span>Duplicate Flags</span></a>
        <a href="reports.php" data-nav="reports"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/><path d="M8 17v-3M12 17v-6M16 17v-4"/></svg><span>Reports &amp; Export</span></a>
        <a href="activity.php" data-nav="activity"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><span>Activity Logs</span></a>
        <a href="settings.php" data-nav="settings"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/></svg><span>Settings</span></a>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user" id="user-name">Administrator</div>
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
        <h1>Settings</h1>
      </header>

      <main class="content">
        <section class="panel account-card" aria-labelledby="nm-title">
          <h2 id="nm-title">Your name</h2>
          <p class="hint">The name shown in the left menu. You still log in with your username (<strong id="nm-username">&ndash;</strong>).</p>

          <div id="nm-alert" class="alert" role="alert" hidden></div>

          <form id="nm-form" novalidate autocomplete="off">
            <div class="field">
              <label><span class="lbl">Name</span>
                <input type="text" id="nm-name" maxlength="60" autocomplete="name"></label>
              <small class="field-error"></small>
            </div>
            <div class="report-actions">
              <button type="submit" class="btn-primary btn-inline" id="nm-submit">
                <span class="btn-text">Save Name</span>
                <span class="spinner" aria-hidden="true"></span>
              </button>
            </div>
          </form>
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

        <section class="panel account-card" aria-labelledby="al-title">
          <h2 id="al-title">Auto-logout</h2>
          <p class="hint">Log out automatically when the system is not used. A 30-second warning is shown first. Saved on this computer and browser.</p>
          <label class="checkbox"><input type="checkbox" id="al-enabled"><span>Enable auto-logout after inactivity</span></label>
          <div class="field" style="margin-top:12px">
            <label><span class="lbl">Log out after</span>
              <select id="al-minutes">
                <option value="1">1 minute</option>
                <option value="2">2 minutes</option>
                <option value="5">5 minutes</option>
                <option value="10">10 minutes</option>
                <option value="15">15 minutes</option>
                <option value="30">30 minutes</option>
                <option value="60">60 minutes</option>
              </select></label>
          </div>
          <p class="hint" id="al-status" role="status"></p>
        </section>

        <section class="panel account-card" aria-labelledby="th-title">
          <h2 id="th-title">Theme</h2>
          <p class="hint">Choose how the system looks. Printed forms and reports always print in the default colors.</p>
          <div class="theme-options" role="radiogroup" aria-labelledby="th-title">
            <label class="theme-opt"><input type="radio" name="theme" value="default"><span><strong>Default</strong><small>Light theme</small></span></label>
            <label class="theme-opt"><input type="radio" name="theme" value="dark"><span><strong>Dark mode</strong><small>Easier on the eyes in low light</small></span></label>
          </div>
          <p class="hint" id="th-status" role="status"></p>
        </section>

        <section class="panel account-card backup-card" aria-labelledby="bk-title">
          <h2 id="bk-title">Backup &amp; restore</h2>
          <p class="hint">A backup is one file with all farmer records, parcels, history, duplicate flags, activity logs, accounts and uploaded photos and documents. Keep copies on another drive or flash disk.</p>

          <div id="bk-alert" class="alert" role="alert" hidden></div>

          <div class="report-actions">
            <button type="button" class="btn-primary btn-inline" id="bk-now">
              <span class="btn-text">Back Up Now</span>
              <span class="spinner" aria-hidden="true"></span>
            </button>
          </div>

          <h3 class="sub-title">Automatic backup</h3>
          <label class="checkbox"><input type="checkbox" id="bk-auto"><span>Back up automatically</span></label>
          <div class="bk-row">
            <div class="field">
              <label><span class="lbl">How often</span>
                <select id="bk-freq">
                  <option value="daily">Daily</option>
                  <option value="weekly">Weekly</option>
                </select></label>
            </div>
            <div class="field">
              <label><span class="lbl">Keep the latest</span>
                <select id="bk-keep">
                  <option value="3">3 automatic backups</option>
                  <option value="7">7 automatic backups</option>
                  <option value="14">14 automatic backups</option>
                  <option value="30">30 automatic backups</option>
                  <option value="60">60 automatic backups</option>
                </select></label>
            </div>
            <button type="button" class="btn-outline" id="bk-save">Save</button>
          </div>
          <p class="hint">The system makes the backup the first time someone logs in or opens the dashboard after it is due, so the computer must be on and the system opened. Older automatic backups are removed; manual backups are never removed automatically.</p>

          <h3 class="sub-title">Saved backups</h3>
          <p class="hint" id="bk-folder"></p>
          <div class="table-wrap">
            <table class="data-table" id="bk-table">
              <thead><tr><th>Date &amp; time</th><th>Type</th><th>Size</th><th class="no-print">Actions</th></tr></thead>
              <tbody id="bk-body"><tr><td colspan="4" class="muted">Loading...</td></tr></tbody>
            </table>
          </div>

          <h3 class="sub-title">Restore from a backup file</h3>
          <p class="hint">Choose a backup file (.zip) from another computer or drive. <strong>Restoring replaces all current data.</strong> A safety backup of the current data is made first.</p>
          <div class="bk-row">
            <div class="field bk-file">
              <label><span class="lbl">Backup file</span>
                <input type="file" id="bk-file" accept=".zip,application/zip"></label>
              <small class="field-error" id="bk-file-error"></small>
            </div>
            <button type="button" class="btn-outline" id="bk-restore-file">Restore&hellip;</button>
          </div>
          <p class="hint" id="bk-limit"></p>
        </section>

        <section class="panel account-card" aria-labelledby="dv-title">
          <h2 id="dv-title">About</h2>
          <img class="about-seal" src="assets/img/palo-seal.png" alt="Municipality of Palo official seal" width="72" height="72">
          <p class="hint">Palo Registry System for Basic Sectors in Agriculture (RSBSA)</p>
          <p class="dev-label">Developers</p>
          <ul class="dev-list">
            <li>P.I. Dumdum</li>
            <li>J. Dacatimbang</li>
            <li>R. Aguilar</li>
          </ul>
        </section>
      </main>
    </div>
  </div>

  <script src="assets/js/script.js"></script>
</body>
</html>
