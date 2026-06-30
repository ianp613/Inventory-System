<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DDC PowerGuard — Supervisor</title>
<link rel="stylesheet" href="../../assets/css/powerguard.css">

</head>
<body class="pgs-body">

<div class="pgs-shell">

<!-- TOP BAR -->
<div class="pgs-topbar">
  <div class="pgs-topbar-left">
    <div class="pgs-avatar"></div>
    <div>
      <div class="pgs-name"></div>
      <div class="pgs-role"></div>
    </div>
  </div>
  <div class="pgs-topbar-right">
    <span id="pgs_active_incident_container" hidden class="pgs-badge-red"><span class="pgs-badge-dot"></span><span id="pgs_active_incident"> 1 active incident</span></span>
    <button class="pgs-iconbtn" aria-label="Notifications">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </button>
    <div style="position:relative;display:inline-flex;">
      <button class="pgs-iconbtn" aria-label="More options" id="pgsMoreBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
      </button>
      <div class="pgs-dropdown" id="pgsMoreDropdown">
        <button class="pgs-dropdown-item" id="pgsSettingsBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          Settings
        </button>
        <div class="pgs-dropdown-divider"></div>
        <button class="pgs-dropdown-item pgs-dropdown-item-danger" id="pgsLogoutBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Log out
        </button>
      </div>
    </div>
  </div>
</div>

  <!-- METRICS -->
  <div class="pgs-metrics">
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">MY OPEN TICKETS</div>
      <div id="pgs_open_ticket" class="pgs-metric-val pgs-blue">0</div>
    </div>
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">AWAITING SIGN-OFF</div>
      <div id="pgs_awaiting_sign_off" class="pgs-metric-val pgs-amber">0</div>
    </div>
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">WORKSTATIONS RESOLVED</div>
      <div id="pgs_workstation_resolved" class="pgs-metric-val pgs-green">0 / 0</div>
    </div>
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">TICKETS CLOSED (ALL TIME)</div>
      <div id="pgs_ticket_closed" class="pgs-metric-val">0</div>
    </div>
  </div>

  <!-- MAIN CARD -->
  <div class="pgs-card">
    <div class="pgs-tabbar">
      <div class="pgs-tab pgs-tab-active" data-pane="pgsSubmit">Submit ticket</div>
      <div class="pgs-tab" data-pane="pgsTickets">My tickets</div>
      <div class="pgs-tab" data-pane="pgsSignoff">Sign-off queue</div>
      <div class="pgs-tab" data-pane="pgsReports">Reports</div>
    </div>

    <!-- SUBMIT TICKET -->
    <div class="pgs-pane pgs-pane-active" id="pgsSubmit">
      <div class="pgs-row">
        <div class="pgs-field">
          <label for="pgsDatetime">Incident date &amp; time</label>
          <input type="datetime-local" id="pgsDatetime">
          <span class="pgs-field-error" id="pgsDatetimeError">Incident date & time is required.</span>
        </div>
        <div class="pgs-field">
          <label for="pgsFlucType">Fluctuation type</label>
          <select id="pgsFlucType">
            <option>Voltage spike</option>
            <option>Power surge</option>
            <option>Brownout</option>
            <option>Complete outage</option>
            <option>Lightning strike</option>
          </select>
        </div>
        <div class="pgs-field">
          <label for="pgsPriority">Priority</label>
          <select id="pgsPriority">
            <option>High</option>
            <option selected>Medium</option>
            <option>Low</option>
          </select>
        </div>
      </div>

      <div class="pgs-row">
        <div class="pgs-field">
          <label for="pgsDept">Department</label>
          <select id="pgsDept">
            <option value="">Select department…</option>
            <!-- options injected by JS -->
          </select>
          <span class="pgs-field-error" id="pgsDeptError">Please select a department.</span>
        </div>
        <div class="pgs-field">
          <label for="pgsArea">Area / floor affected</label>
          <input type="text" id="pgsArea" value="">
        </div>
        <div class="pgs-field" style="max-width:200px">
          <label for="pgsDuration">Duration (minutes)</label>
          <input type="number" id="pgsDuration" value="0">
          <span class="pgs-field-error" id="pgsDurationError">Enter a valid duration in minutes.</span>
        </div>
      </div>

      <div class="pgs-row">
        <div class="pgs-field">
          <label for="pgsDescription">Description</label>
          <textarea id="pgsDescription"></textarea>
          <span class="pgs-field-error" id="pgsDescriptionError">Description is required.</span>
        </div>
      </div>

      <div class="pgs-section-title">Workstation damage declaration</div>

      <div class="pgs-table-wrap">
        <table class="pgs-ws-table">
          <thead>
            <tr>
              <th>WS no.</th>
              <th>Assigned user</th>
              <th>UPS</th>
              <th>System unit</th>
              <th>Monitor</th>
              <th>Notes</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="pgsWsTbody">
            <!-- rows injected by JS -->
          </tbody>
        </table>
      </div>

      <div class="pgs-add-row">
        <button type="button" class="pgs-btn pgs-btn-sm" id="pgsAddWsBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
          Add workstation
        </button>
      </div>

      <div class="pgs-form-footer">
        <button type="button" class="pgs-btn" id="pgsClearFormBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
          Clear form
        </button>
        <button type="button" class="pgs-btn pgs-btn-primary" id="pgsSubmitTicketBtn">
          Submit incident ticket
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
        </button>
      </div>
    </div>

    <!-- MY TICKETS -->
    <div class="pgs-pane" id="pgsTickets">
      <div class="pgs-table-toolbar" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:10px;flex-wrap:wrap">
        <div class="pgs-rows-select-wrap" style="display:flex;align-items:center;gap:8px">
          <label for="pgsRowsPerPage" style="font-size:12px;color:var(--pgs-ink-soft);white-space:nowrap">Rows per page</label>
          <select id="pgsRowsPerPage" style="font-size:12.5px;padding:6px 24px 6px 10px;border-radius:6px;border:1px solid var(--pgs-line);background:var(--pgs-panel-2);color:var(--pgs-ink);width:auto">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>

        <div class="pgs-search-wrap" style="position:relative;max-width:280px;width:100%">
          <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--pgs-ink-faint);pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="pgsTicketSearch" placeholder="Search ticket no, department, type, area…" style="width:100%;font-size:12.5px;padding:7px 10px 7px 30px;border-radius:6px;border:1px solid var(--pgs-line);background:var(--pgs-panel-2);color:var(--pgs-ink)">
        </div>
      </div>
      <div class="pgs-table-wrap" style="overflow-x:auto">
        <table class="pgs-tickets-table">
          <thead>
            <tr>
              <th style="min-width:110px">Ticket no.</th>
              <th style="min-width:100px">Date</th>
              <th style="min-width:120px">Type</th>
              <th>Area</th>
              <th>Workstations</th>
              <th style="min-width:120px">Progress</th>
              <th style="min-width:130px">Status</th>
            </tr>
          </thead>
          <tbody id="pgsTicketsTbody">
            <!-- rows injected by JS -->
          </tbody>
        </table>
      </div>

      <div id="pgsTicketsEmpty" style="display:none;text-align:center;padding:30px;color:var(--pgs-ink-soft);font-size:13px">
        No tickets submitted yet.
      </div>

      <div class="pgs-pagination" id="pgsTicketsPagination" style="display:flex;align-items:center;justify-content:space-between;margin-top:14px">
        <div class="pgs-pagination-info" id="pgsPaginationInfo" style="font-size:12px;color:var(--pgs-ink-soft)"></div>
        <div class="pgs-pagination-controls" style="display:flex;gap:6px">
          <button class="pgs-btn pgs-btn-sm" id="pgsPrevPageBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M15 18l-6-6 6-6"/></svg>
            Prev
          </button>
          <div id="pgsPageNumbers" style="display:flex;gap:4px"></div>
          <button class="pgs-btn pgs-btn-sm" id="pgsNextPageBtn">
            Next
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>

    
    <!-- SIGN-OFF QUEUE -->
    <div class="pgs-pane" id="pgsSignoff">
      <div id="pgsSignoffContainer">
        <!-- ticket groups injected by JS -->
      </div>
    </div>

    <!-- REPORTS -->
    <div class="pgs-pane" id="pgsReports" style="color:var(--pgs-ink-faint)">
      <div class="pgs-report-intro">Reports are scoped to your own submitted tickets only.</div>
      <div class="pgs-report-grid">
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #0000-0000</div>
            <div class="pgs-rcard-val">Incident summary</div>
          </div>
          <button style="color:var(--pgs-ink-faint)" class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #0000-0000</div>
            <div class="pgs-rcard-val">Damage assessment</div>
          </div>
          <button style="color:var(--pgs-ink-faint)" class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #0000-0000</div>
            <div class="pgs-rcard-val">Technician log</div>
          </div>
          <button style="color:var(--pgs-ink-faint)" class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #0000-0000</div>
            <div class="pgs-rcard-val">Parts request</div>
          </div>
          <button style="color:var(--pgs-ink-faint)" class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">ALL MY TICKETS</div>
            <div class="pgs-rcard-val">Historical summary</div>
          </div>
          <button style="color:var(--pgs-ink-faint)" class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">ALL MY TICKETS</div>
            <div class="pgs-rcard-val">Incident frequency</div>
          </div>
          <button style="color:var(--pgs-ink-faint)" class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="../../assets/js/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/js/sole.js"></script>
<script src="../../assets/js/sole.swal.js"></script>
<script src="../../assets/js/powerguard/splash.js"></script>
<script src="../../assets/js/powerguard/supervisor.js"></script>
</body>
</html>