<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page" data-page="dashboard">

  <div class="app">

    <!-- ===== Sidebar (same block will be reused on every admin page) ===== -->
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
        <h1>Dashboard</h1>
        <div class="topbar-actions">
          <span class="muted" id="last-updated"></span>
          <button type="button" class="btn-outline" id="refresh-btn">Refresh</button>
          <a class="btn-solid" href="register.php">+ Register Farmer</a>
        </div>
      </header>

      <main class="content">
        <div id="dash-alert" class="alert" role="alert" hidden></div>

        <!-- Summary cards -->
        <section class="stat-grid" aria-label="Summary">
          <article class="stat">
            <span class="stat-label">Registered Farmers</span>
            <strong class="stat-value" id="stat-farmers">&ndash;</strong>
          </article>
          <article class="stat">
            <span class="stat-label">Registered Parcels</span>
            <strong class="stat-value" id="stat-parcels">&ndash;</strong>
          </article>
          <article class="stat">
            <span class="stat-label">Total Area (ha, active parcels)</span>
            <strong class="stat-value" id="stat-area">&ndash;</strong>
          </article>
          <article class="stat stat-warn">
            <span class="stat-label">Duplicate Flags</span>
            <strong class="stat-value" id="stat-duplicates">&ndash;</strong>
            <a class="stat-link" href="duplicates.php">Review</a>
          </article>
        </section>

        <div class="grid-2">
          <!-- By commodity -->
          <section class="panel" aria-labelledby="commodity-title">
            <div class="panel-head">
              <h2 id="commodity-title">Area by Sectoral Commodity</h2>
              <label class="panel-filter"><span class="lbl">Barangay</span>
                <select id="commodity-barangay" aria-label="Show commodities for one barangay"><option value="">All</option></select></label>
            </div>
            <div id="commodity-chart" class="donut-wrap">
              <p class="empty">Loading...</p>
            </div>
          </section>

          <!-- By barangay -->
          <section class="panel" aria-labelledby="barangay-title">
            <h2 id="barangay-title">Farmers by Barangay</h2>
            <div id="barangay-chart" class="bars">
              <p class="empty">Loading...</p>
            </div>
          </section>
        </div>

        <!-- Registrations per month -->
        <section class="panel" aria-labelledby="month-title">
          <h2 id="month-title">Registrations per Month <span class="muted">(last 12 months)</span></h2>
          <div id="month-chart" class="col-chart">
            <p class="empty">Loading...</p>
          </div>
        </section>

        <!-- Recent updates -->
        <section class="panel" aria-labelledby="recent-title">
          <h2 id="recent-title">Recently Updated Records</h2>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>RSBSA No.</th><th>Farmer</th><th>Barangay</th><th class="num">Total Area (ha)</th><th>Date Updated</th></tr>
              </thead>
              <tbody id="recent-body">
                <tr><td colspan="5" class="empty">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="assets/js/script.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/script.js') ?>"></script>
</body>
</html>
