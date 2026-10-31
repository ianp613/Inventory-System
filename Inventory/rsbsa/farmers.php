<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmers | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page" data-page="farmers">

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
        <h1>Farmers</h1>
        <div class="topbar-actions">
          <a class="btn-solid" href="register.php">+ Register Farmer</a>
        </div>
      </header>

      <main class="content">
        <div id="list-alert" class="alert" role="alert" hidden></div>

        <!-- Search and filters -->
        <section class="panel" aria-label="Search and filters">
          <form id="filter-form" class="filters" novalidate>
            <div class="field filter-search">
              <label><span class="lbl">Search</span>
                <input type="text" id="f-q" placeholder="Name, RSBSA No., lot No. or barangay" autocomplete="off"></label>
            </div>
            <div class="field">
              <label><span class="lbl">Commodity</span>
                <select id="f-commodity">
                  <option value="">All</option>
                  <option>Rice</option>
                  <option>Vegetable</option>
                  <option>Root Crops</option>
                  <option>Banana</option>
                  <option>Coconut</option>
                  <option>Livestock</option>
                  <option>Fishery</option>
                </select></label>
            </div>
            <div class="field">
              <label><span class="lbl">Farming Status</span>
                <select id="f-role">
                  <option value="">All</option>
                  <option>Owner</option>
                  <option>Tenant</option>
                  <option>Leasee</option>
                  <option>Actual Tiller</option>
                  <option>Farm Worker</option>
                </select></label>
            </div>
            <div class="field">
              <label><span class="lbl">Barangay</span>
                <select id="f-barangay">
                  <option value="">All</option>
                </select></label>
            </div>
            <div class="filter-actions">
              <label class="checkbox"><input type="checkbox" id="f-flagged"><span>Duplicates only</span></label>
              <button type="button" class="btn-outline btn-sm" id="f-reset">Reset</button>
            </div>
          </form>
        </section>

        <!-- Results -->
        <section class="panel" aria-label="Farmer list">
          <div class="list-head">
            <span class="muted" id="result-info">Loading...</span>
            <label class="per-page">
              <span class="muted">Rows per page</span>
              <select id="per-page">
                <option>10</option>
                <option selected>25</option>
                <option>50</option>
                <option>100</option>
              </select>
            </label>
          </div>

          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th><button type="button" class="sort" data-sort="rsbsa_no">RSBSA No.</button></th>
                  <th><button type="button" class="sort" data-sort="name">Farmer</button></th>
                  <th>Barangay</th>
                  <th>Commodities</th>
                  <th class="num">Parcels</th>
                  <th class="num"><button type="button" class="sort" data-sort="total_area_ha">Total Area (ha)</button></th>
                  <th><button type="button" class="sort" data-sort="date_updated">Date Updated</button></th>
                  <th class="num">Actions</th>
                </tr>
              </thead>
              <tbody id="farmers-body">
                <tr><td colspan="8" class="empty">Loading...</td></tr>
              </tbody>
            </table>
          </div>

          <nav class="pagination" id="pagination" aria-label="Pages"></nav>
        </section>
      </main>
    </div>
  </div>

  <!-- ===== Record details ===== -->
  <dialog id="view-dialog" class="modal" aria-labelledby="view-title">
    <header class="modal-head">
      <h2 id="view-title">Farmer Record</h2>
      <button type="button" class="modal-close" id="view-close" aria-label="Close">&times;</button>
    </header>
    <div class="modal-body" id="view-body"></div>
    <footer class="modal-foot">
      <button type="button" class="btn-danger" id="view-delete" disabled>Delete Farmer</button>
      <span class="spacer"></span>
      <button type="button" class="btn-outline" id="view-close-2">Close</button>
      <a class="btn-solid" id="view-edit" href="register.php">Edit Record</a>
    </footer>
  </dialog>

  <script src="assets/js/script.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/script.js') ?>"></script>
</body>
</html>
