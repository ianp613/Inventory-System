    
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
    });
  });

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

    const payload = {
        incident_datetime: document.getElementById('pgsDatetime').value,
        fluctuation_type: document.getElementById('pgsFlucType').value,
        priority: document.getElementById('pgsPriority').value,
        area: document.getElementById('pgsArea').value,
        duration_minutes: document.getElementById('pgsDuration').value,
        description: document.getElementById('pgsDescription').value,
        workstations: workstations
    };

    console.log('Submitting incident ticket:', payload);
    // Wire this up to your backend endpoint, e.g.:
    // sole.post("../../controllers/powerguard/submit_ticket.php", payload).then(res => console.log(res));
  });

})();

if(localStorage.getItem("login") == "true"){
    ss.toast(localStorage.getItem("login_title"),localStorage.getItem("login_type"),localStorage.getItem("login_message"))
}