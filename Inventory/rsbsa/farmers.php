<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmers | Palo RSBSA</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page" data-page="farmers">

  <div class="app">

    <!-- ===== Sidebar ===== -->
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
      <button type="button" class="btn-outline" id="view-close-2">Close</button>
      <a class="btn-solid" id="view-edit" href="register.php">Edit Record</a>
    </footer>
  </dialog>

  <script src="assets/js/script.js"></script>
</body>
</html>