<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Duplicate Flags | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page" data-page="duplicates">

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
        <h1>Duplicate Flags</h1>
        <div class="topbar-actions">
          <button type="button" class="btn-outline" id="print-btn">Print List</button>
          <a class="btn-solid" href="reports.php">Reports &amp; Export</a>
        </div>
      </header>

      <main class="content">
        <div id="dup-alert" class="alert" role="alert" hidden></div>

        <!-- Only visible when printing -->
        <div class="print-only print-head">
          <img class="print-seal" src="assets/img/palo-seal.png" alt="">
          <h2>Palo Registry System for Basic Sectors in Agriculture (RSBSA)</h2>
          <p><strong>Duplicate Flags</strong> &middot; <span id="print-meta"></span></p>
        </div>

        <p class="intro">
          A record is flagged when a farmer registers a cadastral lot and tax declaration that is already registered to another farmer.
          Review the supporting documents, add your remarks, and mark the flag as resolved once it is settled.
        </p>

        <section class="panel" aria-label="Duplicate flags">
          <div class="tabs no-print" role="tablist" aria-label="Flag status">
            <button type="button" class="tab active" role="tab" data-status="open" aria-selected="true">Open <span class="count" id="count-open">0</span></button>
            <button type="button" class="tab" role="tab" data-status="resolved" aria-selected="false">Resolved <span class="count" id="count-resolved">0</span></button>
            <button type="button" class="tab" role="tab" data-status="all" aria-selected="false">All</button>
          </div>

          <div class="list-head">
            <div class="field search-inline no-print">
              <label><span class="lbl sr-only">Search</span>
                <input type="text" id="d-q" placeholder="Search lot No., farmer or barangay" autocomplete="off"></label>
            </div>
            <label class="per-page no-print">
              <span class="muted">Rows per page</span>
              <select id="per-page">
                <option>10</option>
                <option selected>25</option>
                <option>50</option>
                <option>100</option>
              </select>
            </label>
          </div>

          <p class="muted" id="result-info">Loading...</p>

          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Lot No.</th>
                  <th>Barangay</th>
                  <th>Flagged Farmer</th>
                  <th>Registered To</th>
                  <th class="num">Area (ha)</th>
                  <th class="num">Tax Decl. (ha)</th>
                  <th>Flagged On</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th class="num no-print">Actions</th>
                </tr>
              </thead>
              <tbody id="dup-body">
                <tr><td colspan="10" class="empty">Loading...</td></tr>
              </tbody>
            </table>
          </div>

          <nav class="pagination no-print" id="pagination" aria-label="Pages"></nav>
        </section>
      </main>
    </div>
  </div>

  <!-- ===== Review window ===== -->
  <dialog id="review-dialog" class="modal" aria-labelledby="review-title">
    <header class="modal-head">
      <h2 id="review-title">Review Duplicate Flag</h2>
      <button type="button" class="modal-close" id="review-close" aria-label="Close">&times;</button>
    </header>
    <div class="modal-body">
      <div id="review-alert" class="alert" role="alert" hidden></div>
      <div id="review-info"></div>

      <h3 class="detail-title">Supporting Documents</h3>
      <div id="review-docs"></div>

      <h3 class="detail-title">Remarks</h3>
      <div class="field">
        <label><span class="lbl sr-only">Remarks</span>
          <textarea id="review-remarks" rows="4" maxlength="1000" placeholder="Explain why this duplication is accepted or rejected"></textarea></label>
        <small class="field-error" id="review-remarks-error"></small>
      </div>
    </div>
    <footer class="modal-foot">
      <button type="button" class="btn-outline" id="review-cancel">Close</button>
      <button type="button" class="btn-outline" id="review-save">Save Remarks</button>
      <button type="button" class="btn-solid" id="review-toggle">Mark as Resolved</button>
    </footer>
  </dialog>

  <script src="assets/js/script.js"></script>
</body>
</html>
