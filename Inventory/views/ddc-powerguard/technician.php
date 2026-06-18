<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PowerGuard — Technician</title>
<link rel="stylesheet" href="../../assets/css/powerguard.css">
</head>
<body class="pgt-body">

<div class="pgt-shell">

  <!-- TOP BAR -->
  <div class="pgt-topbar">
    <div class="pgt-topbar-left">
      <div class="pgt-avatar">EM</div>
      <div>
        <div class="pgt-name">E. Macaraeg</div>
        <div class="pgt-role-label">Technician · IT Department</div>
      </div>
    </div>
    <div class="pgt-topbar-right">
      <span class="pgt-badge pgt-badge-blue"><span class="pgt-badge-dot"></span> 3 claimed</span>
      <span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> 2 pending sign-off</span>
      <button class="pgt-iconbtn" aria-label="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <button class="pgt-iconbtn" aria-label="More options">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
      </button>
    </div>
  </div>

  <!-- METRICS -->
  <div class="pgt-metrics">
    <div class="pgt-metric">
      <div class="pgt-metric-lbl">MY CLAIMED TERMINALS</div>
      <div class="pgt-metric-val pgt-blue">3</div>
    </div>
    <div class="pgt-metric">
      <div class="pgt-metric-lbl">COMPLETED (ALL TIME)</div>
      <div class="pgt-metric-val pgt-green">12</div>
    </div>
    <div class="pgt-metric">
      <div class="pgt-metric-lbl">AWAITING SIGN-OFF</div>
      <div class="pgt-metric-val pgt-amber">2</div>
    </div>
    <div class="pgt-metric">
      <div class="pgt-metric-lbl">ACTIVE INCIDENTS</div>
      <div class="pgt-metric-val">2</div>
    </div>
  </div>

  <!-- MAIN CARD -->
  <div class="pgt-card">
    <div class="pgt-tabbar">
      <div class="pgt-tab pgt-tab-active" data-pane="pgtAllTickets">All tickets</div>
      <div class="pgt-tab" data-pane="pgtMyWork">My work</div>
      <div class="pgt-tab" data-pane="pgtAssessment">Assessment form</div>
      <div class="pgt-tab" data-pane="pgtReports">Reports</div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 1 — ALL TICKETS
    ════════════════════════════════════════ -->
    <div class="pgt-pane pgt-pane-active" id="pgtAllTickets">
      <p style="font-size:13px;color:var(--pgt-ink-soft);margin-bottom:16px;">
        All submitted incident tickets — tap any unclaimed terminal to self-assign it.
      </p>

      <!-- TICKET #2024-0147 -->
      <div class="pgt-ticket-block">
        <div class="pgt-ticket-head">
          <div class="pgt-ticket-head-left">
            <span class="pgt-mono" style="font-size:13px">#2024-0147</span>
            <span class="pgt-badge pgt-badge-red"><span class="pgt-badge-dot"></span> High priority</span>
            <span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> In progress</span>
            <button class="pgt-details-btn" onclick="toggleTicketDetails('pgtDetails0147', this)">
              View details
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pgt-ticket-meta">Jun 8 · R. Villanueva · 2F Admin Wing · Voltage spike · 14 terminals</div>
        </div>
        <div class="pgt-details-panel" id="pgtDetails0147">
          <div class="pgt-details-grid">
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Fluctuation type</div>
              <div class="pgt-details-val">Voltage spike</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Duration</div>
              <div class="pgt-details-val">12 minutes</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Incident date &amp; time</div>
              <div class="pgt-details-val">Jun 8, 2024 · 2:32 PM</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Area / floor</div>
              <div class="pgt-details-val">2F Admin Wing</div>
            </div>
          </div>
          <div class="pgt-details-desc">
            <div class="pgt-details-desc-lbl">Description</div>
            Sudden voltage spike at 2:32 PM caused circuit breakers to trip in admin wing. Multiple UPS units reported burnt smell.
          </div>
        </div>
        <div class="pgt-ticket-body">
          <div class="pgt-terminal-grid" id="pgtTerminalGrid1">
            <!-- seeded by JS -->
          </div>
          <div class="pgt-legend">
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-blue)"></div> Mine</div>
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-ink-faint)"></div> Unclaimed — tap to claim</div>
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-green)"></div> Resolved</div>
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-ink-faint);opacity:.4"></div> Taken by another tech</div>
          </div>
        </div>
      </div>

      <!-- TICKET #2024-0143 -->
      <div class="pgt-ticket-block">
        <div class="pgt-ticket-head">
          <div class="pgt-ticket-head-left">
            <span class="pgt-mono" style="font-size:13px">#2024-0143</span>
            <span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> Medium priority</span>
            <span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> In progress</span>
            <button class="pgt-details-btn" onclick="toggleTicketDetails('pgtDetails0143', this)">
              View details
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pgt-ticket-meta">Jun 5 · P. Mendoza · 4F Finance Wing · Brownout · 4 terminals</div>
        </div>
        <div class="pgt-details-panel" id="pgtDetails0143">
          <div class="pgt-details-grid">
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Fluctuation type</div>
              <div class="pgt-details-val">Brownout</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Duration</div>
              <div class="pgt-details-val">35 minutes</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Incident date &amp; time</div>
              <div class="pgt-details-val">Jun 5, 2024 · 9:14 AM</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Area / floor</div>
              <div class="pgt-details-val">4F Finance Wing</div>
            </div>
          </div>
          <div class="pgt-details-desc">
            <div class="pgt-details-desc-lbl">Description</div>
            Voltage dropped below normal operating range for an extended period, causing intermittent shutdowns on several finance terminals. No visible burn damage reported, but UPS units may have deep-discharged.
          </div>
        </div>
        <div class="pgt-ticket-body">
          <div class="pgt-terminal-grid" id="pgtTerminalGrid2">
            <!-- seeded by JS -->
          </div>
        </div>
      </div>

      <!-- TICKET #2024-0089 CLOSED -->
      <div class="pgt-ticket-block pgt-closed-ticket">
        <div class="pgt-ticket-head">
          <div class="pgt-ticket-head-left">
            <span class="pgt-mono" style="font-size:13px">#2024-0089</span>
            <span class="pgt-badge pgt-badge-green"><span class="pgt-badge-dot"></span> Closed</span>
            <button class="pgt-details-btn" onclick="toggleTicketDetails('pgtDetails0089', this)">
              View details
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pgt-ticket-meta">Mar 12 · R. Villanueva · 3F Admin Wing · Power surge · 2 terminals</div>
        </div>
        <div class="pgt-details-panel" id="pgtDetails0089">
          <div class="pgt-details-grid">
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Fluctuation type</div>
              <div class="pgt-details-val">Power surge</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Duration</div>
              <div class="pgt-details-val">4 minutes</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Incident date &amp; time</div>
              <div class="pgt-details-val">Mar 12, 2024 · 10:02 AM</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Area / floor</div>
              <div class="pgt-details-val">3F Admin Wing</div>
            </div>
          </div>
          <div class="pgt-details-desc">
            <div class="pgt-details-desc-lbl">Description</div>
            Brief power surge following a grid switchover. Two workstations were affected; both were assessed, repaired, and restored to service the same day.
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 2 — MY WORK
    ════════════════════════════════════════ -->
    <div class="pgt-pane" id="pgtMyWork">

      <div class="pgt-my-work-section">
        <div class="pgt-section-lbl">Claimed terminals — in progress</div>
        <div class="pgt-table-wrap">
          <table class="pgt-table">
            <thead>
              <tr>
                <th>Terminal</th>
                <th>Ticket</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Last action</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="pgt-mono">WS-101</td>
                <td style="color:var(--pgt-ink-soft)">#2024-0147</td>
                <td><span class="pgt-badge pgt-badge-red" style="font-size:11px"><span class="pgt-badge-dot"></span> High</span></td>
                <td><span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> Parts pending</span></td>
                <td style="color:var(--pgt-ink-soft);font-size:12px">Assessment submitted Jun 8</td>
                <td>
                  <button class="pgt-btn pgt-btn-sm pgt-btn-transfer" onclick="openTransfer('WS-101','#2024-0147')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    Transfer
                  </button>
                </td>
              </tr>
              <tr>
                <td class="pgt-mono">WS-105</td>
                <td style="color:var(--pgt-ink-soft)">#2024-0147</td>
                <td><span class="pgt-badge pgt-badge-red" style="font-size:11px"><span class="pgt-badge-dot"></span> High</span></td>
                <td><span class="pgt-badge pgt-badge-blue"><span class="pgt-badge-dot"></span> In progress</span></td>
                <td style="color:var(--pgt-ink-soft);font-size:12px">Claimed Jun 8 — not yet submitted</td>
                <td>
                  <button class="pgt-btn pgt-btn-sm pgt-btn-transfer" onclick="openTransfer('WS-105','#2024-0147')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    Transfer
                  </button>
                </td>
              </tr>
              <tr>
                <td class="pgt-mono">WS-110</td>
                <td style="color:var(--pgt-ink-soft)">#2024-0147</td>
                <td><span class="pgt-badge pgt-badge-red" style="font-size:11px"><span class="pgt-badge-dot"></span> High</span></td>
                <td><span class="pgt-badge pgt-badge-gray"><span class="pgt-badge-dot"></span> Not started</span></td>
                <td style="color:var(--pgt-ink-soft);font-size:12px">Claimed Jun 8</td>
                <td>
                  <button class="pgt-btn pgt-btn-sm pgt-btn-transfer" onclick="openTransfer('WS-110','#2024-0147')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    Transfer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="pgt-my-work-section">
        <div class="pgt-section-lbl">Completed assessments</div>
        <div class="pgt-table-wrap">
          <table class="pgt-table">
            <thead>
              <tr>
                <th>Terminal</th>
                <th>Ticket</th>
                <th>Result</th>
                <th>Sign-off</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="pgt-mono">WS-104</td>
                <td style="color:var(--pgt-ink-soft)">#2024-0147</td>
                <td><span class="pgt-badge pgt-badge-green"><span class="pgt-badge-dot"></span> Cleared</span></td>
                <td>
                  <span class="pgt-badge pgt-badge-green">
                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    Signed off
                  </span>
                </td>
                <td style="color:var(--pgt-ink-soft);font-size:12px">Jun 8, 3:28 PM</td>
              </tr>
              <tr>
                <td class="pgt-mono">WS-109</td>
                <td style="color:var(--pgt-ink-soft)">#2024-0147</td>
                <td><span class="pgt-badge pgt-badge-green"><span class="pgt-badge-dot"></span> Cleared</span></td>
                <td>
                  <span class="pgt-badge pgt-badge-green">
                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    Signed off
                  </span>
                </td>
                <td style="color:var(--pgt-ink-soft);font-size:12px">Jun 8, 3:45 PM</td>
              </tr>
              <tr>
                <td class="pgt-mono">WS-07</td>
                <td style="color:var(--pgt-ink-soft)">#2024-0089</td>
                <td><span class="pgt-badge pgt-badge-green"><span class="pgt-badge-dot"></span> Restored</span></td>
                <td>
                  <span class="pgt-badge pgt-badge-green">
                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    Signed off
                  </span>
                </td>
                <td style="color:var(--pgt-ink-soft);font-size:12px">Mar 12, 10:14 AM</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TRANSFER MODAL (hidden) -->
      <div id="pgtTransferModal" style="display:none;margin-top:0">
        <hr class="pgt-divider">
        <div class="pgt-section-lbl">Transfer terminal</div>
        <div class="pgt-transfer-banner">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <span>Transferring <strong id="pgtTransferWs"></strong> from ticket <strong id="pgtTransferTicket"></strong>. Once transferred, you will no longer be responsible for this terminal.</span>
        </div>
        <div class="pgt-row">
          <div class="pgt-field">
            <label for="pgtTransferTo">Transfer to technician</label>
            <select id="pgtTransferTo">
              <option value="">Select technician</option>
              <option>R. Bautista</option>
              <option>J. Pascual</option>
              <option>C. Navarro</option>
              <option>M. dela Rosa</option>
            </select>
          </div>
          <div class="pgt-field">
            <label for="pgtTransferReason">Reason for transfer</label>
            <select id="pgtTransferReason">
              <option value="">Select reason</option>
              <option>Workload — too many terminals</option>
              <option>Specialization — outside my expertise</option>
              <option>Unavailability — leave / schedule conflict</option>
              <option>Other</option>
            </select>
          </div>
        </div>
        <div class="pgt-form-footer">
          <button class="pgt-btn pgt-btn-ghost pgt-btn-sm" onclick="closeTransfer()">Cancel</button>
          <button class="pgt-btn pgt-btn-sm pgt-btn-transfer" onclick="confirmTransfer()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/></svg>
            Confirm transfer
          </button>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 3 — ASSESSMENT FORM
    ════════════════════════════════════════ -->
    <div class="pgt-pane" id="pgtAssessment">
      <div class="pgt-row" style="margin-bottom:16px">
        <div class="pgt-field">
          <label for="pgtAssessWs">Select my terminal</label>
          <select id="pgtAssessWs">
            <option value="">Choose a terminal…</option>
            <option value="WS-101">WS-101 — #2024-0147 (parts pending)</option>
            <option value="WS-105">WS-105 — #2024-0147 (not started)</option>
            <option value="WS-110">WS-110 — #2024-0147 (not started)</option>
          </select>
        </div>
        <div class="pgt-field">
          <label for="pgtAssessDatetime">Assessment date &amp; time</label>
          <input type="datetime-local" id="pgtAssessDatetime">
        </div>
      </div>

      <!-- WORKSTATION DAMAGE DECLARATION (read-only, from supervisor's ticket) -->
      <div class="pgt-section-lbl">Workstation damage declaration</div>
      <div class="pgt-decl-block" id="pgtDeclBlock">
        <div class="pgt-decl-empty">Select a terminal above to view the supervisor's original damage declaration.</div>
      </div>

      <div class="pgt-comp-grid">
                <!-- UPS -->
        <div class="pgt-comp-card">
          <div class="pgt-comp-title">UPS</div>
          <div class="pgt-comp-field">
            <label>Condition</label>
            <select id="pgtUpsCondition" onchange="applyCompClass(this)">
              <option value="" selected disabled>Select…</option>
              <option class="pgt-ok" value="Functional">Functional</option>
              <option class="pgt-damaged" value="Damaged — replace unit">Damaged — replace unit</option>
              <option class="pgt-damaged" value="Damaged — replace battery">Damaged — replace battery</option>
            </select>
          </div>
        </div>

        <!-- SYSTEM UNIT -->
        <div class="pgt-comp-card">
          <div class="pgt-comp-title">System unit</div>
          <div class="pgt-comp-field">
            <label>Condition</label>
            <select id="pgtSuCondition" onchange="applyCompClass(this)">
              <option value="" selected disabled>Select…</option>
              <option value="Functional">Functional</option>
              <option value="Damaged — replace PSU">Damaged — replace PSU</option>
              <option value="Damaged — replace storage">Damaged — replace storage</option>
              <option value="Damaged — replace memory">Damaged — replace memory</option>
              <option value="Damaged — replace motherboard">Damaged — replace motherboard</option>
              <option value="Damaged — replace unit">Damaged — replace unit</option>
            </select>
          </div>
        </div>

        <!-- MONITOR -->
        <div class="pgt-comp-card">
          <div class="pgt-comp-title">Monitor</div>
          <div class="pgt-comp-field">
            <label>Condition</label>
            <select id="pgtMonCondition" onchange="applyCompClass(this)">
              <option value="" selected disabled>Select…</option>
              <option value="Functional">Functional</option>
              <option value="Damaged — needs replacement">Damaged — needs replacement</option>
            </select>
          </div>
        </div>
      </div>

      <div class="pgt-row">
        <div class="pgt-field">
          <label for="pgtFindings">Technical findings</label>
          <textarea id="pgtFindings" placeholder="Describe what you found — component condition, visible damage, test results, POST outcome…"></textarea>
        </div>
      </div>

      <div class="pgt-row">
        <div class="pgt-field">
          <label for="pgtParts">Parts needed</label>
          <input type="text" id="pgtParts" placeholder="e.g. APC Back-UPS 650VA x1">
        </div>
        <div class="pgt-field">
          <label for="pgtEscalate">Escalate to</label>
          <select id="pgtEscalate">
            <option value="">None — no escalation needed</option>
            <option>Supervisor</option>
            <option>IT Manager</option>
            <option>Procurement</option>
          </select>
        </div>
      </div>

      <!-- Photo attachments -->
      <!-- <div class="pgt-section-lbl" style="margin-top:4px">Photo attachments</div>
      <div class="pgt-photo-row">
        <div class="pgt-photo-thumb">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <div class="pgt-photo-thumb">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <button class="pgt-btn pgt-btn-sm" style="height:48px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
          Add photo
        </button>
      </div> -->

      <div class="pgt-form-footer">
        <button class="pgt-btn pgt-btn-ghost pgt-btn-sm" id="pgtSaveDraftBtn">Save draft</button>
        <button class="pgt-btn pgt-btn-primary pgt-btn-sm" id="pgtSubmitAssessBtn">
          Submit assessment
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
        </button>
      </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 4 — REPORTS
    ════════════════════════════════════════ -->
    <div class="pgt-pane" id="pgtReports">
      <p style="font-size:13px;color:var(--pgt-ink-soft);margin-bottom:20px;">
        Technicians can view and generate reports across all incidents and projects.
      </p>

      <div class="pgt-report-group">
        <div class="pgt-section-lbl">Current ticket — #2024-0147</div>
        <div class="pgt-report-grid">
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">ALL TECHNICIANS</div><div class="pgt-rcard-val">Incident summary</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">ALL TECHNICIANS</div><div class="pgt-rcard-val">All technician logs</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
        </div>
      </div>

      <div class="pgt-report-group">
        <div class="pgt-section-lbl">My work history</div>
        <div class="pgt-report-grid">
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">PERSONAL</div><div class="pgt-rcard-val">My assessment log</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">PERSONAL</div><div class="pgt-rcard-val">My parts requests</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
        </div>
      </div>

      <div class="pgt-report-group">
        <div class="pgt-section-lbl">All projects — cross-incident</div>
        <div class="pgt-report-grid">
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">ALL INCIDENTS</div><div class="pgt-rcard-val">Cross-incident status</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">ALL INCIDENTS</div><div class="pgt-rcard-val">Parts requests history</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">ALL INCIDENTS</div><div class="pgt-rcard-val">Workstation damage history</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
          <div class="pgt-rcard">
            <div><div class="pgt-rcard-lbl">ALL INCIDENTS</div><div class="pgt-rcard-val">Incident frequency</div></div>
            <button class="pgt-btn pgt-btn-sm">View</button>
          </div>
        </div>
      </div>
    </div>

  </div><!-- end pgt-card -->
</div><!-- end pgt-shell -->
<script src="../../assets/js/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/js/sole.js"></script>
<script src="../../assets/js/sole.swal.js"></script>
<script src="../../assets/js/powerguard/splash.js"></script>
<script src="../../assets/js/powerguard/technician.js"></script>
</body>
</html>