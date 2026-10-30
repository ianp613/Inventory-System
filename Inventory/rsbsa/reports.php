<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports &amp; Export | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page" data-page="reports">

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
        <h1>Reports &amp; Export</h1>
      </header>

      <main class="content">
        <div id="rep-alert" class="alert" role="alert" hidden></div>

        <!-- ===== 1. Farmer masterlist ===== -->
        <section class="panel" aria-labelledby="rep-farmers-title">
          <h2 id="rep-farmers-title">Farmer Masterlist</h2>
          <p class="hint">One row per farmer parcel: registration details, family, parcels, commodity, areas and farming status. Leave the filters empty to export everyone.</p>

          <div class="form-grid cols-4 report-filters">
            <div class="field">
              <label><span class="lbl">Commodity</span>
                <select id="r-commodity">
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
                <select id="r-role">
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
                <select id="r-barangay">
                  <option value="">All</option>
                </select></label>
            </div>
            <div class="field">
              <label><span class="lbl">Date Updated (from)</span>
                <input type="date" id="r-from"></label>
            </div>
            <div class="field">
              <label><span class="lbl">Date Updated (to)</span>
                <input type="date" id="r-to"></label>
              <small class="field-error" id="r-range-error"></small>
            </div>
          </div>

          <div class="report-actions">
            <button type="button" class="btn-solid" data-export data-report="farmers" data-format="xlsx">Download Excel (.xlsx)</button>
            <button type="button" class="btn-outline" data-export data-report="farmers" data-format="csv">Download CSV</button>
          </div>
        </section>

        <div class="grid-2">
          <!-- ===== 2. Duplication report ===== -->
          <section class="panel" aria-labelledby="rep-dup-title">
            <h2 id="rep-dup-title">Duplication Report</h2>
            <p class="hint">Flagged farmer name and lot number, with remarks of duplication, as a printable Excel form.</p>

            <div class="form-grid">
              <div class="field">
                <label><span class="lbl">Flags to include</span>
                  <select id="r-dup-status">
                    <option value="all">All flags</option>
                    <option value="open">Open flags only</option>
                    <option value="resolved">Resolved flags only</option>
                  </select></label>
              </div>
            </div>

            <div class="report-actions">
              <button type="button" class="btn-solid" data-export data-report="duplicates" data-format="xlsx">Download Excel (.xlsx)</button>
              <button type="button" class="btn-outline" data-export data-report="duplicates" data-format="csv">Download CSV</button>
              <a class="btn-outline" href="duplicates.php">Open Duplicate Flags</a>
            </div>
          </section>

          <!-- ===== 3. Printable registration form ===== -->
          <section class="panel" aria-labelledby="rep-form-title">
            <h2 id="rep-form-title">Printable Registration Form</h2>
            <p class="hint">Search for a farmer to print their filled-in RSBSA form, or print a blank form for field encoding.</p>

            <div class="field">
              <label><span class="lbl">Find farmer</span>
                <input type="text" id="r-search" placeholder="Name or RSBSA No." autocomplete="off"></label>
            </div>

            <ul class="pick-list" id="pick-list" hidden></ul>
            <p class="notice info" id="picked" hidden></p>

            <div class="report-actions">
              <button type="button" class="btn-solid" id="print-farmer" disabled>Print Registration Form</button>
              <button type="button" class="btn-outline" id="print-blank">Print Blank Form</button>
            </div>
          </section>
        </div>
      </main>
    </div>
  </div>

  <!-- Filled in by the script when printing a registration form -->
  <div id="print-form" class="print-form" aria-hidden="true"></div>

  <script src="assets/js/script.js"></script>
</body>
</html>
