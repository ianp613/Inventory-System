<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DDC PowerGuard — Administrator</title>
<link rel="stylesheet" href="../../assets/css/powerguard.css">
</head>
<body class="pga-body">

<div class="pga-shell">

  <!-- TOP BAR -->
  <div class="pga-topbar">
    <div class="pga-topbar-left">
      <div class="pga-avatar">SA</div>
      <div>
        <div class="pga-name">System Admin</div>
        <div class="pga-role-label">Administrator · Full system access</div>
      </div>
    </div>
    <div class="pga-topbar-right">
      <span class="pga-badge pga-badge-red"><span class="pga-badge-dot"></span> 2 active incidents</span>
      <span class="pga-badge pga-badge-amber"><span class="pga-badge-dot"></span> 2 pending approvals</span>
      <button class="pga-iconbtn" aria-label="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <div style="position:relative;display:inline-flex;">
        <button class="pga-iconbtn" aria-label="More options" id="pgaMoreBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
        </button>
        <div class="pga-dropdown" id="pgaMoreDropdown">
          <button class="pga-dropdown-item" id="pgaSettingsBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Settings
          </button>
          <div class="pga-dropdown-divider"></div>
          <button class="pga-dropdown-item pga-dropdown-item-danger" id="pgaLogoutBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log out
          </button>
        </div>  
      </div>
    </div>
  </div>

  <!-- METRICS -->
  <div class="pga-metrics">
    <div class="pga-metric"><div class="pga-metric-lbl">OPEN TICKETS</div><div class="pga-metric-val pga-red">2</div></div>
    <div class="pga-metric"><div class="pga-metric-lbl">TOTAL WORKSTATIONS</div><div class="pga-metric-val pga-blue">18</div></div>
    <div class="pga-metric"><div class="pga-metric-lbl">ACTIVE TECHNICIANS</div><div class="pga-metric-val pga-green">6</div></div>
    <div class="pga-metric"><div class="pga-metric-lbl">DEPARTMENTS</div><div class="pga-metric-val">7</div></div>
    <div class="pga-metric"><div class="pga-metric-lbl">PENDING APPROVALS</div><div class="pga-metric-val pga-amber">2</div></div>
  </div>

  <!-- MAIN CARD -->
  <div class="pga-card">
    <div class="pga-tabbar">
      <div class="pga-tab pga-tab-active" data-pane="pgaOverview">Overview</div>
      <div class="pga-tab" data-pane="pgaTickets">All tickets</div>
      <div class="pga-tab" data-pane="pgaReassign">Reassign terminals</div>
      <div class="pga-tab" data-pane="pgaDepartments">Departments</div>
      <div class="pga-tab" data-pane="pgaApprovals">Account approvals</div>
      <div class="pga-tab" data-pane="pgaCreateTech">Create technician</div>
      <div class="pga-tab" data-pane="pgaManageAccounts">Manage accounts</div>
      <div class="pga-tab" data-pane="pgaReports">Reports</div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB: OVERVIEW
    ════════════════════════════════════════ -->
    <div class="pga-pane pga-pane-active" id="pgaOverview">
      <div class="pga-overview-grid">
        <div class="pga-overview-card">
          <div class="pga-overview-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>
            Active incidents
          </div>
          <div class="pga-table-wrap">
            <table class="pga-table">
              <thead><tr><th>Ticket</th><th>Dept.</th><th>Progress</th><th>Status</th></tr></thead>
              <tbody>
                <tr>
                  <td class="pga-mono">#2024-0147</td>
                  <td style="color:var(--pga-ink-soft)">Admin</td>
                  <td><div class="pga-pbar-text">3/14</div><div class="pga-pbar"><div class="pga-pbar-fill" style="width:21%"></div></div></td>
                  <td><span class="pga-badge pga-badge-amber"><span class="pga-badge-dot"></span> In progress</span></td>
                </tr>
                <tr>
                  <td class="pga-mono">#2024-0143</td>
                  <td style="color:var(--pga-ink-soft)">Finance</td>
                  <td><div class="pga-pbar-text">2/4</div><div class="pga-pbar"><div class="pga-pbar-fill" style="width:50%"></div></div></td>
                  <td><span class="pga-badge pga-badge-amber"><span class="pga-badge-dot"></span> In progress</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="pga-overview-card">
          <div class="pga-overview-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
            Pending account approvals
          </div>
          <div class="pga-table-wrap">
            <table class="pga-table">
              <thead><tr><th>Name</th><th>Role</th><th>Submitted</th></tr></thead>
              <tbody>
                <tr><td>P. Mendoza</td><td style="color:var(--pga-ink-soft)">Supervisor</td><td style="color:var(--pga-ink-soft);font-size:12px">Jun 14</td></tr>
                <tr><td>L. Ramos</td><td style="color:var(--pga-ink-soft)">Supervisor</td><td style="color:var(--pga-ink-soft);font-size:12px">Jun 16</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <hr class="pga-divider">

      <div class="pga-overview-card-title" style="margin-bottom:12px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18.7 8 13 13.7l-3-3L4 16.4"/></svg>
        System-wide workstation status
      </div>
      <div class="pga-overview-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="pga-overview-card" style="text-align:center;padding:14px">
          <div class="pga-metric-val pga-red" style="font-size:26px">5</div>
          <div class="pga-metric-lbl" style="margin-top:4px">CRITICAL</div>
        </div>
        <div class="pga-overview-card" style="text-align:center;padding:14px">
          <div class="pga-metric-val pga-amber" style="font-size:26px">4</div>
          <div class="pga-metric-lbl" style="margin-top:4px">WARNING</div>
        </div>
        <div class="pga-overview-card" style="text-align:center;padding:14px">
          <div class="pga-metric-val pga-green" style="font-size:26px">5</div>
          <div class="pga-metric-lbl" style="margin-top:4px">CLEARED</div>
        </div>
        <div class="pga-overview-card" style="text-align:center;padding:14px">
          <div class="pga-metric-val" style="font-size:26px">4</div>
          <div class="pga-metric-lbl" style="margin-top:4px">UNASSIGNED</div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB: ALL TICKETS
    ════════════════════════════════════════ -->
    <div class="pga-pane" id="pgaTickets">
      <div class="pga-section-intro">Full visibility across every ticket submitted by every supervisor. Admin can view, monitor, and override sign-off if needed — but does not submit new tickets.</div>

      <div class="pga-filter-bar">
        <select>
          <option>All departments</option>
          <option>Administration</option>
          <option>Finance</option>
          <option>Information Technology</option>
          <option>Operations</option>
        </select>
        <select>
          <option>All statuses</option>
          <option>In progress</option>
          <option>Closed</option>
        </select>
        <select>
          <option>All priorities</option>
          <option>High</option>
          <option>Medium</option>
          <option>Low</option>
        </select>
      </div>

      <div class="pga-table-wrap">
        <table class="pga-table">
          <thead>
            <tr><th>Ticket no.</th><th>Submitted by</th><th>Dept.</th><th>Date</th><th>Workstations</th><th style="min-width:110px">Progress</th><th>Status</th><th></th></tr>
          </thead>
          <tbody>
            <tr>
              <td class="pga-mono">#2024-0147</td>
              <td>R. Villanueva</td>
              <td style="color:var(--pga-ink-soft)">Admin</td>
              <td style="color:var(--pga-ink-soft)">Jun 8</td>
              <td>
                <span class="pga-chip" style="border-color:var(--pga-red);color:var(--pga-red)">WS-101</span>
                <span class="pga-chip" style="color:var(--pga-ink-faint)">+13</span>
              </td>
              <td><div class="pga-pbar-text">3/14</div><div class="pga-pbar"><div class="pga-pbar-fill" style="width:21%"></div></div></td>
              <td><span class="pga-badge pga-badge-amber"><span class="pga-badge-dot"></span> In progress</span></td>
              <td><button class="pga-btn pga-btn-sm">View</button></td>
            </tr>
            <tr>
              <td class="pga-mono">#2024-0143</td>
              <td>P. Mendoza</td>
              <td style="color:var(--pga-ink-soft)">Finance</td>
              <td style="color:var(--pga-ink-soft)">Jun 5</td>
              <td>
                <span class="pga-chip" style="border-color:var(--pga-amber);color:var(--pga-amber-text)">WS-F01</span>
                <span class="pga-chip" style="color:var(--pga-ink-faint)">+3</span>
              </td>
              <td><div class="pga-pbar-text">2/4</div><div class="pga-pbar"><div class="pga-pbar-fill" style="width:50%"></div></div></td>
              <td><span class="pga-badge pga-badge-amber"><span class="pga-badge-dot"></span> In progress</span></td>
              <td><button class="pga-btn pga-btn-sm">View</button></td>
            </tr>
            <tr>
              <td class="pga-mono">#2024-0089</td>
              <td>R. Villanueva</td>
              <td style="color:var(--pga-ink-soft)">Admin</td>
              <td style="color:var(--pga-ink-soft)">Mar 12</td>
              <td>
                <span class="pga-chip" style="border-color:var(--pga-green);color:var(--pga-green)">WS-07</span>
                <span class="pga-chip" style="border-color:var(--pga-green);color:var(--pga-green)">WS-08</span>
              </td>
              <td><div class="pga-pbar-text">2/2</div><div class="pga-pbar"><div class="pga-pbar-fill" style="width:100%"></div></div></td>
              <td><span class="pga-badge pga-badge-green"><span class="pga-badge-dot"></span> Closed</span></td>
              <td><button class="pga-btn pga-btn-sm">View</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB: REASSIGN TERMINALS
    ════════════════════════════════════════ -->
    <div class="pga-pane" id="pgaReassign">
      <div class="pga-section-intro">Admin can transfer any workstation terminal to a different technician — regardless of who currently holds it. Use this to rebalance workload or cover for unavailable technicians.</div>

      <div class="pga-section-lbl">Ticket #2024-0147 — terminal assignments</div>
      <div class="pga-terminal-grid" id="pgaTerminalGrid">
        <!-- seeded by JS -->
      </div>

      <div id="pgaReassignModal" style="display:none;margin-top:20px">
        <hr class="pga-divider">
        <div class="pga-section-lbl">Reassign terminal</div>
        <div class="pga-reassign-banner">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
          <span>Reassigning <strong id="pgaReassignWs"></strong> — currently: <strong id="pgaReassignCurrent"></strong></span>
        </div>
        <div class="pga-row">
          <div class="pga-field">
            <label for="pgaReassignTo">Assign to technician</label>
            <select id="pgaReassignTo">
              <option value="">Select technician</option>
              <option>E. Macaraeg</option>
              <option>R. Bautista</option>
              <option>J. Pascual</option>
              <option>C. Navarro</option>
              <option>M. dela Rosa</option>
            </select>
          </div>
          <div class="pga-field">
            <label for="pgaReassignNote">Reason / note (optional)</label>
            <input type="text" id="pgaReassignNote" placeholder="e.g. rebalancing workload">
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:6px">
          <button class="pga-btn pga-btn-ghost pga-btn-sm" onclick="closeReassign()">Cancel</button>
          <button class="pga-btn pga-btn-sm pga-btn-transfer" onclick="confirmReassign()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Confirm reassignment
          </button>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
        TAB: DEPARTMENTS
    ════════════════════════════════════════ -->
    <div class="pga-pane" id="pgaDepartments">
      <div class="pga-section-intro">Each department has exactly one supervisor at a time. A supervisor, however, can be assigned to multiple departments simultaneously. Assigning a new supervisor to a department automatically replaces the previous one.</div>

      <div class="pga-row" style="margin-bottom:20px;justify-content:space-between;align-items:flex-end">
        <div style="display:flex;gap:10px;align-items:flex-end">
          <div class="pga-field" style="max-width:260px">
            <label for="pgaNewDeptName">Create new department</label>
            <input type="text" id="pgaNewDeptName" placeholder="e.g. Records Management">
          </div>
          <div class="pga-field" style="flex:0 0 auto">
            <button class="pga-btn pga-btn-primary" onclick="addDepartment()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
              Add department
            </button>
          </div>
        </div>

        <div style="position:relative;max-width:300px;width:100%">
          <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:var(--pga-ink-faint);pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="pgaDeptSearch" placeholder="Search department or supervisor…" style="width:100%;font-size:12.5px;padding:7px 10px 7px 30px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink)">
        </div>
      </div>

      

      <div class="pga-dept-grid" id="pgaDeptGrid">
        <!-- seeded by JS -->
      </div>

      <hr class="pga-divider">

      <div class="pga-section-lbl">Supervisor → departments overview</div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:10px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:8px">
          <label style="font-size:12px;color:var(--pga-ink-soft);white-space:nowrap">Rows</label>
          <select id="pgaDeptOvRowsPerPage" style="font-size:12.5px;padding:5px 22px 5px 9px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a9b3ae' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 7px center;">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
          </select>
        </div>
        <div style="position:relative">
          <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:var(--pga-ink-faint);pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="pgaDeptOvSearch" placeholder="Search supervisor…" style="font-size:12.5px;padding:6px 10px 6px 28px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink);width:220px">
        </div>
      </div>

      <div class="pga-table-wrap">
        <table class="pga-table" id="pgaSupervisorOverviewTable">
          <thead><tr><th>Supervisor</th><th>Departments held</th><th># Departments</th></tr></thead>
          <tbody id="pgaDeptOvTbody"><!-- seeded by JS --></tbody>
        </table>
      </div>

      <div id="pgaDeptOvPagination" style="display:flex;align-items:center;justify-content:space-between;margin-top:12px">
        <div id="pgaDeptOvPaginationInfo" style="font-size:12px;color:var(--pga-ink-soft)"></div>
        <div style="display:flex;gap:4px">
          <button class="pga-btn pga-btn-sm" id="pgaDeptOvPrev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M15 18l-6-6 6-6"/></svg> Prev
          </button>
          <div id="pgaDeptOvPageNums" style="display:flex;gap:4px"></div>
          <button class="pga-btn pga-btn-sm" id="pgaDeptOvNext">
            Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>


    <!-- ═══════════════════════════════════════
      TAB: ACCOUNT APPROVALS
    ════════════════════════════════════════ -->

   <div class="pga-pane" id="pgaApprovals">
      <div class="pga-section-intro">Pending supervisor registrations only. Technicians do not self-register — their accounts are created directly by the admin in the "Create technician" tab. Verify employee ID and details before approving.</div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:8px">
          <label style="font-size:12px;color:var(--pga-ink-soft);white-space:nowrap">Rows</label>
          <select id="pgaApprovalsRowsPerPage" style="font-size:12.5px;padding:5px 22px 5px 9px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a9b3ae' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 7px center;">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
          </select>
        </div>
        <div style="position:relative">
          <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:var(--pga-ink-faint);pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="pgaApprovalsSearch" placeholder="Search name, email, employee ID…" style="font-size:12.5px;padding:6px 10px 6px 28px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink);width:260px">
        </div>
      </div>

      <div class="pga-table-wrap" id="pgaApprovalsContainer" style="padding-left:15px;padding-right:20px;padding-top:10px;">
        <!-- injected by JS -->
      </div>

      <div id="pgaApprovalsPagination" style="display:flex;align-items:center;justify-content:space-between;margin-top:14px">
        <div id="pgaApprovalsPaginationInfo" style="font-size:12px;color:var(--pga-ink-soft)"></div>
        <div style="display:flex;gap:4px">
          <button class="pga-btn pga-btn-sm" id="pgaApprovalsPrev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M15 18l-6-6 6-6"/></svg> Prev
          </button>
          <div id="pgaApprovalsPageNums" style="display:flex;gap:4px"></div>
          <button class="pga-btn pga-btn-sm" id="pgaApprovalsNext">
            Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB: CREATE TECHNICIAN
    ════════════════════════════════════════ -->
    <div class="pga-pane" id="pgaCreateTech">
      <div class="pga-section-intro">Technician accounts are created directly by the admin — there is no public sign-up or approval queue for this role. The account is active immediately upon creation.</div>

      <div class="pga-row">
        <div class="pga-field">
          <label for="pgaTechFname">First name</label>
          <input type="text" id="pgaTechFname" placeholder="e.g. Elena">
          <span class="pga-field-error" id="pgaTechFnameError">First name is required.</span>
        </div>
        <div class="pga-field">
          <label for="pgaTechLname">Last name</label>
          <input type="text" id="pgaTechLname" placeholder="e.g. Macaraeg">
          <span class="pga-field-error" id="pgaTechLnameError">Last name is required.</span>
        </div>
      </div>

      <div class="pga-row">
        <div class="pga-field">
          <label for="pgaTechEmail">Work email address</label>
          <input type="email" id="pgaTechEmail" placeholder="you@company.com">
        </div>
        <div class="pga-field">
          <label for="pgaTechPhone">Mobile number</label>
          <input type="tel" id="pgaTechPhone" placeholder="+63 9XX XXX XXXX">
        </div>
      </div>

      <div class="pga-row">
        <div class="pga-field">
          <label for="pgaTechEmpid">Employee ID</label>
          <input type="text" id="pgaTechEmpid" placeholder="e.g. EMP-2024-0042">
          <span class="pga-field-error" id="pgaTechEmpidError">Employee ID is required.</span>
        </div>
        <div class="pga-field">
          <label for="pgaTechJobtitle">Job title</label>
          <input type="text" id="pgaTechJobtitle" value="Technician, IT Support" placeholder="e.g. Technician, IT Support">
          <span class="pga-field-error" id="pgaTechJobtitleError">Job title is required.</span>
        </div>
      </div>

      <hr class="pga-divider">

      <div class="pga-section-lbl">Account credentials</div>
      <div class="pga-row">
        <div class="pga-field">
          <label for="pgaTechUsername">Username</label>
          <input type="text" readonly id="pgaTechUsername" placeholder="The username is this account's employee ID.">
          <span class="pga-field-error" id="pgaTechUsernameError">Username is required.</span>
        </div>
        <div class="pga-field">
          <label for="pgaTechPassword">Temporary password</label>
          <div style="display:flex;gap:8px">
            <input type="text" id="pgaTechPassword" placeholder="Auto-generated or set manually" style="flex:1">
            <button class="pga-btn pga-btn-sm" type="button" onclick="generateTechPassword()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
              Generate
            </button>
          </div>
          <span class="pga-field-error" id="pgaTechPasswordError">Password is required.</span>
        </div>
      </div>

      <div style="background:var(--pga-blue-bg);border:1px solid #2a4060;border-radius:8px;padding:10px 13px;font-size:12.5px;color:var(--pga-blue);line-height:1.5;margin-bottom:6px;display:flex;gap:10px;align-items:flex-start">
        <svg style="width:15px;height:15px;flex-shrink:0;margin-top:1px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>The technician will be required to change this temporary password on first login.</span>
      </div>

      <div class="pga-form-footer-create" style="display:flex;justify-content:flex-end;margin-top:16px">
        <button class="pga-btn pga-btn-primary" id="pgaCreateTechBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
          Create technician account
        </button>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB: MANAGE ACCOUNTS
    ════════════════════════════════════════ -->
    <div class="pga-pane" id="pgaManageAccounts">
      <div class="pga-section-intro">View every supervisor and technician account in the system. Edit their details, reset their password, or deactivate/delete an account.</div>

      <div class="pga-filter-bar">
        <select id="pgaAccFilterRole">
          <option value="all">All roles</option>
          <option value="supervisor">Supervisors only</option>
          <option value="technician">Technicians only</option>
        </select>
        <select id="pgaAccFilterStatus">
          <option value="all">All statuses</option>
          <option value="active">Active only</option>
          <option value="deactivated">Deactivated only</option>
        </select>
        <div style="display:flex;align-items:center;gap:8px">
          <label style="font-size:12px;color:var(--pga-ink-soft);white-space:nowrap">Rows</label>
          <select id="pgaAccRowsPerPage" style="font-size:12.5px;padding:5px 22px 5px 9px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink);appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a9b3ae' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 7px center;">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
          </select>
        </div>
        <div style="position:relative;margin-left:auto">
          <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:13px;height:13px;color:var(--pga-ink-faint);pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="pgaAccSearch" placeholder="Search name, email, employee ID…" style="font-size:12.5px;padding:6px 10px 6px 28px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-panel-2);color:var(--pga-ink);width:260px">
        </div>
      </div>

      <div class="pga-table-wrap">
        <table class="pga-table" id="pgaAccountsTable">
          <thead>
            <tr><th>Name</th><th>Employee ID</th><th>Email</th><th>Status</th><th style="min-width:230px">Actions</th></tr>
          </thead>
          <tbody><!-- seeded by JS --></tbody>
        </table>
      </div>
      <div id="pgaAccountsPagination" style="display:flex;align-items:center;justify-content:space-between;margin-top:12px">
        <div id="pgaAccountsPaginationInfo" style="font-size:12px;color:var(--pga-ink-soft)"></div>
        <div style="display:flex;gap:4px">
          <button class="pga-btn pga-btn-sm" id="pgaAccountsPrev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M15 18l-6-6 6-6"/></svg> Prev
          </button>
          <div id="pgaAccountsPageNums" style="display:flex;gap:4px"></div>
          <button class="pga-btn pga-btn-sm" id="pgaAccountsNext">
            Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>
      </div>

      <!-- EDIT ACCOUNT PANEL (hidden by default) -->
      <div id="pgaEditAccountPanel" style="display:none;margin-top:20px">
        <hr class="pga-divider">
        <div class="pga-section-lbl">Edit account — <span id="pgaEditAccName"></span></div>
        <div class="pga-row">
          <div class="pga-field">
            <label for="pgaEditFname">First name</label>
            <input type="text" id="pgaEditFname">
          </div>
          <div class="pga-field">
            <label for="pgaEditLname">Last name</label>
            <input type="text" id="pgaEditLname">
          </div>
        </div>
        <div class="pga-row">
          <div class="pga-field">
            <label for="pgaEditEmail">Email</label>
            <input type="email" id="pgaEditEmail">
          </div>
          <div class="pga-field">
            <label for="pgaEditPhone">Phone</label>
            <input type="tel" id="pgaEditPhone">
          </div>
        </div>
        <div class="pga-row">
          <div class="pga-field">
            <label for="pgaEditEmpid">Employee ID</label>
            <input type="text" id="pgaEditEmpid">
          </div>
          <div class="pga-field">
            <label for="pgaEditJobtitle">Job title</label>
            <input type="text" id="pgaEditJobtitle">
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:6px">
          <button class="pga-btn pga-btn-ghost pga-btn-sm" onclick="closeEditAccount()">Cancel</button>
          <button class="pga-btn pga-btn-sm pga-btn-primary" onclick="saveEditAccount()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Save changes
          </button>
        </div>
      </div>

      <!-- RESET PASSWORD PANEL (hidden by default) -->
      <div id="pgaResetPwPanel" style="display:none;margin-top:20px">
        <hr class="pga-divider">
        <div class="pga-section-lbl">Reset password — <span id="pgaResetPwName"></span></div>
        <div style="background:var(--pga-amber-bg);border:1px solid #5a4230;border-radius:8px;padding:10px 13px;font-size:12.5px;color:var(--pga-amber-text);line-height:1.5;margin-bottom:14px;display:flex;gap:10px;align-items:flex-start">
          <svg style="width:15px;height:15px;flex-shrink:0;margin-top:1px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <span>This generates a new temporary password. The user will be required to change it on next login.</span>
        </div>
        <div class="pga-row">
          <div class="pga-field">
            <label for="pgaResetPwValue">New temporary password</label>
            <div style="display:flex;gap:8px">
              <input type="text" id="pgaResetPwValue" placeholder="Click generate" style="flex:1">
              <button class="pga-btn pga-btn-sm" type="button" onclick="generateResetPassword()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                Generate
              </button>
            </div>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:6px">
          <button class="pga-btn pga-btn-ghost pga-btn-sm" onclick="closeResetPassword()">Cancel</button>
          <button class="pga-btn pga-btn-sm pga-btn-primary" onclick="confirmResetPassword()">
            Confirm reset
          </button>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB: REPORTS
    ════════════════════════════════════════ -->
    <div class="pga-pane" id="pgaReports" style="color:var(--pga-ink-faint)">
      <div class="pga-section-intro">System-wide reports across every department, ticket, and technician.</div>

      <div class="pga-report-group">
        <div class="pga-section-lbl">System-wide</div>
        <div class="pga-report-grid">
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">ALL DEPARTMENTS</div><div class="pga-rcard-val">Incident frequency</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">ALL DEPARTMENTS</div><div class="pga-rcard-val">Workstation damage history</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">ALL DEPARTMENTS</div><div class="pga-rcard-val">Technician performance</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">ALL DEPARTMENTS</div><div class="pga-rcard-val">Supervisor activity log</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
        </div>
      </div>

      <div class="pga-report-group">
        <div class="pga-section-lbl">Administrative</div>
        <div class="pga-report-grid">
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">ACCOUNTS</div><div class="pga-rcard-val">Account approval history</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">ACCOUNTS</div><div class="pga-rcard-val">Department assignment log</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">TERMINALS</div><div class="pga-rcard-val">Reassignment history</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
          <div class="pga-rcard"><div><div class="pga-rcard-lbl">PARTS</div><div class="pga-rcard-val">Parts requests — all tickets</div></div><button style="color:var(--pga-ink-faint)" class="pga-btn pga-btn-sm">Generate</button></div>
        </div>
      </div>
    </div>

  </div><!-- end pga-card -->
</div><!-- end pga-shell -->
<script src="../../assets/js/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/js/sole.js"></script>
<script src="../../assets/js/sole.swal.js"></script>
<script src="../../assets/js/powerguard/splash.js"></script>
<script src="../../assets/js/powerguard/administrator.js"></script>
</body>
</html>