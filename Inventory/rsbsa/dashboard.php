<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Palo RSBSA</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page" data-page="dashboard">

  <div class="app">

    <!-- ===== Sidebar (same block will be reused on every admin page) ===== -->
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
      <div class="sidebar-brand">
        <span class="brand-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22V10"/><path d="M12 10C12 6 9 4 5 4c0 4 2 6 7 6z"/><path d="M12 14c0-3.5 2.5-5.5 7-5.5 0 3.5-2 5.5-7 5.5z"/>
          </svg>
        </span>
        <div>
          <strong>Palo RSBSA</strong>
          <small>Administrator</small>
        </div>
      </div>

      <nav class="nav">
        <a href="dashboard.php" data-nav="dashboard">Dashboard</a>
        <a href="farmers.php" data-nav="farmers">Farmers</a>
        <a href="register.php" data-nav="register">Register Farmer</a>
        <a href="duplicates.php" data-nav="duplicates">Duplicate Flags</a>
        <a href="reports.php" data-nav="reports">Reports &amp; Export</a>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user" id="user-name">Administrator</div>
        <button type="button" class="btn-ghost" id="logout-btn">Log Out</button>
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
            <span class="stat-label">Total Area (ha)</span>
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
            <h2 id="commodity-title">Area by Sectoral Commodity</h2>
            <div id="commodity-list" class="bars">
              <p class="empty">Loading...</p>
            </div>
          </section>

          <!-- By barangay -->
          <section class="panel" aria-labelledby="barangay-title">
            <h2 id="barangay-title">Farmers by Barangay</h2>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr><th>Barangay</th><th class="num">Farmers</th><th class="num">Area (ha)</th></tr>
                </thead>
                <tbody id="barangay-body">
                  <tr><td colspan="3" class="empty">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>

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

  <script src="assets/js/script.js"></script>
</body>
</html>