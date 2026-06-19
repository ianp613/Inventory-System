    
(function(){

  /* ---------- TAB SWITCHING ---------- */
  const tabs = document.querySelectorAll('.pgs-tab');
  const panes = document.querySelectorAll('.pgs-pane');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('pgs-tab-active'));
      panes.forEach(p => p.classList.remove('pgs-pane-active'));
      tab.classList.add('pgs-tab-active');
      document.getElementById(tab.dataset.pane).classList.add('pgs-pane-active');
      localStorage.setItem("pgs-active",tab.getAttribute("data-pane"))
    });
  });

  if(localStorage.getItem("pgs-active") !== null){
    tabs.forEach(t => t.classList.remove('pgs-tab-active'));
    panes.forEach(p => p.classList.remove('pgs-pane-active'));
     tabs.forEach(tab => {
      if(tab.getAttribute("data-pane") == localStorage.getItem("pgs-active")){
        tab.classList.add('pgs-tab-active');
        document.getElementById(tab.dataset.pane).classList.add('pgs-pane-active');
      }
    });
  }
  

  /* ---------- WORKSTATION TABLE ---------- */
  const tbody = document.getElementById('pgsWsTbody');
  const addBtn = document.getElementById('pgsAddWsBtn');

  const statusOptions = ['Damaged','Suspected','OK'];

  function statusSelectHTML(selected){
    return statusOptions.map(opt =>
      `<option value="${opt}" ${opt===selected ? 'selected' : ''}>${opt}</option>`
    ).join('');
  }

  function applyStatusClass(select){
    statusOptions.forEach(opt => select.classList.remove('pgs-status-'+opt));
    select.classList.add('pgs-status-'+select.value);
  }

  function addWsRow(data){
    data = data || {};
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <input type="text" class="pgs-ws-num-input" value="" style="font-family:'IBM Plex Mono', monospace; font-weight:700; font-size:12.5px; width:80px">
      </td>
      <td><input type="text" class="pgs-ws-user-input" placeholder="Assigned user" value="${data.user || ''}"></td>
      <td><select class="pgs-ws-ups">${statusSelectHTML(data.ups || 'OK')}</select></td>
      <td><select class="pgs-ws-su">${statusSelectHTML(data.su || 'OK')}</select></td>
      <td><select class="pgs-ws-monitor">${statusSelectHTML(data.monitor || 'OK')}</select></td>
      <td><input type="text" class="pgs-ws-notes-input" placeholder="Notes" value="${data.notes || ''}"></td>
      <td>
        <button type="button" class="pgs-del-btn" aria-label="Remove workstation">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
        </button>
      </td>
    `;

    tbody.appendChild(tr);

    // wire status color classes
    ['.pgs-ws-ups','.pgs-ws-su','.pgs-ws-monitor'].forEach(sel => {
      const el = tr.querySelector(sel);
      applyStatusClass(el);
      el.addEventListener('change', () => applyStatusClass(el));
    });

    // wire delete
    tr.querySelector('.pgs-del-btn').addEventListener('click', () => {
      tr.remove();
    });
  }

  addBtn.addEventListener('click', () => addWsRow());

  // seed with the two example rows from the screenshot
  addWsRow({ wsNumber:'', user:'', ups:'OK', su:'OK', monitor:'OK', notes:'' });
  addWsRow({ wsNumber:'', user:'', ups:'OK', su:'OK', monitor:'OK', notes:'' });
  addWsRow({ wsNumber:'', user:'', ups:'OK', su:'OK', monitor:'OK', notes:'' });
  addWsRow({ wsNumber:'', user:'', ups:'OK', su:'OK', monitor:'OK', notes:'' });
  addWsRow({ wsNumber:'', user:'', ups:'OK', su:'OK', monitor:'OK', notes:'' });

  /* ---------- SUBMIT TICKET ---------- */
  document.getElementById('pgsSubmitTicketBtn').addEventListener('click', () => {

    const datetimeInput = document.getElementById('pgsDatetime');
    const datetimeError = document.getElementById('pgsDatetimeError');
    const durationInput = document.getElementById('pgsDuration');
    const durationError = document.getElementById('pgsDurationError');
    const descriptionInput = document.getElementById('pgsDescription');
    const descriptionError = document.getElementById('pgsDescriptionError');

    // reset previous error state
    datetimeInput.classList.remove('pgs-has-error');
    datetimeError.classList.remove('pgs-show');
    durationInput.classList.remove('pgs-has-error');
    durationError.classList.remove('pgs-show');
    descriptionInput.classList.remove('pgs-has-error');
    descriptionError.classList.remove('pgs-show');

    let hasError = false;

    // ── VALIDATION: Incident date & time ──
    const datetimeVal = datetimeInput.value;
    if(!datetimeVal){
      datetimeInput.classList.add('pgs-has-error');
      datetimeError.textContent = 'Incident date & time is required.';
      datetimeError.classList.add('pgs-show');
      hasError = true;
    } else if(new Date(datetimeVal) > new Date()){
      datetimeInput.classList.add('pgs-has-error');
      datetimeError.textContent = 'Incident date & time cannot be in the future.';
      datetimeError.classList.add('pgs-show');
      hasError = true;
    }

    // ── VALIDATION: Duration (minutes) ──
    const durationVal = durationInput.value;
    if(!durationVal || isNaN(durationVal) || Number(durationVal) <= 0){
      durationInput.classList.add('pgs-has-error');
      durationError.textContent = 'Enter a valid duration in minutes (greater than 0).';
      durationError.classList.add('pgs-show');
      hasError = true;
    }

    // ── VALIDATION: Description ──
    if(!descriptionInput.value.trim()){
      descriptionInput.classList.add('pgs-has-error');
      descriptionError.textContent = 'Description is required.';
      descriptionError.classList.add('pgs-show');
      hasError = true;
    }

    const workstations = [];
    tbody.querySelectorAll('tr').forEach(tr => {
        if(tr.querySelector('.pgs-ws-num-input').value && tr.querySelector('.pgs-ws-user-input').value){
            workstations.push({
                ws_number: tr.querySelector('.pgs-ws-num-input').value,
                assigned_user: tr.querySelector('.pgs-ws-user-input').value,
                ups_status: tr.querySelector('.pgs-ws-ups').value,
                system_unit_status: tr.querySelector('.pgs-ws-su').value,
                monitor_status: tr.querySelector('.pgs-ws-monitor').value,
                notes: tr.querySelector('.pgs-ws-notes-input').value
            });    
        }
    });

    if(!workstations.length){
      ss.toast("Invalid Ticket","warning","Workstation damage declaration field cannot be empty.",null,"#082b49")
      hasError = true;
    }

    if(hasError) return;

    sole.post("../../controllers/powerguard/supervisor/submit_ticket.php",{
      sup_id: localStorage.getItem("userid_sup"),
      incident_datetime: document.getElementById('pgsDatetime').value,
      fluctuation_type: document.getElementById('pgsFlucType').value,
      priority: document.getElementById('pgsPriority').value,
      area: document.getElementById('pgsArea').value,
      duration_minutes: document.getElementById('pgsDuration').value,
      description: document.getElementById('pgsDescription').value,
      workstations: workstations
    }).then(res => {
      if(res.status){
        ss.toast(res.title,res.type,res.message,null,"#082b49")
        clearForm()
      }
    })
  });

  document.getElementById('pgsClearFormBtn').addEventListener('click', () => {
    if(confirm('Clear all fields and reset the workstation list? Any unsaved input will be lost.')){
      clearForm();
    }
  });

  function clearForm(){
    // clear top-level fields
    document.getElementById('pgsDatetime').value = '';
    document.getElementById('pgsFlucType').selectedIndex = 0;
    document.getElementById('pgsPriority').selectedIndex = 1;
    document.getElementById('pgsArea').value = '';
    document.getElementById('pgsDuration').value = '';
    document.getElementById('pgsDescription').value = '';

    // clear any lingering error states
    ['pgsDatetime','pgsDuration','pgsDescription'].forEach(id => {
      const input = document.getElementById(id);
      const errorEl = document.getElementById(id + 'Error');
      if(input) input.classList.remove('pgs-has-error');
      if(errorEl) errorEl.classList.remove('pgs-show');
    });

    // clear and reset workstation rows back to 5 blank rows
    tbody.innerHTML = '';
    for(let i = 0; i < 5; i++){
      addWsRow({ wsNumber:'', user:'', ups:'OK', su:'OK', monitor:'OK', notes:'' });
    }
  }
















































  /* ---------- MY TICKETS: FETCH + RENDER + PAGINATION ---------- */
  let pgsAllTickets = [];
  let pgsFilteredTickets = [];
  let pgsCurrentPage = 1;
  let pgsPageSize = 5;
  let pgsSearchTerm = '';

  function statusBadgeHTML(status){
    const map = {
      'in_progress': { cls:'pgs-badge-amber', label:'In progress' },
      'closed':      { cls:'pgs-badge-green', label:'Closed' },
      'pending':     { cls:'pgs-badge-gray',  label:'Pending' }
    };
    const s = map[status] || { cls:'pgs-badge-gray', label: status };
    return `<span class="pgs-badge ${s.cls}"><span class="pgs-badge-dot"></span> ${s.label}</span>`;
  }

  function wsChipsHTML(workstations){
    if(!workstations || !workstations.length) return '<span style="color:var(--pgs-ink-faint);font-size:12px">No workstations</span>';
    const visible = workstations.slice(0, 3);
    const remaining = workstations.length - visible.length;
    let html = visible.map(ws => {
      const color = ws.status === 'resolved' ? 'var(--pgs-green)' : (ws.status === 'damaged' ? 'var(--pgs-red)' : 'var(--pgs-amber)');
      return `<span class="pgs-chip" style="border-color:${color};color:${color}">${ws.ws_number}</span>`;
    }).join('');
    if(remaining > 0){
      html += `<span class="pgs-chip" style="color:var(--pgs-ink-faint)">+${remaining} more</span>`;
    }
    return html;
  }

  function applyTicketFilter(){
    const term = pgsSearchTerm.trim().toLowerCase();
    if(!term){
      pgsFilteredTickets = pgsAllTickets;
    } else {
      pgsFilteredTickets = pgsAllTickets.filter(t => {
        const dateFormatted = t.incident_datetime
          ? new Date(t.incident_datetime).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).toLowerCase()
          : '';
        const areaValue = (t.area && t.area !== '-') ? t.area.toLowerCase() : '';

        return (
          String(t.ticket_no).toLowerCase().includes(term) ||
          (t.fluctuation_type || '').toLowerCase().includes(term) ||
          areaValue.includes(term) ||
          (t.status || '').toLowerCase().includes(term) ||
          dateFormatted.includes(term)
        );
      });
    }
    pgsCurrentPage = 1;
    renderTicketsPage();
  }

  function renderTicketsPage(){
    const tbody = document.getElementById('pgsTicketsTbody');
    const emptyState = document.getElementById('pgsTicketsEmpty');
    const paginationBar = document.getElementById('pgsTicketsPagination');

    if(!pgsFilteredTickets.length){
      tbody.innerHTML = '';
      emptyState.style.display = 'block';
      emptyState.textContent = pgsSearchTerm ? 'No tickets match your search.' : 'No tickets submitted yet.';
      paginationBar.style.display = 'none';
      return;
    }

    emptyState.style.display = 'none';
    paginationBar.style.display = 'flex';

    const totalPages = Math.ceil(pgsFilteredTickets.length / pgsPageSize);
    if(pgsCurrentPage > totalPages) pgsCurrentPage = totalPages;
    if(pgsCurrentPage < 1) pgsCurrentPage = 1;

    const start = (pgsCurrentPage - 1) * pgsPageSize;
    const pageItems = pgsFilteredTickets.slice(start, start + pgsPageSize);

    tbody.innerHTML = pageItems.map(t => {
      const resolved = t.resolved_count ?? 0;
      const total = t.workstation_count ?? (t.workstations ? t.workstations.length : 0);
      const pct = total > 0 ? Math.round((resolved / total) * 100) : 0;

      return `
        <tr>
          <td class="pgs-ticket-no">#${t.ticket_no}</td>
          <td style="color:var(--pgs-ink-soft)">${new Date(t.incident_datetime).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
          <td>${t.fluctuation_type}</td>
          <td style="color:var(--pgs-ink-soft)">${t.area != "-" ? t.area : ""}</td>
          <td>${wsChipsHTML(t.workstations)}</td>
          <td>
            <div class="pgs-progress-text">${resolved} / ${total} resolved</div>
            <div class="pgs-progress-bar"><div class="pgs-progress-fill" style="width:${pct}%"></div></div>
          </td>
          <td>${statusBadgeHTML(t.status)}</td>
        </tr>
      `;
    }).join('');

    renderPaginationControls(totalPages);
  }

  function renderPaginationControls(totalPages){
    const infoEl = document.getElementById('pgsPaginationInfo');
    const pageNumsEl = document.getElementById('pgsPageNumbers');
    const prevBtn = document.getElementById('pgsPrevPageBtn');
    const nextBtn = document.getElementById('pgsNextPageBtn');

    const start = (pgsCurrentPage - 1) * pgsPageSize + 1;
    const end = Math.min(pgsCurrentPage * pgsPageSize, pgsFilteredTickets.length);
    infoEl.textContent = `Showing ${start}–${end} of ${pgsFilteredTickets.length} tickets`;

    pageNumsEl.innerHTML = '';
    for(let i = 1; i <= totalPages; i++){
      const btn = document.createElement('button');
      btn.className = 'pgs-page-num' + (i === pgsCurrentPage ? ' pgs-page-active' : '');
      btn.textContent = i;
      btn.addEventListener('click', () => {
        pgsCurrentPage = i;
        renderTicketsPage();
      });
      pageNumsEl.appendChild(btn);
    }

    prevBtn.disabled = pgsCurrentPage <= 1;
    nextBtn.disabled = pgsCurrentPage >= totalPages;
  }

  document.getElementById('pgsPrevPageBtn').addEventListener('click', () => {
    if(pgsCurrentPage > 1){
      pgsCurrentPage--;
      renderTicketsPage();
    }
  });
  document.getElementById('pgsNextPageBtn').addEventListener('click', () => {
    const totalPages = Math.ceil(pgsFilteredTickets.length / pgsPageSize);
    if(pgsCurrentPage < totalPages){
      pgsCurrentPage++;
      renderTicketsPage();
    }
  });

  // rows-per-page dropdown
  document.getElementById('pgsRowsPerPage').addEventListener('change', function(){
    pgsPageSize = parseInt(this.value, 10);
    pgsCurrentPage = 1;
    renderTicketsPage();
  });

  // search input — debounced slightly so it doesn't refilter on every keystroke
  let pgsSearchDebounce;
  document.getElementById('pgsTicketSearch').addEventListener('input', function(){
    clearTimeout(pgsSearchDebounce);
    const value = this.value;
    pgsSearchDebounce = setTimeout(() => {
      pgsSearchTerm = value;
      applyTicketFilter();
    }, 200);
  });

  loadMyTickets()

  function loadMyTickets(){
    sole.post("../../controllers/powerguard/supervisor/get_tickets.php", {
      sup_id: localStorage.getItem("userid_sup")
    }).then(res => {
      pgsAllTickets = res.data || res || [];
      pgsFilteredTickets = pgsAllTickets;
      pgsCurrentPage = 1;
      renderTicketsPage();
    });
  }

  // load tickets when the "My tickets" tab is clicked
  document.querySelector('.pgs-tab[data-pane="pgsTickets"]').addEventListener('click', () => {
    loadMyTickets();
  });










































/* ---------- SIGN-OFF QUEUE: FETCH + RENDER ---------- */

function signoffStatusBadge(status){
    const map = {
      'submitted': { cls:'pgs-badge-amber', label:'Submitted — awaiting sign-off' },
      'pending':   { cls:'pgs-badge', label:'Pending', extraStyle:'background:var(--pgs-panel-2);color:var(--pgs-ink-faint);border:1px solid var(--pgs-line)' },
      'draft':     { cls:'pgs-badge', label:'Awaiting tech', extraStyle:'background:var(--pgs-panel-2);color:var(--pgs-ink-faint);border:1px solid var(--pgs-line)' },
      'rejected':  { cls:'pgs-badge-red', label:'Rejected' },
      'done':      { cls:'pgs-badge-green', label:'Signed off' }
    };
    const s = map[status] || { cls:'pgs-badge', label: status, extraStyle:'background:var(--pgs-panel-2);color:var(--pgs-ink-faint);border:1px solid var(--pgs-line)' };
    const styleAttr = s.extraStyle ? ` style="${s.extraStyle}"` : '';
    return `<span class="pgs-badge ${s.cls}"${styleAttr}><span class="pgs-badge-dot"></span> ${s.label}</span>`;
  }

  function signoffRowHTML(ws, ticketId){
    const isPending = ws.status === 'submitted';
    const isDimmed = ws.status === 'draft' || ws.status === 'pending';

    const findingText = ws.findings
      ? ws.findings
      : 'Assessment in progress…';

    const timeLine = ws.submitted_at
      ? new Date(ws.submitted_at).toLocaleString('en-US', { month:'short', day:'numeric', hour:'numeric', minute:'2-digit' })
      : 'Not yet submitted';

    const actionsHTML = isPending
      ? `
        <div class="pgs-signoff-actions-row">
          <button class="pgs-btn pgs-btn-sm pgs-btn-success" onclick="signOffWorkstation(${ticketId}, '${ws.ws_number}')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Sign off
          </button>
          <button class="pgs-btn pgs-btn-sm pgs-btn-reject" onclick="rejectWorkstation(${ticketId}, '${ws.ws_number}')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
            Reject
          </button>
        </div>
      `
      : '';

    return `
      <div class="pgs-signoff-row ${isDimmed ? 'pgs-disabled' : ''}">
        <div class="pgs-signoff-ws">${ws.ws_number}</div>
        <div class="pgs-signoff-tech">
          <svg style="width:12px;height:12px;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          ${ws.technician_name || 'Unassigned'}<br><span class="pgs-signoff-tech-time">${timeLine}</span>
        </div>
        <div class="pgs-signoff-finding" ${isDimmed ? 'style="color:var(--pgs-ink-faint)"' : ''}>${findingText}</div>
        <div class="pgs-signoff-actions">
          ${signoffStatusBadge(ws.status)}
          ${actionsHTML}
        </div>
      </div>
    `;
  }

  function signoffRowHTML(ws, ticketId){
    const showActionButtons = ws.status === 'submitted'; // only an actual submitted assessment can be signed off or rejected
    const isDimmed = ws.status === 'draft' || ws.status === 'pending';

    const findingText = ws.findings
      ? ws.findings
      : 'Assessment in progress…';

    const timeLine = ws.submitted_at
      ? new Date(ws.submitted_at).toLocaleString('en-US', { month:'short', day:'numeric', hour:'numeric', minute:'2-digit' })
      : 'Not yet submitted';

    const actionsHTML = showActionButtons
      ? `
        <div class="pgs-signoff-actions-row">
          <button class="pgs-btn pgs-btn-sm pgs-btn-success" onclick="signOffWorkstation(${ticketId}, '${ws.ws_number}')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Sign off
          </button>
          <button class="pgs-btn pgs-btn-sm pgs-btn-reject" onclick="rejectWorkstation(${ticketId}, '${ws.ws_number}')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
            Reject
          </button>
        </div>
      `
      : ''; // pending (unclaimed) and draft both get no buttons — nothing has been submitted yet

    return `
      <div class="pgs-signoff-row ${isDimmed ? 'pgs-disabled' : ''}">
        <div class="pgs-signoff-ws">${ws.ws_number}</div>
        <div class="pgs-signoff-tech">
          <svg style="width:12px;height:12px;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          ${ws.technician_name || 'Unassigned'}<br><span class="pgs-signoff-tech-time">${timeLine}</span>
        </div>
        <div class="pgs-signoff-finding" ${isDimmed ? 'style="color:var(--pgs-ink-faint)"' : ''}>${findingText}</div>
        <div class="pgs-signoff-actions">
          ${signoffStatusBadge(ws.status)}
          ${actionsHTML}
        </div>
      </div>
    `;
  }

  function renderSignoffQueue(tickets){
    console.log(tickets)
    const container = document.getElementById('pgsSignoffContainer');

    if(!tickets.length){
      container.innerHTML = `
        <div style="text-align:center;padding:40px 16px;color:var(--pgs-ink-faint);font-size:13px">
          <div style="display:flex;flex-direction:column;align-items:center;gap:8px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:28px;height:28px;opacity:.5"><path d="M20 6 9 17l-5-5"/></svg>
            No workstations awaiting sign-off.
          </div>
        </div>
      `;
      return;
    }

    container.innerHTML = tickets.map(ticket => `
      <div class="pgs-signoff-ticket-group">
        <div class="pgs-signoff-ticket-header">
          <span class="pgs-badge pgs-badge-blue"><span class="pgs-badge-dot"></span> #${ticket.ticket_no}</span>
        </div>
        <div class="pgs-signoff-intro">Submitted assessments awaiting your sign-off — Ticket #${ticket.ticket_no}</div>
        ${ticket.workstations.map(ws => signoffRowHTML(ws, ticket.id)).join('')}
      </div>
    `).join('');
  }

  loadSignoffQueue()
  function loadSignoffQueue(){
    sole.post("../../controllers/powerguard/supervisor/get_signoff_queue.php", {
      sup_id: localStorage.getItem("userid_sup")
    }).then(res => {
      const tickets = res.data || res || [];
      renderSignoffQueue(tickets);
    });
  }

  // load sign-off queue when the tab is clicked
  document.querySelector('.pgs-tab[data-pane="pgsSignoff"]').addEventListener('click', () => {
    loadSignoffQueue();
  });

  /* ---------- SIGN OFF / REJECT ACTIONS ---------- */
  function signOffWorkstation(ticketId, wsNumber){
    sole.post("../../controllers/powerguard/supervisor/signoff_workstation.php", {
      sup_id: localStorage.getItem("userid_sup"),
      ticket_id: ticketId,
      ws_number: wsNumber
    }).then(res => {
      console.log(res);
      loadSignoffQueue(); // refresh the list after action
    });
  }

  function rejectWorkstation(ticketId, wsNumber){
    sole.post("../../controllers/powerguard/supervisor/reject_workstation.php", {
      sup_id: localStorage.getItem("userid_sup"),
      ticket_id: ticketId,
      ws_number: wsNumber
    }).then(res => {
      console.log(res);
      loadSignoffQueue(); // refresh the list after action
    });
  }














































  





})();

if(localStorage.getItem("login_sup") == "true"){
    ss.toast(localStorage.getItem("login_title"),localStorage.getItem("login_type"),localStorage.getItem("login_message"),null,"#082b49")
    localStorage.removeItem("login_sup")
}else{
  splash(0.5, "light", "#082b49");
}

document.getElementsByClassName("pgs-avatar")[0].innerText = localStorage.getItem("pgs_avatar")
document.getElementsByClassName("pgs-name")[0].innerText = localStorage.getItem("pgs_name")
document.getElementsByClassName("pgs-role")[0].innerText = localStorage.getItem("pgs_role")