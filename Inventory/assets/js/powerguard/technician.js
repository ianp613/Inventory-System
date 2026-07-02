if(localStorage.getItem("login_tech") !== null){
  if(localStorage.getItem("login_tech") == "true"){
      ss.toast(localStorage.getItem("login_title"),localStorage.getItem("login_type"),localStorage.getItem("login_message"),null,"#16201d")
      localStorage.setItem("login_tech",false)
  }else{
    splash(0.5, "light", "#16201d");
  }  
}else{
  window.location.replace("signin.php")
  splash(5, "light", "#16201d");
}

(function(){

  /* ── TICKET DETAILS TOGGLE ── */
  window.toggleTicketDetails = function(panelId, btn){
    const panel = document.getElementById(panelId);
    const isOpen = panel.classList.contains('pgt-details-panel-open');
    panel.classList.toggle('pgt-details-panel-open', !isOpen);
    btn.classList.toggle('pgt-details-open', !isOpen);
    btn.childNodes[0].textContent = isOpen ? 'View details ' : 'Hide details ';
  };

  /* ── TAB SWITCHING ── */
  const tabs = document.querySelectorAll('.pgt-tab');
  const panes = document.querySelectorAll('.pgt-pane');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('pgt-tab-active'));
      panes.forEach(p => p.classList.remove('pgt-pane-active'));
      tab.classList.add('pgt-tab-active');
      document.getElementById(tab.dataset.pane).classList.add('pgt-pane-active');
      localStorage.setItem("pgt-active",tab.getAttribute("data-pane"))
    });
  });

  if(localStorage.getItem("pgt-active") !== null){
    tabs.forEach(t => t.classList.remove('pgt-tab-active'));
    panes.forEach(p => p.classList.remove('pgt-pane-active'));
     tabs.forEach(tab => {
      if(tab.getAttribute("data-pane") == localStorage.getItem("pgt-active")){
        tab.classList.add('pgt-tab-active');
        document.getElementById(tab.dataset.pane).classList.add('pgt-pane-active');
      }
    });
  }

  // /* ── TERMINAL PILLS — TICKET 1 ── */
  // const ticket1Terminals = [
  //   { id:'WS-101', state:'mine' },
  //   { id:'WS-102', state:'taken', tech:'R. Bautista' },
  //   { id:'WS-103', state:'taken', tech:'R. Bautista' },
  //   { id:'WS-104', state:'done' },
  //   { id:'WS-105', state:'mine' },
  //   { id:'WS-106', state:'free' },
  //   { id:'WS-107', state:'free' },
  //   { id:'WS-108', state:'free' },
  //   { id:'WS-109', state:'done' },
  //   { id:'WS-110', state:'mine' },
  //   { id:'WS-111', state:'free' },
  //   { id:'WS-112', state:'free' },
  //   { id:'WS-113', state:'free' },
  //   { id:'WS-114', state:'free' },
  // ];

  // const ticket2Terminals = [
  //   { id:'WS-F01', state:'taken', tech:'J. Pascual' },
  //   { id:'WS-F02', state:'free' },
  //   { id:'WS-F03', state:'free' },
  //   { id:'WS-F04', state:'done' },
  // ];

  function stateClass(state){
    return { mine:'pgt-pill-mine', free:'pgt-pill-free', taken:'pgt-pill-taken', done:'pgt-pill-done' }[state] || '';
  }

  // function buildTerminalGrid(containerId, terminals){
  //   const grid = document.getElementById(containerId);
  //   terminals.forEach((t, idx) => {
  //     const pill = document.createElement('div');
  //     pill.className = 'pgt-terminal-pill ' + stateClass(t.state);
  //     pill.dataset.idx = idx;

  //     let inner = `<span class="pgt-pill-dot"></span>${t.id}`;
  //     if(t.state === 'mine') inner += ` <span class="pgt-pill-sub">· mine</span>`;
  //     if(t.state === 'taken') inner += ` <span class="pgt-pill-sub">· ${t.tech}</span>`;
  //     if(t.state === 'done') inner += ` <span class="pgt-pill-sub">· done</span>`;
  //     pill.innerHTML = inner;

  //     if(t.state === 'free'){
  //       pill.title = 'Tap to claim ' + t.id;
  //       pill.addEventListener('click', () => {
  //         t.state = 'mine';
  //         pill.className = 'pgt-terminal-pill pgt-pill-mine';
  //         pill.innerHTML = `<span class="pgt-pill-dot"></span>${t.id} <span class="pgt-pill-sub">· mine</span>`;
  //       });
  //     }

  //     grid.appendChild(pill);
  //   });
  // }

  // buildTerminalGrid('pgtTerminalGrid1', ticket1Terminals);
  // buildTerminalGrid('pgtTerminalGrid2', ticket2Terminals);






























/* ── ALL TICKETS: FETCH + RENDER ── */

  function priorityBadge(priority){
    const map = {
      'high':   { cls:'pgt-badge-red',   label:'High priority' },
      'medium': { cls:'pgt-badge-amber', label:'Medium priority' },
      'low':    { cls:'pgt-badge-green', label:'Low priority' },
    };
    const p = map[(priority || '').toLowerCase()] || { cls:'pgt-badge-gray', label: priority };
    return `<span class="pgt-badge ${p.cls}"><span class="pgt-badge-dot"></span> ${p.label}</span>`;
  }

  function ticketStatusBadge(status){
    const map = {
      'in_progress': { cls:'pgt-badge-amber', label:'In progress' },
      'closed':      { cls:'pgt-badge-green', label:'Closed' },
      'pending':     { cls:'pgt-badge-gray',  label:'Pending' },
    };
    const s = map[(status || '').toLowerCase()] || { cls:'pgt-badge-gray', label: status };
    return `<span class="pgt-badge ${s.cls}"><span class="pgt-badge-dot"></span> ${s.label}</span>`;
  }

  function terminalPillHTML(ws, ticketId){
    const techId = localStorage.getItem('userid_tech');
    let state, inner, dataAttrs = '';

    if(ws.status === 'done' || ws.status === 'closed'){
      state = 'done';
      inner = `<span class="pgt-pill-dot"></span>${ws.ws_number} <span class="pgt-pill-sub">· done</span>`;
    } else if(!ws.claimed_by){
      state = 'free';
      inner = `<span class="pgt-pill-dot"></span>${ws.ws_number}`;
      dataAttrs = `data-claim="true" data-ws="${ws.ws_number}" data-id="${ws.id}" data-ticket="${ticketId}" title="Tap to claim ${ws.ws_number}"`;
    } else if(String(ws.claimed_by) === String(techId)){
      state = 'mine';
      inner = `<span class="pgt-pill-dot"></span>${ws.ws_number} <span class="pgt-pill-sub">· mine</span>`;
    } else {
      state = 'taken';
      inner = `<span class="pgt-pill-dot"></span>${ws.ws_number} <span class="pgt-pill-sub">· ${ws.technician_name || 'taken'}</span>`;
    }

    return `<div class="pgt-terminal-pill pgt-pill-${state}" ${dataAttrs} id="pgt-pill-${ticketId}-${ws.ws_number.replace(/\W/g,'_')}">${inner}</div>`;
  }

  function ticketBlockHTML(ticket){
    const isClosed = ticket.status === 'closed';
    const detailId = `pgtDetails_${ticket.id}`;
    const gridId   = `pgtGrid_${ticket.id}`;

    const dateStr = ticket.incident_datetime
      ? new Date(ticket.incident_datetime).toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit' })
      : '';

    const terminalPills = (ticket.workstations || [])
      .map(ws => terminalPillHTML(ws, ticket.id))
      .join('');

    const hasFreeTerminals = (ticket.workstations || []).some(ws => !ws.claimed_by && ws.status !== 'done');

    return `
      <div class="pgt-ticket-block ${isClosed ? 'pgt-closed-ticket' : ''}">
        <div class="pgt-ticket-head">
          <div class="pgt-ticket-head-left">
            <span class="pgt-mono" style="font-size:13px">#${ticket.ticket_no}</span>
            ${priorityBadge(ticket.priority)}
            ${ticketStatusBadge(ticket.status)}
            <button class="pgt-details-btn" data-toggle-detail="${detailId}">
              View details
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pgt-ticket-meta">
            ${dateStr}${ticket.supervisor_name ? ' · ' + ticket.supervisor_name : ''} · ${ticket.dept_name}${ticket.area ? ' · ' + ticket.area : ''} · ${ticket.fluctuation_type || ''} · ${(ticket.workstations || []).length} terminals
          </div>
        </div>

        <div class="pgt-details-panel" id="${detailId}">
          <div class="pgt-details-grid">
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Fluctuation type</div>
              <div class="pgt-details-val">${ticket.fluctuation_type || '—'}</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Duration</div>
              <div class="pgt-details-val">${ticket.duration_minutes ? ticket.duration_minutes + ' minutes' : '—'}</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Incident date &amp; time</div>
              <div class="pgt-details-val">${dateStr || '—'}</div>
            </div>
            <div class="pgt-details-item">
              <div class="pgt-details-lbl">Area / floor</div>
              <div class="pgt-details-val">${ticket.area || '—'}</div>
            </div>
          </div>
          <div class="pgt-details-desc">
            <div class="pgt-details-desc-lbl">Description</div>
            ${ticket.description || 'No description provided.'}
          </div>
        </div>

        <div class="pgt-ticket-body">
          <div class="pgt-terminal-grid" id="${gridId}">
            ${terminalPills || '<span style="color:var(--pgt-ink-faint);font-size:12px">No terminals declared.</span>'}
          </div>
          ${!isClosed && hasFreeTerminals ? `
          <div class="pgt-legend">
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-blue)"></div> Mine</div>
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-ink-faint)"></div> Unclaimed — tap to claim</div>
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-green)"></div> Resolved</div>
            <div class="pgt-legend-item"><div class="pgt-legend-dot" style="background:var(--pgt-ink-faint);opacity:.4"></div> Taken by another tech</div>
          </div>` : ''}
        </div>
      </div>
    `;
  }

  function renderAllTickets(tickets){
    const container = document.getElementById('pgtAllTicketsContainer');

    if(!tickets.length){
      container.innerHTML = `
        <div style="text-align:center;padding:40px 16px;color:var(--pgt-ink-faint);font-size:13px">
          <div style="display:flex;flex-direction:column;align-items:center;gap:8px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:28px;height:28px;opacity:.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            No incident tickets submitted yet.
          </div>
        </div>
      `;
      return;
    }

    container.innerHTML = tickets.map(t => ticketBlockHTML(t)).join('');
  }

  function loadAllTickets(){
    sole.post("../../controllers/powerguard/technician/get_all_tickets.php", {
      tech_id: localStorage.getItem("userid_tech")
    }).then(res => {
      document.getElementById("pgt_tickets").innerText = res.length
      pgtAllTicketsData = res.data || res || [];
      filterAndRenderTickets();
    });
  }


  /* ----------- Search function ----------------- */
  let pgtAllTicketsSearchTerm = '';
  let pgtAllTicketsData       = []; // store the full unfiltered list

  function filterAndRenderTickets(){
    const term = pgtAllTicketsSearchTerm.trim().toLowerCase();
    const techId = localStorage.getItem('userid_tech');

    if(!term){
      renderAllTickets(pgtAllTicketsData);
      return;
    }

    const filtered = pgtAllTicketsData.filter(ticket => {

        // format the date the same way your table renders it
        const dateFormatted = ticket.incident_datetime
        ? new Date(ticket.incident_datetime).toLocaleString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric',
            hour: 'numeric', minute: '2-digit'
          }).toLowerCase()
        : '';

      // ticket-level fields
      const ticketMatch =
        String(ticket.ticket_no || '').toLowerCase().includes(term) ||
        (ticket.description || '').toLowerCase().includes(term) ||
        (ticket.fluctuation_type || '').toLowerCase().includes(term) ||
        (ticket.area || '').toLowerCase().includes(term) ||
        (ticket.priority || '').toLowerCase().includes(term) ||
        (ticket.status || '').toLowerCase().includes(term) ||
        (ticket.supervisor_name || '').toLowerCase().includes(term) ||
        (ticket.dept_name || '').toLowerCase().includes(term) ||
        dateFormatted.includes(term);

      // workstation-level fields
      const wsMatch = (ticket.workstations || []).some(ws => {
        // ws number
        const wsNumMatch = (ws.ws_number || '').toLowerCase().includes(term);

        // status labels — mine, unclaimed, done, taken + technician name
        const isMine     = String(ws.claimed_by) === String(techId);
        const isUnclaimed = !ws.claimed_by;
        const isDone     = ws.status === 'done' || ws.status === 'closed';

        const stateLabel = isMine ? 'mine' : isUnclaimed ? 'unclaimed' : isDone ? 'done' : 'taken';
        const stateMatch = stateLabel.includes(term);

        // technician name who claimed it
        const techNameMatch = (ws.technician_name || '').toLowerCase().includes(term);

        return wsNumMatch || stateMatch || techNameMatch;
      });

      return ticketMatch || wsMatch;
    });

    renderAllTickets(filtered);
  }

  // search input listener
  let pgtAllTicketsSearchDebounce;
  document.getElementById('pgtAllTicketsSearch').addEventListener('input', function(){
    clearTimeout(pgtAllTicketsSearchDebounce);
    const val = this.value;
    pgtAllTicketsSearchDebounce = setTimeout(() => {
      pgtAllTicketsSearchTerm = val;
      filterAndRenderTickets();
    }, 200);
  });

  /* ── DETAIL TOGGLE — event delegation (no onclick) ── */
  document.getElementById('pgtAllTicketsContainer').addEventListener('click', function(e){

    // View details toggle
    const detailBtn = e.target.closest('[data-toggle-detail]');
    if(detailBtn){
      const panelId = detailBtn.dataset.toggleDetail;
      const panel   = document.getElementById(panelId);
      const isOpen  = panel.classList.contains('pgt-details-panel-open');
      panel.classList.toggle('pgt-details-panel-open', !isOpen);
      detailBtn.classList.toggle('pgt-details-open', !isOpen);
      detailBtn.childNodes[0].textContent = isOpen ? 'View details ' : 'Hide details ';
      return;
    }

    // Claim terminal
    const pill = e.target.closest('[data-claim="true"]');
    if(!pill) return;

    const wsId     = pill.dataset.id;
    const wsNumber = pill.dataset.ws;
    const ticketId = pill.dataset.ticket;

    Swal.fire({
      position: 'top',
      title: `Claim ${wsNumber}?`,
      text: `You will be assigned to workstation ${wsNumber}. Once claimed, you are responsible for assessing it.`,
      icon: null,
      showCancelButton: true,
      confirmButtonColor: '#16201d',
      confirmButtonText: 'Claim it',
      customClass: {
        popup: 'my-custom-popup',
        actions: 'my-right-buttons'
      }
    }).then(result => {
      if(!result.isConfirmed) return;

      // optimistic UI — immediately mark as mine
      const techName = localStorage.getItem('pgt_name') || 'me';
      pill.className = 'pgt-terminal-pill pgt-pill-mine';
      pill.innerHTML = `<span class="pgt-pill-dot"></span>${wsNumber} <span class="pgt-pill-sub">· mine</span>`;
      pill.removeAttribute('data-claim');
      pill.removeAttribute('title');

      // send to backend
      sole.post("../../controllers/powerguard/technician/claim_terminal.php", {
        tech_id:   localStorage.getItem("userid_tech"),
        ticket_id: ticketId,
        ws_number: wsNumber,
        ws_id: wsId
      }).then(res => {
        loadWorkStations()
        console.log('Claim response:', res);
      });
    });
  });




































  function loadAllTechnician(){
    pgtTransferTo.innerHTML = `<option selected disabled value="">Select technician</option>`
    sole.post("../../controllers/powerguard/technician/get_all_technician.php",{
      id : localStorage.getItem("userid_tech")
    }).then(res => {
      res.forEach(user => {
        var op = document.createElement("option")
        op.value = `${user.id}|${user.fname[0]}. ${user.lname}`
        op.innerText = `${user.fname[0]}. ${user.lname}`
        pgtTransferTo.appendChild(op)
      })
    })
  }












  /* ── WORKSTATION DAMAGE DECLARATION (supervisor's original ticket data) ── */
  let declarations = {
    'WS-101': { user:'J. Santos', ups:'Damaged', system:'Suspected', monitor:'OK', notes:'Burnt smell from UPS' },
    'WS-105': { user:'L. Garcia', ups:'Damaged', system:'Damaged', monitor:'OK', notes:'System won\'t POST' },
    'WS-110': { user:'F. Aquino', ups:'Suspected', system:'OK', monitor:'OK', notes:'Awaiting technician inspection' },
  };



  function loadWorkStations(){
    var pgtAssessWs = document.getElementById('pgtAssessWs')
    var pgtAssessWs_ = pgtAssessWs.value

    sole.post("../../controllers/powerguard/technician/get_claimed_workstation.php",{
      tech_id : localStorage.getItem("userid_tech")
    }).then(res => {
      document.getElementById("pgt_completed").innerText = res[1][0].length
      document.getElementById("pgt_submitted").innerText = res[1][1]
      if(res[1][2]){
        document.getElementById("pgt_rejected_container").hidden = false
        document.getElementById("pgt_rejected_container").classList.add("pgt-badge")
        document.getElementById("pgt_rejected").innerText = `${res[1][2]} rejected assessment${res[1][2] > 1 ? "s" : ""}`
      }else{
        document.getElementById("pgt_rejected_container").hidden = true
        document.getElementById("pgt_rejected_container").classList.remove("pgt-badge")
        document.getElementById("pgt_rejected").innerText = `0 rejected assessment`
      }
      document.getElementById("pgt_claimed").innerText = res[0].length

      pgtAssessWs.innerHTML = `<option selected disabled value="">Choose a workstation…</option>`
      res[0].forEach(pg_ws => {
        var op = document.createElement("option")
        op.value = `${pg_ws["ws_number"]}|${pg_ws["id"]}|${pg_ws["sign_off_queue"]}`
        op.innerText = `${pg_ws["ws_number"]} — #${pg_ws["ticket_no"]} (${pg_ws["sign_off_queue"]})`
        pgtAssessWs.appendChild(op)
      })

      // Load completed assessements
      pgtCompletedAll      = res[1][0].data || res[1][0] || [];
      pgtCompletedFiltered = pgtCompletedAll;
      pgtCompletedPage     = 1;
      renderCompletedTable();

      // Load claimed workstation
      pgtClaimedAll      = res[0];
      pgtClaimedFiltered = pgtClaimedAll;
      pgtClaimedPage     = 1;
      renderClaimedTable();

      declarations = res[0].reduce((acc, item) => {
        acc[item.ws_number] = {
          user: item.assigned_user,
          ups: item.ups_status,
          system: item.system_unit_status,
          monitor: item.monitor_status,
          notes: item.notes != "-" ? item.notes : "",
          wd_id: item.id
        };
        return acc;
      }, {});
      if(Object.keys(declarations).includes(localStorage.getItem("pgtAssessWs"))){
        pgtAssessWs.value = localStorage.getItem("pgtAssessWs")
      }

      if(pgtAssessWs_ && localStorage.getItem("pgtAssessWs") !== null){
        pgtAssessWs.value = localStorage.getItem("pgtAssessWs")
        loadWsAssessment(localStorage.getItem("pgtAssessWs"))
      }
    })
  }




























/* ── MY WORK — CLAIMED TABLE ── */
  let pgtClaimedAll = [], pgtClaimedFiltered = [], pgtClaimedPage = 1, pgtClaimedPageSize = 5;

  function wsStatusBadge(status){
    const map = {
      'submitted': { cls:'pgt-badge-amber', label:'Submitted' },
      'draft':     { cls:'pgt-badge-blue',  label:'In progress' },
      'pending':   { cls:'pgt-badge-gray',  label:'Not started' },
      'rejected':  { cls:'pgt-badge-red',   label:'Rejected' },
    };
    const s = map[(status||'').toLowerCase()] || { cls:'pgt-badge-gray', label: status };
    return `<span class="pgt-badge ${s.cls}"><span class="pgt-badge-dot"></span> ${s.label}</span>`;
  }

  function wsPriorityBadge(priority){
    const map = {
      'high':   { cls:'pgt-badge-red',   label:'High' },
      'medium': { cls:'pgt-badge-amber', label:'Medium' },
      'low':    { cls:'pgt-badge-green', label:'Low' },
    };
    const p = map[(priority||'').toLowerCase()] || { cls:'pgt-badge-gray', label: priority };
    return `<span class="pgt-badge ${p.cls}" style="font-size:11px"><span class="pgt-badge-dot"></span> ${p.label}</span>`;
  }

  function renderClaimedTable(){
    const tbody    = document.getElementById('pgtClaimedTbody');
    const pagination = document.getElementById('pgtClaimedPagination');

    if(!pgtClaimedFiltered.length){
      tbody.innerHTML = `
        <tr><td colspan="5" style="text-align:center;padding:28px;color:var(--pgt-ink-faint);font-size:13px">
          <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:24px;height:24px;opacity:.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            No claimed workstations.
          </div>
        </td></tr>`;
      pagination.style.display = 'none';
      return;
    }

    pagination.style.display = 'flex';
    const totalPages = Math.ceil(pgtClaimedFiltered.length / pgtClaimedPageSize);
    if(pgtClaimedPage > totalPages) pgtClaimedPage = totalPages;
    if(pgtClaimedPage < 1) pgtClaimedPage = 1;

    const start = (pgtClaimedPage - 1) * pgtClaimedPageSize;
    const items = pgtClaimedFiltered.slice(start, start + pgtClaimedPageSize);

    tbody.innerHTML = items.map(ws => {
      return `
        <tr>
          <td class="pgt-mono">${ws.ws_number}</td>
          <td style="color:var(--pgt-ink-soft)">#${ws.ticket_no}</td>
          <td>${wsPriorityBadge(ws.priority)}</td>
          <td>${wsStatusBadge(ws.sign_off_queue)}</td>
          <td>
            ${ws.sign_off_queue != "submitted" ? `
            <button class="pgt-btn pgt-btn-sm pgt-btn-transfer"
              data-transfer-id="${ws.id}"
              data-transfer-ws="${ws.ws_number}"
              data-transfer-ticket="#${ws.ticket_no}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
              Transfer
            </button>` : "—" } 
          </td>
        </tr>`;
    }).join('');

    renderPaginationFor('pgtClaimed', totalPages, pgtClaimedPage, pgtClaimedFiltered.length, pgtClaimedPageSize, (p) => {
      pgtClaimedPage = p; renderClaimedTable();
    });
  }

  function applyClaimedFilter(){
    const term = document.getElementById('pgtClaimedSearch').value.trim().toLowerCase();
    pgtClaimedFiltered = !term ? pgtClaimedAll : pgtClaimedAll.filter(ws =>
      (ws.ws_number||'').toLowerCase().includes(term) ||
      (ws.ticket_no||'').toLowerCase().includes(term) ||
      (ws.priority||'').toLowerCase().includes(term) ||
      (ws.sign_off_queue||'').toLowerCase().includes(term)
    );
    pgtClaimedPage = 1;
    renderClaimedTable();
  }

  /* ── MY WORK — COMPLETED TABLE ── */
  let pgtCompletedAll = [], pgtCompletedFiltered = [], pgtCompletedPage = 1, pgtCompletedPageSize = 5;

  // function signoffBadge(status){
  //   if(status === 'done'){
  //     return `<span class="pgt-badge pgt-badge-green"><svg style="width:11px;height:11px;margin-right:3px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Signed off</span>`;
  //   }
  //   if(status === 'rejected'){
  //     return `<span class="pgt-badge pgt-badge-red"><span class="pgt-badge-dot"></span> Rejected</span>`;
  //   }
  //   return `<span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> Awaiting sign-off</span>`;
  // }

  // function resultBadge(ups, su, monitor){
  //   const allOk = [ups, su, monitor].every(v => (v||'').toLowerCase().includes('functional'));
  //   return allOk
  //     ? `<span class="pgt-badge pgt-badge-green"><span class="pgt-badge-dot"></span> Cleared</span>`
  //     : `<span class="pgt-badge pgt-badge-amber"><span class="pgt-badge-dot"></span> Repaired</span>`;
  // }

  function renderCompletedTable(){
    const tbody    = document.getElementById('pgtCompletedTbody');
    const pagination = document.getElementById('pgtCompletedPagination');

    if(!pgtCompletedFiltered.length){
      tbody.innerHTML = `
        <tr><td colspan="4" style="text-align:center;padding:28px;color:var(--pgt-ink-faint);font-size:13px">
          <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:24px;height:24px;opacity:.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            No completed assessments yet.
          </div>
        </td></tr>`;
      pagination.style.display = 'none';
      return;
    }

    pagination.style.display = 'flex';
    const totalPages = Math.ceil(pgtCompletedFiltered.length / pgtCompletedPageSize);
    if(pgtCompletedPage > totalPages) pgtCompletedPage = totalPages;
    if(pgtCompletedPage < 1) pgtCompletedPage = 1;

    const start = (pgtCompletedPage - 1) * pgtCompletedPageSize;
    const items = pgtCompletedFiltered.slice(start, start + pgtCompletedPageSize);

    tbody.innerHTML = items.map(ws => {
      const dateStr = ws.assessed_at
        ? new Date(ws.assessed_at).toLocaleString('en-US',{month:'short',day:'numeric',hour:'numeric',minute:'2-digit'})
        : '—';
      const findings = ws.technical_findings && ws.technical_findings !== '-'
        ? ws.technical_findings
        : dateStr != "—" ? '<span style="color:var(--pgt-ink-faint)">No findings recorded.</span>'
        : '<span style="color:var(--pgt-ink-faint)">—</span>'
      return `
        <tr>
          <td class="pgt-mono">${ws.ws_number}</td>
          <td style="color:var(--pgt-ink-soft)">#${ws.ticket_no}</td>
          <td style="font-size:12.5px;color:var(--pgt-ink-soft);max-width:280px">${findings}</td>
          <td style="color:var(--pgt-ink-soft);font-size:12px">${dateStr}</td>
        </tr>`;
    }).join('');

    renderPaginationFor('pgtCompleted', totalPages, pgtCompletedPage, pgtCompletedFiltered.length, pgtCompletedPageSize, (p) => {
      pgtCompletedPage = p; renderCompletedTable();
    });
  }

  function applyCompletedFilter(){
    const term = document.getElementById('pgtCompletedSearch').value.trim().toLowerCase();
    pgtCompletedFiltered = !term ? pgtCompletedAll : pgtCompletedAll.filter(ws =>
      (ws.ws_number||'').toLowerCase().includes(term) ||
      (ws.ticket_no||'').toLowerCase().includes(term) ||
      (ws.technical_findings ||'').toLowerCase().includes(term) ||
      (ws.assessed_at ||'').toLowerCase().includes(term)
    );
    pgtCompletedPage = 1;
    renderCompletedTable();
  }

  /* ── SHARED PAGINATION RENDERER ── */
  function renderPaginationFor(prefix, totalPages, currentPage, totalItems, pageSize, onPageClick){
    const infoEl    = document.getElementById(prefix + 'PaginationInfo');
    const pageNums  = document.getElementById(prefix + 'PageNums');
    const prevBtn   = document.getElementById(prefix + 'Prev');
    const nextBtn   = document.getElementById(prefix + 'Next');

    const start = (currentPage - 1) * pageSize + 1;
    const end   = Math.min(currentPage * pageSize, totalItems);
    infoEl.textContent = `Showing ${start}–${end} of ${totalItems}`;

    pageNums.innerHTML = '';
    for(let i = 1; i <= totalPages; i++){
      const btn = document.createElement('button');
      btn.className = 'pgs-page-num' + (i === currentPage ? ' pgs-page-active' : '');
      btn.textContent = i;
      btn.addEventListener('click', () => onPageClick(i));
      pageNums.appendChild(btn);
    }

    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
  }

  /* ── WIRE CONTROLS ── */
  document.getElementById('pgtClaimedRowsPerPage').addEventListener('change', function(){
    pgtClaimedPageSize = parseInt(this.value); pgtClaimedPage = 1; renderClaimedTable();
  });
  document.getElementById('pgtCompletedRowsPerPage').addEventListener('change', function(){
    pgtCompletedPageSize = parseInt(this.value); pgtCompletedPage = 1; renderCompletedTable();
  });

  let pgtClaimedSearchDebounce, pgtCompletedSearchDebounce;
  document.getElementById('pgtClaimedSearch').addEventListener('input', function(){
    clearTimeout(pgtClaimedSearchDebounce);
    pgtClaimedSearchDebounce = setTimeout(() => applyClaimedFilter(), 200);
  });
  document.getElementById('pgtCompletedSearch').addEventListener('input', function(){
    clearTimeout(pgtCompletedSearchDebounce);
    pgtCompletedSearchDebounce = setTimeout(() => applyCompletedFilter(), 200);
  });

  document.getElementById('pgtClaimedPrev').addEventListener('click', () => {
    if(pgtClaimedPage > 1){ pgtClaimedPage--; renderClaimedTable(); }
  });
  document.getElementById('pgtClaimedNext').addEventListener('click', () => {
    if(pgtClaimedPage < Math.ceil(pgtClaimedFiltered.length / pgtClaimedPageSize)){ pgtClaimedPage++; renderClaimedTable(); }
  });
  document.getElementById('pgtCompletedPrev').addEventListener('click', () => {
    if(pgtCompletedPage > 1){ pgtCompletedPage--; renderCompletedTable(); }
  });
  document.getElementById('pgtCompletedNext').addEventListener('click', () => {
    if(pgtCompletedPage < Math.ceil(pgtCompletedFiltered.length / pgtCompletedPageSize)){ pgtCompletedPage++; renderCompletedTable(); }
  });

  /* ── TRANSFER — event delegation, no onclick ── */
  document.getElementById('pgtMyWork').addEventListener('click', function(e){
    const btn = e.target.closest('[data-transfer-ws]');
    if(!btn) return;
    openTransfer(btn.dataset.transferId, btn.dataset.transferWs, btn.dataset.transferTicket);
  });
































  function toDatetimeLocal(val){
    if(!val) return '';
    const d = new Date(val);
    if(isNaN(d.getTime())) return '';
    // format to yyyy-MM-ddThh:mm
    const pad = n => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }

  function loadWsAssessment(data){
    var ws_id = data.split("|")[1]
    sole.post("../../controllers/powerguard/technician/get_assessment.php",{
      ws_id : ws_id
    }).then(res => {
      clearAssessmentForm()

      if(res.length){
        document.getElementById('pgtAssessDatetime').value = toDatetimeLocal(res[0]["assessed_at"]);

        ["pgtUpsCondition", "pgtSuCondition", "pgtMonCondition"].forEach((id, i) => {
          const keys = ["ups_condition", "system_unit_condition", "monitor_condition"];
          const el = document.getElementById(id);
          el.value = res[0][keys[i]] != "-" ? res[0][keys[i]] : "";
          applyCompClass(el);
        });

        document.getElementById('pgtFindings').value = res[0]["technical_findings"] != "-" ? res[0]["technical_findings"] : "";
        document.getElementById('pgtParts').value =  res[0]["parts_needed"] != "-" ? res[0]["parts_needed"] : "";
        document.getElementById('pgtEscalate').value =  res[0]["escalate_to"] != "-" ? res[0]["escalate_to"] : "";

      }else{
        document.getElementById('pgtAssessDatetime').value = getNow()
      }
    })
  }





















  var pgtClearAssessBtn = document.getElementById("pgtClearAssessBtn")
  pgtClearAssessBtn.addEventListener("click", () => {
    clearAssessmentForm_All()
  })

  function clearAssessmentForm_All(){
    document.getElementById('pgtAssessWs').value = ""
    document.getElementById('pgtDeclBlock').innerHTML = `
      <div class="pgt-decl-empty">Select a terminal above to view the supervisor's original damage declaration.</div>
    `;
    pgtClearAssessBtn.hidden = true
    pgtClearAssessBtn.classList.remove("pgt-btn")

    document.getElementById("pgt_already_submitted_container").hidden = true
    document.getElementById("pgt_save_container").hidden = false
    clearAssessmentForm()
  }

  function clearAssessmentForm(){
    document.getElementById('pgtAssessDatetime').value = '';

    // component condition selects + remove color classes + clear errors
    ['pgtUpsCondition','pgtSuCondition','pgtMonCondition'].forEach(id => {
      const el = document.getElementById(id);
      el.value = '';
      ['pgt-damaged','pgt-suspected','pgt-ok','pgt-critical','pgt-warning','pgt-cleared'].forEach(c => el.classList.remove(c));
      el.classList.remove('pgt-has-error');
      document.getElementById(id + 'Error').classList.remove('pgt-show');
    });

    document.getElementById('pgtFindings').value = '';
    document.getElementById('pgtParts').value = '';
    document.getElementById('pgtEscalate').value = '';

    document.getElementById('pgtAssessDatetime').classList.remove('pgt-has-error');
    document.getElementById('pgtAssessDatetimeError').classList.remove('pgt-show');

    document.getElementById('pgtFindings').classList.remove('pgt-has-error');
    document.getElementById('pgtFindingsError').classList.remove('pgt-show');
  }


































  /* ── TRANSFER MODAL ── */
  let _transferWsId = '';
  let _transferWs = '';
  let _transferTicket = '';

  window.openTransfer = function(wsId, ws, ticket){
    _transferWsId   = wsId;
    _transferWs     = ws;
    _transferTicket = ticket;
    document.getElementById('pgtTransferWs').textContent     = ws;
    document.getElementById('pgtTransferTicket').textContent = ticket;
    document.getElementById('pgtTransferModal').style.display = 'block';
    document.getElementById('pgtTransferModal').scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeTransfer = function(){
    document.getElementById('pgtTransferModal').style.display = 'none';
    document.getElementById('pgtTransferTo').value = '';
    document.getElementById('pgtTransferReason').value = '';
  };

  window.confirmTransfer = function(){
    const to     = document.getElementById('pgtTransferTo').value;
    const reason = document.getElementById('pgtTransferReason').value;

    if(!to)    { ss.toast("Tranfer Failed!","warning","Please select a technician to transfer to.",null,"#16201d"); return; }
    if(!reason){ ss.toast("Tranfer Failed!","warning","Please select a reason for the transfer.",null,"#16201d"); return; }

    const tech_id = to.split("|")[0]
    const tech_name = to.split("|")[1]

    const payload = {
      ws:        _transferWs,
      ws_id:     _transferWsId,
      ticket:    _transferTicket,
      tech_id:   tech_id,
      tech_name: tech_name,
      reason:    reason
    };
    console.log('Transfer payload:', payload);
    sole.post("../../controllers/powerguard/technician/transfer_terminal.php", payload).then(res => {
      ss.toast(res.title,res.type,res.message,null,"#16201d")
      closeTransfer();
      callAllLoadFunction();
    });
  };

  /* ── COMPONENT SELECT COLOR CLASS ── */
  window.applyCompClass = function(select){
    const val = select.value.toLowerCase();
    ['pgt-damaged','pgt-suspected','pgt-ok','pgt-critical','pgt-warning','pgt-cleared'].forEach(c => select.classList.remove(c));
    if(val.includes('damaged') || val.includes('total loss') || val.includes('critical')) select.classList.add('pgt-damaged');
    else if(val.includes('functional') || val.includes('ok') || val.includes('cleared')) select.classList.add('pgt-ok');
    else if(val.includes('warning') || val.includes('repair')) select.classList.add('pgt-warning');
  };

  function declStatusClass(status){
    const s = (status || '').toLowerCase();
    if(s === 'damaged') return 'pgt-decl-damaged';
    if(s === 'suspected') return 'pgt-decl-suspected';
    return 'pgt-decl-ok';
  }

  function renderDeclaration(ws){
    const block = document.getElementById('pgtDeclBlock');
    const d = declarations[ws];
    if(!d){
      block.innerHTML = `<div class="pgt-decl-empty">Select a terminal above to view the supervisor's original damage declaration.</div>`;
      return;
    }
    block.innerHTML = `
      <div class="pgt-decl-grid">
        <div class="pgt-decl-item">
          <div class="pgt-decl-lbl">Assigned user</div>
          <div class="pgt-decl-val">${d.user}</div>
        </div>
        <div class="pgt-decl-item">
          <div class="pgt-decl-lbl">UPS status</div>
          <span class="pgt-decl-status ${declStatusClass(d.ups)}"><span class="pgt-decl-status-dot"></span>${d.ups}</span>
        </div>
        <div class="pgt-decl-item">
          <div class="pgt-decl-lbl">System unit status</div>
          <span class="pgt-decl-status ${declStatusClass(d.system)}"><span class="pgt-decl-status-dot"></span>${d.system}</span>
        </div>
        <div class="pgt-decl-item">
          <div class="pgt-decl-lbl">Monitor status</div>
          <span class="pgt-decl-status ${declStatusClass(d.monitor)}"><span class="pgt-decl-status-dot"></span>${d.monitor}</span>
        </div>
      </div>
      <div class="pgt-decl-notes">
        <div class="pgt-decl-notes-lbl">Supervisor notes</div>
        ${d.notes}
      </div>
    `;
  }

  
  document.getElementById('pgtAssessWs').addEventListener('change', function(){
    if(this.value){
      pgtClearAssessBtn.hidden = false
      pgtClearAssessBtn.classList.add("pgt-btn")
      localStorage.setItem("pgtAssessWs",this.value)
      loadWsAssessment(this.value)
    }else{
      clearAssessmentForm()
      pgtClearAssessBtn.hidden = true
      pgtClearAssessBtn.classList.remove("pgt-btn")
      localStorage.removeItem("pgtAssessWs")
    }
    var pgtAssessWs_ = this.value.split("|")
    if(pgtAssessWs_[2] == "submitted"){
      document.getElementById("pgt_already_submitted_container").hidden = false
      document.getElementById("pgt_save_container").hidden = true
    }else{
      document.getElementById("pgt_already_submitted_container").hidden = true
      document.getElementById("pgt_save_container").hidden = false
    }
    renderDeclaration(pgtAssessWs_[0]);
  });

/* ── ASSESSMENT FORM: LIVE ERROR CLEAR ── */
  document.getElementById('pgtAssessDatetime').addEventListener('input', function(){
    this.classList.remove('pgt-has-error');
    document.getElementById('pgtAssessDatetimeError').classList.remove('pgt-show');
  });
  document.getElementById('pgtFindings').addEventListener('input', function(){
    this.classList.remove('pgt-has-error');
    document.getElementById('pgtFindingsError').classList.remove('pgt-show');
  });
  ['pgtUpsCondition','pgtSuCondition','pgtMonCondition'].forEach(id => {
    document.getElementById(id).addEventListener('change', function(){
      this.classList.remove('pgt-has-error');
      document.getElementById(id + 'Error').classList.remove('pgt-show');
    });
  });

  /* ── SUBMIT ASSESSMENT ── */
  document.getElementById('pgtSubmitAssessBtn').addEventListener('click', () => {
    if(!pgtAssessWs.value){ ss.toast("Select Workstation","warning","Please select a workstation to assess.",null,"#16201d"); return; }
    

    // collect refs
    const datetimeInput  = document.getElementById('pgtAssessDatetime');
    const upsInput       = document.getElementById('pgtUpsCondition');
    const suInput        = document.getElementById('pgtSuCondition');
    const monInput       = document.getElementById('pgtMonCondition');
    const findingsInput  = document.getElementById('pgtFindings');

    // reset all error states
    [datetimeInput, upsInput, suInput, monInput, findingsInput].forEach(el => {
      el.classList.remove('pgt-has-error');
    });
    ['pgtAssessDatetimeError','pgtUpsConditionError','pgtSuConditionError','pgtMonConditionError','pgtFindingsError'].forEach(id => {
      document.getElementById(id).classList.remove('pgt-show');
    });

    let hasError = false;

    // ── datetime
    if(!datetimeInput.value){
      datetimeInput.classList.add('pgt-has-error');
      document.getElementById('pgtAssessDatetimeError').classList.add('pgt-show');
      hasError = true;
    } else if(new Date(datetimeInput.value) > new Date()){
      datetimeInput.classList.add('pgt-has-error');
      document.getElementById('pgtAssessDatetimeError').textContent = 'Assessment date & time cannot be in the future.';
      document.getElementById('pgtAssessDatetimeError').classList.add('pgt-show');
      hasError = true;
    }

    // ── UPS condition
    if(!upsInput.value){
      upsInput.classList.add('pgt-has-error');
      document.getElementById('pgtUpsConditionError').classList.add('pgt-show');
      hasError = true;
    }

    // ── System unit condition
    if(!suInput.value){
      suInput.classList.add('pgt-has-error');
      document.getElementById('pgtSuConditionError').classList.add('pgt-show');
      hasError = true;
    }

    // ── Monitor condition
    if(!monInput.value){
      monInput.classList.add('pgt-has-error');
      document.getElementById('pgtMonConditionError').classList.add('pgt-show');
      hasError = true;
    }

    // ── Technical findings
    if(!findingsInput.value.trim()){
      findingsInput.classList.add('pgt-has-error');
      document.getElementById('pgtFindingsError').classList.add('pgt-show');
      hasError = true;
    }

    if(hasError) return;
    
    sole.post("../../controllers/powerguard/technician/submit_assessment.php", {
      ws_id:                  pgtAssessWs.value.split("|")[1],
      assessed_at:            getNow(),
      ups_condition:          upsInput.value,
      system_unit_condition:  suInput.value,
      monitor_condition:      monInput.value,
      technical_findings:     findingsInput.value,
      parts_needed:           document.getElementById('pgtParts').value,
      escalate_to:            document.getElementById('pgtEscalate').value,
      type:                   "submit"
    }).then(res => {
      ss.toast(res.title,res.type,res.message,null,"#16201d")
      localStorage.removeItem("pgtAssessWs")
      loadWorkStations()
      setTimeout(() => {
        clearAssessmentForm_All()
      }, 100);
    })

    // sole.post("../../controllers/powerguard/assessment.php", payload).then(res => console.log(res));
  });

  /* ── SAVE DRAFT ── */
  document.getElementById('pgtSaveDraftBtn').addEventListener('click', () => {
    if(!pgtAssessWs.value){ ss.toast("Select Workstation","warning","Please select a workstation to assess.",null,"#16201d"); return; }

    const datetimeInput  = document.getElementById('pgtAssessDatetime');
    const upsInput       = document.getElementById('pgtUpsCondition');
    const suInput        = document.getElementById('pgtSuCondition');
    const monInput       = document.getElementById('pgtMonCondition');
    const findingsInput  = document.getElementById('pgtFindings');
    
    sole.post("../../controllers/powerguard/technician/submit_assessment.php", {
      ws_id:                  pgtAssessWs.value.split("|")[1],
      assessed_at:            getNow(),
      ups_condition:          upsInput.value,
      system_unit_condition:  suInput.value,
      monitor_condition:      monInput.value,
      technical_findings:     findingsInput.value,
      parts_needed:           document.getElementById('pgtParts').value,
      escalate_to:            document.getElementById('pgtEscalate').value,
      type:                   "draft"
    }).then(res => {
      ss.toast(res.title,res.type,res.message,null,"#16201d")
      localStorage.removeItem("pgtAssessWs")
      loadWorkStations()
      setTimeout(() => {
        clearAssessmentForm_All()
      }, 100);
    })
  });




































  const pgtMoreBtn      = document.getElementById('pgtMoreBtn');
  const pgtMoreDropdown = document.getElementById('pgtMoreDropdown');

  pgtMoreBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    pgtMoreDropdown.classList.toggle('pgt-dropdown-open');
  });

  document.addEventListener('click', () => {
    pgtMoreDropdown.classList.remove('pgt-dropdown-open');
  });

  document.getElementById('pgtSettingsBtn').addEventListener('click', () => {
    pgtMoreDropdown.classList.remove('pgt-dropdown-open');
    window.location.href = '../../views/powerguard/settings.php';
  });

  document.getElementById('pgtLogoutBtn').addEventListener('click', () => {
    pgtMoreDropdown.classList.remove('pgt-dropdown-open');
      Swal.fire({
        position: 'top',
        title: "You are about to leave",
        text: "Feel free to come back anytime to continue your activities. Would you like to end your current session?",
        icon: null,
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Confirm",
        customClass: {
            title: 'left-align-swal',
            htmlContainer: 'left-align-swal',
            popup: 'my-custom-popup',
            actions: 'my-right-buttons'
        }
    }).then((result) => {
        if (result.isConfirmed){
          localStorage.removeItem("userid_tech")
          localStorage.removeItem("login_tech")
          window.location.replace("signin.php");
        }
    });
  });









  function getNow(){
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    return `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
  }











  document.querySelector('.pgt-tab[data-pane="pgtMyWork"]').addEventListener('click', () => {
    callAllLoadFunction();
  });

  document.querySelector('.pgt-tab[data-pane="pgtAssessment"]').addEventListener('click', () => {
    callAllLoadFunction()
  });

  document.querySelector('.pgt-tab[data-pane="pgtAllTickets"]').addEventListener('click', () => {
    callAllLoadFunction()
  });

  document.querySelector('.pgt-tab[data-pane="pgtReports"]').addEventListener('click', () => {
    ss.toast("Reports Unavailable","info","Gereration of reports will be available soon.",null,"#16201d")
  });

  function callAllLoadFunction(){
    loadWorkStations()
    loadAllTickets()
    loadAllTechnician()
  }

  callAllLoadFunction()
})();



document.getElementsByClassName("pgt-avatar")[0].innerText = localStorage.getItem("pgt_avatar")
document.getElementsByClassName("pgt-name")[0].innerText = localStorage.getItem("pgt_name")
document.getElementsByClassName("pgt-role-label")[0].innerText = localStorage.getItem("pgt_role")




