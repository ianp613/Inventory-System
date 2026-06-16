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
      <div class="pgs-avatar">RV</div>
      <div>
        <div class="pgs-name">R. Villanueva</div>
        <div class="pgs-role">Supervisor · Admin Department</div>
      </div>
    </div>
    <div class="pgs-topbar-right">
      <span class="pgs-badge pgs-badge-red"><span class="pgs-badge-dot"></span> 1 active incident</span>
      <button class="pgs-iconbtn" aria-label="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <button class="pgs-iconbtn" aria-label="More options">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
      </button>
    </div>
  </div>

  <!-- METRICS -->
  <div class="pgs-metrics">
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">MY OPEN TICKETS</div>
      <div class="pgs-metric-val pgs-blue">0</div>
    </div>
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">AWAITING SIGN-OFF</div>
      <div class="pgs-metric-val pgs-amber">0</div>
    </div>
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">WORKSTATIONS RESOLVED</div>
      <div class="pgs-metric-val pgs-green">0 / 0</div>
    </div>
    <div class="pgs-metric">
      <div class="pgs-metric-lbl">TICKETS CLOSED (ALL TIME)</div>
      <div class="pgs-metric-val">0</div>
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
          <input type="datetime-local" id="pgsDatetime" value="2024-06-08T14:32">
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
            <option>Medium</option>
            <option>Low</option>
          </select>
        </div>
      </div>

      <div class="pgs-row">
        <div class="pgs-field">
          <label for="pgsArea">Area / floor affected</label>
          <input type="text" id="pgsArea" value="">
        </div>
        <div class="pgs-field" style="max-width:200px">
          <label for="pgsDuration">Duration (minutes)</label>
          <input type="number" id="pgsDuration" value="0">
        </div>
      </div>

      <div class="pgs-row">
        <div class="pgs-field">
          <label for="pgsDescription">Description</label>
          <textarea id="pgsDescription" placeholder="Aa"></textarea>
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
        <button type="button" class="pgs-btn pgs-btn-primary" id="pgsSubmitTicketBtn">
          Submit incident ticket
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
        </button>
      </div>
    </div>

    <!-- MY TICKETS -->
    <div class="pgs-pane" id="pgsTickets">
      <div class="pgs-table-wrap" style="overflow-x:auto">
        <table class="pgs-tickets-table">
          <thead>
            <tr>
              <th>Ticket no.</th>
              <th>Date</th>
              <th>Type</th>
              <th>Area</th>
              <th>Workstations</th>
              <th style="min-width:120px">Progress</th>
              <th style="min-width:130px">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="pgs-ticket-no">#2024-0147</td>
              <td style="color:var(--pgs-ink-soft)">Jun 8, 2024</td>
              <td>Voltage spike</td>
              <td style="color:var(--pgs-ink-soft)">2F Admin Wing</td>
              <td>
                <span class="pgs-chip" style="border-color:var(--pgs-red);color:var(--pgs-red)">WS-101</span>
                <span class="pgs-chip" style="border-color:var(--pgs-red);color:var(--pgs-red)">WS-102</span>
                <span class="pgs-chip" style="border-color:var(--pgs-green);color:var(--pgs-green)">WS-104</span>
                <span class="pgs-chip" style="color:var(--pgs-ink-faint)">+11 more</span>
              </td>
              <td>
                <div class="pgs-progress-text">3 / 14 resolved</div>
                <div class="pgs-progress-bar"><div class="pgs-progress-fill" style="width:21%"></div></div>
              </td>
              <td><span class="pgs-badge pgs-badge-amber"><span class="pgs-badge-dot"></span> In progress</span></td>
            </tr>
            <tr>
              <td class="pgs-ticket-no">#2024-0089</td>
              <td style="color:var(--pgs-ink-soft)">Mar 12, 2024</td>
              <td>Power surge</td>
              <td style="color:var(--pgs-ink-soft)">3F Admin Wing</td>
              <td>
                <span class="pgs-chip" style="border-color:var(--pgs-green);color:var(--pgs-green)">WS-07</span>
                <span class="pgs-chip" style="border-color:var(--pgs-green);color:var(--pgs-green)">WS-08</span>
              </td>
              <td>
                <div class="pgs-progress-text">2 / 2 resolved</div>
                <div class="pgs-progress-bar"><div class="pgs-progress-fill" style="width:100%"></div></div>
              </td>
              <td><span class="pgs-badge pgs-badge-green"><span class="pgs-badge-dot"></span> Closed</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SIGN-OFF QUEUE -->
    <div class="pgs-pane" id="pgsSignoff">
      <div class="pgs-signoff-intro">Submitted assessments awaiting your sign-off — Ticket #2024-0147</div>

      <div class="pgs-signoff-row">
        <div class="pgs-signoff-ws">WS-104</div>
        <div class="pgs-signoff-tech">
          <svg style="width:12px;height:12px;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          E. Macaraeg<br><span class="pgs-signoff-tech-time">Jun 8 · 3:28 PM</span>
        </div>
        <div class="pgs-signoff-finding">All components functional. UPS charging normally, system unit POST successful, monitor clear.</div>
        <div class="pgs-signoff-actions">
          <span class="pgs-badge pgs-badge-green"><span class="pgs-badge-dot"></span> OK — cleared</span>
          <div class="pgs-signoff-actions-row">
            <button class="pgs-btn pgs-btn-sm pgs-btn-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
              Sign off
            </button>
            <button class="pgs-btn pgs-btn-sm pgs-btn-reject">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
              Reject
            </button>
          </div>
        </div>
      </div>

      <div class="pgs-signoff-row">
        <div class="pgs-signoff-ws">WS-109</div>
        <div class="pgs-signoff-tech">
          <svg style="width:12px;height:12px;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          E. Macaraeg<br><span class="pgs-signoff-tech-time">Jun 8 · 3:45 PM</span>
        </div>
        <div class="pgs-signoff-finding">All components functional. Cleared for use.</div>
        <div class="pgs-signoff-actions">
          <span class="pgs-badge pgs-badge-green"><span class="pgs-badge-dot"></span> OK — cleared</span>
          <div class="pgs-signoff-actions-row">
            <button class="pgs-btn pgs-btn-sm pgs-btn-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
              Sign off
            </button>
            <button class="pgs-btn pgs-btn-sm pgs-btn-reject">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
              Reject
            </button>
          </div>
        </div>
      </div>

      <div class="pgs-signoff-row">
        <div class="pgs-signoff-ws">WS-101</div>
        <div class="pgs-signoff-tech">
          <svg style="width:12px;height:12px;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          E. Macaraeg<br><span class="pgs-signoff-tech-time">Jun 8 · 3:10 PM</span>
        </div>
        <div class="pgs-signoff-finding">UPS damaged — replace unit. System unit intact and boots normally. Parts requested: APC 650VA x1.</div>
        <div class="pgs-signoff-actions">
          <span class="pgs-badge pgs-badge-amber"><span class="pgs-badge-dot"></span> Parts pending</span>
          <div class="pgs-signoff-actions-row">
            <button class="pgs-btn pgs-btn-sm pgs-btn-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
              Sign off
            </button>
            <button class="pgs-btn pgs-btn-sm pgs-btn-reject">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
              Reject
            </button>
          </div>
        </div>
      </div>

      <div class="pgs-signoff-row pgs-disabled">
        <div class="pgs-signoff-ws">WS-102</div>
        <div class="pgs-signoff-tech">
          <svg style="width:12px;height:12px;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          R. Bautista<br><span class="pgs-signoff-tech-time">Not yet submitted</span>
        </div>
        <div class="pgs-signoff-finding" style="color:var(--pgs-ink-faint)">Assessment in progress…</div>
        <div class="pgs-signoff-actions">
          <span class="pgs-badge" style="background:var(--pgs-panel-2);color:var(--pgs-ink-faint);border:1px solid var(--pgs-line)">Awaiting tech</span>
        </div>
      </div>
    </div>

    <!-- REPORTS -->
    <div class="pgs-pane" id="pgsReports">
      <div class="pgs-report-intro">Reports are scoped to your own submitted tickets only.</div>
      <div class="pgs-report-grid">
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #2024-0147</div>
            <div class="pgs-rcard-val">Incident summary</div>
          </div>
          <button class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #2024-0147</div>
            <div class="pgs-rcard-val">Damage assessment</div>
          </div>
          <button class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #2024-0147</div>
            <div class="pgs-rcard-val">Technician log</div>
          </div>
          <button class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">CURRENT TICKET — #2024-0147</div>
            <div class="pgs-rcard-val">Parts request</div>
          </div>
          <button class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">ALL MY TICKETS</div>
            <div class="pgs-rcard-val">Historical summary</div>
          </div>
          <button class="pgs-btn pgs-btn-sm">Generate</button>
        </div>
        <div class="pgs-rcard">
          <div>
            <div class="pgs-rcard-lbl">ALL MY TICKETS</div>
            <div class="pgs-rcard-val">Incident frequency</div>
          </div>
          <button class="pgs-btn pgs-btn-sm">Generate</button>
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