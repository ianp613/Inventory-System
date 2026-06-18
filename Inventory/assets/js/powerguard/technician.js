
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

  /* ── TERMINAL PILLS — TICKET 1 ── */
  const ticket1Terminals = [
    { id:'WS-101', state:'mine' },
    { id:'WS-102', state:'taken', tech:'R. Bautista' },
    { id:'WS-103', state:'taken', tech:'R. Bautista' },
    { id:'WS-104', state:'done' },
    { id:'WS-105', state:'mine' },
    { id:'WS-106', state:'free' },
    { id:'WS-107', state:'free' },
    { id:'WS-108', state:'free' },
    { id:'WS-109', state:'done' },
    { id:'WS-110', state:'mine' },
    { id:'WS-111', state:'free' },
    { id:'WS-112', state:'free' },
    { id:'WS-113', state:'free' },
    { id:'WS-114', state:'free' },
  ];

  const ticket2Terminals = [
    { id:'WS-F01', state:'taken', tech:'J. Pascual' },
    { id:'WS-F02', state:'free' },
    { id:'WS-F03', state:'free' },
    { id:'WS-F04', state:'done' },
  ];

  function stateClass(state){
    return { mine:'pgt-pill-mine', free:'pgt-pill-free', taken:'pgt-pill-taken', done:'pgt-pill-done' }[state] || '';
  }

  function buildTerminalGrid(containerId, terminals){
    const grid = document.getElementById(containerId);
    terminals.forEach((t, idx) => {
      const pill = document.createElement('div');
      pill.className = 'pgt-terminal-pill ' + stateClass(t.state);
      pill.dataset.idx = idx;

      let inner = `<span class="pgt-pill-dot"></span>${t.id}`;
      if(t.state === 'mine') inner += ` <span class="pgt-pill-sub">· mine</span>`;
      if(t.state === 'taken') inner += ` <span class="pgt-pill-sub">· ${t.tech}</span>`;
      if(t.state === 'done') inner += ` <span class="pgt-pill-sub">· done</span>`;
      pill.innerHTML = inner;

      if(t.state === 'free'){
        pill.title = 'Tap to claim ' + t.id;
        pill.addEventListener('click', () => {
          t.state = 'mine';
          pill.className = 'pgt-terminal-pill pgt-pill-mine';
          pill.innerHTML = `<span class="pgt-pill-dot"></span>${t.id} <span class="pgt-pill-sub">· mine</span>`;
        });
      }

      grid.appendChild(pill);
    });
  }

  buildTerminalGrid('pgtTerminalGrid1', ticket1Terminals);
  buildTerminalGrid('pgtTerminalGrid2', ticket2Terminals);

  /* ── TRANSFER MODAL ── */
  let _transferWs = '';
  let _transferTicket = '';

  window.openTransfer = function(ws, ticket){
    _transferWs = ws;
    _transferTicket = ticket;
    document.getElementById('pgtTransferWs').textContent = ws;
    document.getElementById('pgtTransferTicket').textContent = ticket;
    document.getElementById('pgtTransferModal').style.display = 'block';
    // scroll to modal
    document.getElementById('pgtTransferModal').scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeTransfer = function(){
    document.getElementById('pgtTransferModal').style.display = 'none';
    document.getElementById('pgtTransferTo').value = '';
    document.getElementById('pgtTransferReason').value = '';
  };

  window.confirmTransfer = function(){
    const to = document.getElementById('pgtTransferTo').value;
    const reason = document.getElementById('pgtTransferReason').value;
    if(!to){ alert('Please select a technician to transfer to.'); return; }
    if(!reason){ alert('Please select a reason for the transfer.'); return; }
    const payload = { ws: _transferWs, ticket: _transferTicket, transfer_to: to, reason };
    console.log('Transfer payload:', payload);
    // sole.post("../../controllers/powerguard/transfer.php", payload).then(res => console.log(res));
    closeTransfer();
    alert(`${_transferWs} transferred to ${to} successfully.`);
  };

  /* ── COMPONENT SELECT COLOR CLASS ── */
  window.applyCompClass = function(select){
    const val = select.value.toLowerCase();
    ['pgt-damaged','pgt-suspected','pgt-ok','pgt-critical','pgt-warning','pgt-cleared'].forEach(c => select.classList.remove(c));
    if(val.includes('damaged') || val.includes('total loss') || val.includes('critical')) select.classList.add('pgt-damaged');
    else if(val.includes('functional') || val.includes('ok') || val.includes('cleared')) select.classList.add('pgt-ok');
    else if(val.includes('warning') || val.includes('repair')) select.classList.add('pgt-warning');
  };

  /* ── WORKSTATION DAMAGE DECLARATION (supervisor's original ticket data) ── */
  const declarations = {
    'WS-101': { user:'J. Santos', ups:'Damaged', system:'Suspected', monitor:'OK', notes:'Burnt smell from UPS' },
    'WS-105': { user:'L. Garcia', ups:'Damaged', system:'Damaged', monitor:'OK', notes:'System won\'t POST' },
    'WS-110': { user:'F. Aquino', ups:'Suspected', system:'OK', monitor:'OK', notes:'Awaiting technician inspection' },
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
    renderDeclaration(this.value);
  });

  /* ── SUBMIT ASSESSMENT ── */
  document.getElementById('pgtSubmitAssessBtn').addEventListener('click', () => {
    const ws = document.getElementById('pgtAssessWs').value;
    if(!ws){ alert('Please select a terminal to assess.'); return; }

    const payload = {
      ws_number: ws,
      assessed_at: document.getElementById('pgtAssessDatetime').value,
      ups_condition: document.getElementById('pgtUpsCondition').value,
      system_unit_condition: document.getElementById('pgtSuCondition').value,
      monitor_condition: document.getElementById('pgtMonCondition').value,
      technical_findings: document.getElementById('pgtFindings').value,
      parts_needed: document.getElementById('pgtParts').value,
      estimated_completion: document.getElementById('pgtCompletion').value,
      escalate_to: document.getElementById('pgtEscalate').value,
    };

    console.log('Assessment payload:', payload);
    // sole.post("../../controllers/powerguard/assessment.php", payload).then(res => console.log(res));
  });

  /* ── SAVE DRAFT ── */
  document.getElementById('pgtSaveDraftBtn').addEventListener('click', () => {
    console.log('Saving draft...');
  });

})();


if(localStorage.getItem("login_tech") == "true"){
    ss.toast(localStorage.getItem("login_title"),localStorage.getItem("login_type"),localStorage.getItem("login_message"),null,"#223330")
    localStorage.removeItem("login_tech")
}else{
  splash(0.5, "light", "#16201d");
}