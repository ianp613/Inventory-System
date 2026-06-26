if(localStorage.getItem("login_admin") !== null){
  if(localStorage.getItem("login_admin") == "true"){
      ss.toast(localStorage.getItem("login_title"),localStorage.getItem("login_type"),localStorage.getItem("login_message"),null,"#16201d")
      localStorage.setItem("login_admin",false)
  }else{
    splash(0.5, "light", "#16201d");
  }  
}else{
  window.location.replace("signin.php")
  splash(5, "light", "#16201d");
}

(function(){

  /* ── TAB SWITCHING ── */
  const tabs = document.querySelectorAll('.pga-tab');
  const panes = document.querySelectorAll('.pga-pane');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('pga-tab-active'));
      panes.forEach(p => p.classList.remove('pga-pane-active'));
      tab.classList.add('pga-tab-active');
      document.getElementById(tab.dataset.pane).classList.add('pga-pane-active');
      localStorage.setItem("pga-active",tab.getAttribute("data-pane"))
    });
  });

  if(localStorage.getItem("pga-active") !== null){
    tabs.forEach(t => t.classList.remove('pga-tab-active'));
    panes.forEach(p => p.classList.remove('pga-pane-active'));
     tabs.forEach(tab => {
      if(tab.getAttribute("data-pane") == localStorage.getItem("pga-active")){
        tab.classList.add('pga-tab-active');
        document.getElementById(tab.dataset.pane).classList.add('pga-pane-active');
      }
    });
  }

  /* ── REASSIGN TERMINALS ── */
  const terminals = [
    { id:'WS-101', tech:'E. Macaraeg', state:'assigned' },
    { id:'WS-102', tech:'R. Bautista', state:'assigned' },
    { id:'WS-103', tech:'R. Bautista', state:'assigned' },
    { id:'WS-104', tech:null, state:'done' },
    { id:'WS-105', tech:'E. Macaraeg', state:'assigned' },
    { id:'WS-106', tech:null, state:'unassigned' },
    { id:'WS-107', tech:null, state:'unassigned' },
    { id:'WS-108', tech:null, state:'unassigned' },
    { id:'WS-109', tech:null, state:'done' },
    { id:'WS-110', tech:'E. Macaraeg', state:'assigned' },
  ];

  function pillClass(state){
    return { assigned:'pga-pill-assigned', unassigned:'pga-pill-unassigned', done:'pga-pill-done' }[state] || '';
  }

  function renderTerminals(){
    const grid = document.getElementById('pgaTerminalGrid');
    grid.innerHTML = '';
    terminals.forEach((t, idx) => {
      const pill = document.createElement('div');
      pill.className = 'pga-terminal-pill ' + pillClass(t.state);
      let inner = `<span class="pga-pill-dot"></span>${t.id}`;
      if(t.state === 'assigned') inner += ` <span class="pga-pill-sub">· ${t.tech}</span>`;
      if(t.state === 'unassigned') inner += ` <span class="pga-pill-sub">· unassigned</span>`;
      if(t.state === 'done') inner += ` <span class="pga-pill-sub">· done</span>`;
      pill.innerHTML = inner;

      if(t.state !== 'done'){
        pill.addEventListener('click', () => openReassign(idx));
      }
      grid.appendChild(pill);
    });
  }
  renderTerminals();

  let _reassignIdx = null;

  window.openReassign = function(idx){
    _reassignIdx = idx;
    const t = terminals[idx];
    document.getElementById('pgaReassignWs').textContent = t.id;
    document.getElementById('pgaReassignCurrent').textContent = t.tech ? t.tech : 'unassigned';
    document.getElementById('pgaReassignTo').value = '';
    document.getElementById('pgaReassignNote').value = '';
    document.getElementById('pgaReassignModal').style.display = 'block';
    document.getElementById('pgaReassignModal').scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeReassign = function(){
    document.getElementById('pgaReassignModal').style.display = 'none';
    _reassignIdx = null;
  };

  window.confirmReassign = function(){
    const to = document.getElementById('pgaReassignTo').value;
    if(!to){ alert('Please select a technician to assign to.'); return; }
    if(_reassignIdx === null) return;
    const t = terminals[_reassignIdx];
    const note = document.getElementById('pgaReassignNote').value;

    const payload = { ws: t.id, previous_tech: t.tech, new_tech: to, note };
    console.log('Admin reassignment payload:', payload);
    // sole.post("../../controllers/powerguard/admin_reassign.php", payload).then(res => console.log(res));

    t.tech = to;
    t.state = 'assigned';
    renderTerminals();
    closeReassign();
    alert(`${payload.ws} reassigned to ${to}.`);
  };

  /* ── DEPARTMENTS ── */
  /* RULE: exactly one supervisor per department. A supervisor may hold many departments. */
  let departments = [
    { name:'Administration', supervisor:'R. Villanueva' },
    { name:'Finance', supervisor:'P. Mendoza' },
    { name:'Information Technology', supervisor:'R. Villanueva' },
    { name:'Operations', supervisor:null },
    { name:'Human Resources', supervisor:null },
    { name:'Procurement', supervisor:'L. Ramos' },
  ];

  const availableSupervisors = ['R. Villanueva', 'P. Mendoza', 'L. Ramos', 'D. Santos', 'A. Cruz'];

  function renderDepartments(){
    const grid = document.getElementById('pgaDeptGrid');
    grid.innerHTML = '';

    departments.forEach((dept, di) => {
      const card = document.createElement('div');
      card.className = 'pga-dept-card';

      const currentSupHtml = dept.supervisor
        ? `<div class="pga-dept-current-sup">
             <span class="pga-dept-supervisor-name">
               <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
               ${dept.supervisor}
             </span>
             <span class="pga-remove-x" data-dept="${di}" title="Unassign supervisor">
               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
             </span>
           </div>`
        : `<div class="pga-dept-current-sup pga-dept-unfilled">No supervisor assigned</div>`;

      const options = availableSupervisors.map(s =>
        `<option ${s===dept.supervisor ? 'selected':''}>${s}</option>`
      ).join('');

      card.innerHTML = `
        <div class="pga-dept-card-head">
          <div class="pga-dept-name">${dept.name}</div>
        </div>
        ${currentSupHtml}
        <div style="display:flex;gap:6px">
          <select class="pga-dept-assign-select" data-dept="${di}" style="flex:1;font-size:12.5px;padding:7px 26px 7px 10px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-bg);color:var(--pga-ink);appearance:none;cursor:pointer;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a9b3ae' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 8px center;">
            <option value="">${dept.supervisor ? 'Reassign to…' : 'Assign supervisor…'}</option>
            ${options}
          </select>
          <button class="pga-btn pga-btn-sm pga-dept-assign-btn" data-dept="${di}">${dept.supervisor ? 'Reassign' : 'Assign'}</button>
        </div>
      `;
      grid.appendChild(card);
    });

    // wire unassign buttons
    grid.querySelectorAll('.pga-remove-x').forEach(el => {
      el.addEventListener('click', () => {
        const di = parseInt(el.dataset.dept);
        const removed = departments[di].supervisor;
        departments[di].supervisor = null;
        console.log('Unassigned', removed, 'from', departments[di].name);
        renderDepartments();
        renderSupervisorOverview();
      });
    });

    // wire assign/reassign buttons
    grid.querySelectorAll('.pga-dept-assign-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const di = parseInt(btn.dataset.dept);
        const select = grid.querySelector(`.pga-dept-assign-select[data-dept="${di}"]`);
        if(!select.value) return;
        const previous = departments[di].supervisor;
        departments[di].supervisor = select.value;
        console.log('Assigned', select.value, 'to', departments[di].name, previous ? `(replaced ${previous})` : '(new)');
        renderDepartments();
        renderSupervisorOverview();
      });
    });
  }

  function renderSupervisorOverview(){
    const tbody = document.querySelector('#pgaSupervisorOverviewTable tbody');
    tbody.innerHTML = '';
    availableSupervisors.forEach(sup => {
      const held = departments.filter(d => d.supervisor === sup).map(d => d.name);
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${sup}</td>
        <td>${held.length ? held.map(n => `<span class="pga-chip">${n}</span>`).join('') : '<span style="color:var(--pga-ink-faint);font-size:12px">No departments assigned</span>'}</td>
        <td class="pga-mono">${held.length}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  renderDepartments();
  renderSupervisorOverview();

  window.addDepartment = function(){
    const input = document.getElementById('pgaNewDeptName');
    const name = input.value.trim();
    if(!name){ alert('Enter a department name.'); return; }
    departments.push({ name, supervisor:null });
    input.value = '';
    renderDepartments();
    renderSupervisorOverview();
  };

  /* ── ACCOUNT APPROVALS: FETCH + RENDER + PAGINATION ── */
  let pgaApprovalsAll      = [];
  let pgaApprovalsFiltered = [];
  let pgaApprovalsPage     = 1;
  let pgaApprovalsPageSize = 5;
  let pgaApprovalsSearch   = '';

  // tracks which user IDs have been acted on this session: { id: 'approved' | 'rejected' }
  const pgaActedOn = {};

  function sortApprovals(list){
    // pending floats to top, rejected sinks to bottom
    return [...list].sort((a, b) => {
      const aActed = !!pgaActedOn[a.id] || a.account === 'rejected';
      const bActed = !!pgaActedOn[b.id] || b.account === 'rejected';
      if(aActed === bActed) return 0;
      return aActed ? 1 : -1;
    });
  }

  function approvalRowHTML(user){
    const acted  = pgaActedOn[user.id];
    const avatar = (user.fname || '').charAt(0).toUpperCase() + (user.lname || '').charAt(0).toUpperCase();

    const submittedDate = user.submitted_at
      ? new Date(user.submitted_at).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })
      : '';

    let actionsHTML;

    if(acted){
      // optimistic UI — acted on this session
      actionsHTML = `
        <span class="pga-badge ${acted === 'approved' ? 'pga-badge-green' : 'pga-badge-red'} pga-approval-acted-badge">
          <span class="pga-badge-dot"></span> ${acted === 'approved' ? 'Approved' : 'Rejected'}
        </span>`;

    } else if(user.account === 'rejected'){
      // already rejected from backend
      actionsHTML = `
        <span class="pga-badge pga-badge-red">
          <span class="pga-badge-dot"></span> Rejected
        </span>`;

    } else {
      // pending — show approve / reject buttons
      actionsHTML = `
        <div class="pga-approval-actions-row">
          <button class="pga-btn pga-btn-sm pga-btn-success"
            data-approval-action="approve"
            data-approval-id="${user.id}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Approve
          </button>
          <button class="pga-btn pga-btn-sm pga-btn-reject"
            data-approval-action="reject"
            data-approval-id="${user.id}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
            Reject
          </button>
        </div>`;
    }

    const isDimmed = acted || user.account === 'rejected';

    return `
      <div class="pga-approval-row ${isDimmed && isDimmed != "approved" ? 'pga-approval-acted' : ''}" id="pga-approval-row-${user.id}">
        <div class="pga-approval-avatar">${avatar}</div>
        <div class="pga-approval-info">
          <div class="pga-approval-name">${user.fname} ${user.lname}</div>
          <div class="pga-approval-meta">
            Job title: ${user.job_title || '—'} · Employee ID: ${user.employee_id || '—'}<br>
            ${user.email || '—'} · ${user.phone || '—'}${submittedDate ? ' · Submitted ' + submittedDate : ''}
          </div>
        </div>
        <div class="pga-approval-actions">
          <span class="pga-badge pga-badge-blue"><span class="pga-badge-dot"></span> Supervisor</span>
          ${actionsHTML}
        </div>
      </div>
    `;
  }

  function renderApprovalsPage(){
    const container    = document.getElementById('pgaApprovalsContainer');
    const pagination   = document.getElementById('pgaApprovalsPagination');
    const sorted       = sortApprovals(pgaApprovalsFiltered);

    if(!sorted.length){
      container.innerHTML = `
        <div style="text-align:center;padding:40px 16px;color:var(--pga-ink-faint);font-size:13px">
          <div style="display:flex;flex-direction:column;align-items:center;gap:8px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:28px;height:28px;opacity:.5"><path d="M20 6 9 17l-5-5"/></svg>
            No pending approvals.
          </div>
        </div>`;
      pagination.style.display = 'none';
      return;
    }

    pagination.style.display = 'flex';
    const totalPages = Math.ceil(sorted.length / pgaApprovalsPageSize);
    if(pgaApprovalsPage > totalPages) pgaApprovalsPage = totalPages;
    if(pgaApprovalsPage < 1) pgaApprovalsPage = 1;

    const start = (pgaApprovalsPage - 1) * pgaApprovalsPageSize;
    const items = sorted.slice(start, start + pgaApprovalsPageSize);

    container.innerHTML = items.map(u => approvalRowHTML(u)).join('');

    // pagination info + page numbers
    const infoEl   = document.getElementById('pgaApprovalsPaginationInfo');
    const pageNums = document.getElementById('pgaApprovalsPageNums');
    const prevBtn  = document.getElementById('pgaApprovalsPrev');
    const nextBtn  = document.getElementById('pgaApprovalsNext');

    infoEl.textContent = `Showing ${start + 1}–${Math.min(start + pgaApprovalsPageSize, sorted.length)} of ${sorted.length}`;

    pageNums.innerHTML = '';
    for(let i = 1; i <= totalPages; i++){
      const btn = document.createElement('button');
      btn.className = 'pgs-page-num' + (i === pgaApprovalsPage ? ' pgs-page-active' : '');
      btn.textContent = i;
      btn.addEventListener('click', () => { pgaApprovalsPage = i; renderApprovalsPage(); });
      pageNums.appendChild(btn);
    }

    prevBtn.disabled = pgaApprovalsPage <= 1;
    nextBtn.disabled = pgaApprovalsPage >= totalPages;
  }

  function applyApprovalsFilter(){
    const term = pgaApprovalsSearch.trim().toLowerCase();
    pgaApprovalsFiltered = !term ? pgaApprovalsAll : pgaApprovalsAll.filter(u =>
      (u.fname || '').toLowerCase().includes(term) ||
      (u.lname || '').toLowerCase().includes(term) ||
      (u.email || '').toLowerCase().includes(term) ||
      (u.employee_id || '').toLowerCase().includes(term) ||
      (u.job_title || '').toLowerCase().includes(term)
    );
    pgaApprovalsPage = 1;
    renderApprovalsPage();
  }

  function loadApprovals(){
    sole.post("../../controllers/powerguard/administrator/get_pending_approvals.php", {
      admin_id: localStorage.getItem("userid_admin")
    }).then(res => {
      pgaApprovalsAll      = res.data || res || [];
      pgaApprovalsFiltered = pgaApprovalsAll;
      pgaApprovalsPage     = 1;
      renderApprovalsPage();
    });
  }

  loadApprovals();

  document.querySelector('.pga-tab[data-pane="pgaApprovals"]').addEventListener('click', () => {
    loadApprovals();
  });

  // rows per page
  document.getElementById('pgaApprovalsRowsPerPage').addEventListener('change', function(){
    pgaApprovalsPageSize = parseInt(this.value);
    pgaApprovalsPage = 1;
    renderApprovalsPage();
  });

  // search
  let pgaApprovalsSearchDebounce;
  document.getElementById('pgaApprovalsSearch').addEventListener('input', function(){
    clearTimeout(pgaApprovalsSearchDebounce);
    const val = this.value;
    pgaApprovalsSearchDebounce = setTimeout(() => {
      pgaApprovalsSearch = val;
      applyApprovalsFilter();
    }, 200);
  });

  // prev / next
  document.getElementById('pgaApprovalsPrev').addEventListener('click', () => {
    if(pgaApprovalsPage > 1){ pgaApprovalsPage--; renderApprovalsPage(); }
  });
  document.getElementById('pgaApprovalsNext').addEventListener('click', () => {
    const total = Math.ceil(sortApprovals(pgaApprovalsFiltered).length / pgaApprovalsPageSize);
    if(pgaApprovalsPage < total){ pgaApprovalsPage++; renderApprovalsPage(); }
  });

  // approve / reject — event delegation, no onclick
  document.getElementById('pgaApprovalsContainer').addEventListener('click', function(e){
    const btn = e.target.closest('[data-approval-action]');
    if(!btn) return;

    const action = btn.dataset.approvalAction;
    const userId = btn.dataset.approvalId;

    // optimistic UI — dim the row and replace buttons with badge immediately
    pgaActedOn[userId] = action === 'approve' ? 'approved' : 'rejected';
    renderApprovalsPage(); // re-render so acted-on row sinks to bottom

    if(action === 'approve'){
      sole.post("../../controllers/powerguard/administrator/approve_account.php", {
        admin_id: localStorage.getItem("userid_admin"),
        user_id:  userId
      }).then(res => {
        ss.toast(res.title,res.type,res.message,null,"#16201d")
      });
    }

    if(action === 'reject'){
      sole.post("../../controllers/powerguard/administrator/reject_account.php", {
        admin_id: localStorage.getItem("userid_admin"),
        user_id:  userId
      }).then(res => {
        ss.toast(res.title,res.type,res.message,null,"#16201d")
      });
    }
  });

  /* ── CREATE TECHNICIAN ACCOUNT ── */
  window.generateTechPassword = function(){
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
    let pw = '';
    for(let i = 0; i < 12; i++){
      pw += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('pgaTechPassword').value = pw;
  };

  document.getElementById('pgaCreateTechBtn').addEventListener('click', () => {
    const fname = document.getElementById('pgaTechFname').value.trim();
    const lname = document.getElementById('pgaTechLname').value.trim();
    const email = document.getElementById('pgaTechEmail').value.trim();
    const phone = document.getElementById('pgaTechPhone').value.trim();
    const empid = document.getElementById('pgaTechEmpid').value.trim();
    const specialty = document.getElementById('pgaTechSpecialty').value.trim();
    const username = document.getElementById('pgaTechUsername').value.trim();
    const password = document.getElementById('pgaTechPassword').value.trim();

    if(!fname || !lname || !email || !empid || !username || !password){
      alert('Please fill in first name, last name, email, employee ID, username, and password.');
      return;
    }

    const payload = {
      fname, lname, email, phone, employee_id: empid, specialty,
      username, password,
      privileges: 'technician',
      status: 'active' /* technicians skip the pending approval queue entirely */
    };

    console.log('Create technician payload:', payload);
    // sole.post("../../controllers/powerguard/create_technician.php", payload).then(res => console.log(res));

    alert(`Technician account created for ${fname} ${lname}. Account is active immediately — no approval needed.`);

    // reset form
    ['pgaTechFname','pgaTechLname','pgaTechEmail','pgaTechPhone','pgaTechEmpid','pgaTechSpecialty','pgaTechUsername','pgaTechPassword'].forEach(id => {
      document.getElementById(id).value = '';
    });
  });

  /* ── MANAGE ACCOUNTS ── */
  let accounts = [
    { id:1, fname:'R.', lname:'Villanueva', role:'supervisor', jobtitle:'Supervisor', empid:'EMP-2024-0012', email:'r.villanueva@company.com', phone:'+63 917 555 0101', status:'active' },
    { id:2, fname:'P.', lname:'Mendoza', role:'supervisor', jobtitle:'Operations Manager', empid:'EMP-2024-0091', email:'p.mendoza@company.com', phone:'+63 917 555 0142', status:'active' },
    { id:3, fname:'L.', lname:'Ramos', role:'supervisor', jobtitle:'Supervisor', empid:'EMP-2024-0104', email:'l.ramos@company.com', phone:'+63 917 555 0212', status:'active' },
    { id:4, fname:'E.', lname:'Macaraeg', role:'technician', jobtitle:'Technician', empid:'EMP-2024-0042', email:'e.macaraeg@company.com', phone:'+63 917 555 0301', status:'active' },
    { id:5, fname:'R.', lname:'Bautista', role:'technician', jobtitle:'Technician', empid:'EMP-2024-0095', email:'r.bautista@company.com', phone:'+63 917 555 0322', status:'active' },
    { id:6, fname:'J.', lname:'Pascual', role:'technician', jobtitle:'Technician', empid:'EMP-2024-0102', email:'j.pascual@company.com', phone:'+63 917 555 0188', status:'deactivated' },
  ];

  function statusBadge(status){
    return status === 'active'
      ? `<span class="pga-badge pga-badge-green"><span class="pga-badge-dot"></span> Active</span>`
      : `<span class="pga-badge pga-badge-gray"><span class="pga-badge-dot"></span> Deactivated</span>`;
  }

  function roleBadge(role){
    return role === 'supervisor'
      ? `<span class="pga-badge pga-badge-blue"><span class="pga-badge-dot"></span> Supervisor</span>`
      : `<span class="pga-badge pga-badge-purple"><span class="pga-badge-dot"></span> Technician</span>`;
  }

  function renderAccounts(){
    const tbody = document.querySelector('#pgaAccountsTable tbody');
    const roleFilter = document.getElementById('pgaAccFilterRole').value;
    const statusFilter = document.getElementById('pgaAccFilterStatus').value;

    tbody.innerHTML = '';
    accounts
      .filter(a => roleFilter === 'all' || a.role === roleFilter)
      .filter(a => statusFilter === 'all' || a.status === statusFilter)
      .forEach(a => {
        const tr = document.createElement('tr');
        if(a.status === 'deactivated') tr.style.opacity = '0.55';
        tr.innerHTML = `
          <td>${a.fname} ${a.lname}</td>
          <td>${roleBadge(a.role)}</td>
          <td style="color:var(--pga-ink-soft)">${a.empid}</td>
          <td style="color:var(--pga-ink-soft)">${a.email}</td>
          <td>${statusBadge(a.status)}</td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <button class="pga-btn pga-btn-sm" onclick="openEditAccount(${a.id})">Edit</button>
              <button class="pga-btn pga-btn-sm" onclick="openResetPassword(${a.id})">Reset password</button>
              <button class="pga-btn pga-btn-sm ${a.status==='active' ? 'pga-btn-reject' : 'pga-btn-success'}" onclick="toggleAccountStatus(${a.id})">${a.status === 'active' ? 'Deactivate' : 'Reactivate'}</button>
              <button class="pga-btn pga-btn-sm pga-btn-reject" onclick="deleteAccount(${a.id})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                Delete
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(tr);
      });
  }

  document.getElementById('pgaAccFilterRole').addEventListener('change', renderAccounts);
  document.getElementById('pgaAccFilterStatus').addEventListener('change', renderAccounts);
  renderAccounts();

  function findAccount(id){
    return accounts.find(a => a.id === id);
  }

  /* Edit account */
  let _editingId = null;

  window.openEditAccount = function(id){
    const a = findAccount(id);
    if(!a) return;
    _editingId = id;
    document.getElementById('pgaEditAccName').textContent = a.fname + ' ' + a.lname;
    document.getElementById('pgaEditFname').value = a.fname.replace('.', '');
    document.getElementById('pgaEditLname').value = a.lname;
    document.getElementById('pgaEditEmail').value = a.email;
    document.getElementById('pgaEditPhone').value = a.phone;
    document.getElementById('pgaEditEmpid').value = a.empid;
    document.getElementById('pgaEditJobtitle').value = a.jobtitle;

    document.getElementById('pgaResetPwPanel').style.display = 'none';
    const panel = document.getElementById('pgaEditAccountPanel');
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeEditAccount = function(){
    document.getElementById('pgaEditAccountPanel').style.display = 'none';
    _editingId = null;
  };

  window.saveEditAccount = function(){
    if(_editingId === null) return;
    const a = findAccount(_editingId);
    if(!a) return;

    a.fname = document.getElementById('pgaEditFname').value.trim().charAt(0) + '.';
    a.lname = document.getElementById('pgaEditLname').value.trim();
    a.email = document.getElementById('pgaEditEmail').value.trim();
    a.phone = document.getElementById('pgaEditPhone').value.trim();
    a.empid = document.getElementById('pgaEditEmpid').value.trim();
    a.jobtitle = document.getElementById('pgaEditJobtitle').value.trim();

    console.log('Saved account edit:', a);
    // sole.post("../../controllers/powerguard/update_account.php", a).then(res => console.log(res));

    renderAccounts();
    closeEditAccount();
    alert('Account details updated.');
  };

  /* Reset password */
  let _resetId = null;

  window.openResetPassword = function(id){
    const a = findAccount(id);
    if(!a) return;
    _resetId = id;
    document.getElementById('pgaResetPwName').textContent = a.fname + ' ' + a.lname;
    document.getElementById('pgaResetPwValue').value = '';

    document.getElementById('pgaEditAccountPanel').style.display = 'none';
    const panel = document.getElementById('pgaResetPwPanel');
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeResetPassword = function(){
    document.getElementById('pgaResetPwPanel').style.display = 'none';
    _resetId = null;
  };

  window.generateResetPassword = function(){
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
    let pw = '';
    for(let i = 0; i < 12; i++){
      pw += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('pgaResetPwValue').value = pw;
  };

  window.confirmResetPassword = function(){
    const pw = document.getElementById('pgaResetPwValue').value.trim();
    if(!pw){ alert('Generate or enter a new password first.'); return; }
    const a = findAccount(_resetId);
    if(!a) return;

    console.log('Password reset for', a.empid, '— new temp password:', pw);
    // sole.post("../../controllers/powerguard/reset_password.php", { id: a.id, password: pw }).then(res => console.log(res));

    alert(`Password reset for ${a.fname} ${a.lname}. They must change it on next login.`);
    closeResetPassword();
  };

  /* Deactivate / reactivate */
  window.toggleAccountStatus = function(id){
    const a = findAccount(id);
    if(!a) return;
    a.status = a.status === 'active' ? 'deactivated' : 'active';
    console.log('Status toggled for', a.empid, '→', a.status);
    // sole.post("../../controllers/powerguard/toggle_status.php", { id: a.id, status: a.status }).then(res => console.log(res));
    renderAccounts();
  };

  /* Delete */
  window.deleteAccount = function(id){
    const a = findAccount(id);
    if(!a) return;
    if(!confirm(`Permanently delete the account for ${a.fname} ${a.lname}? This cannot be undone.`)) return;
    accounts = accounts.filter(acc => acc.id !== id);
    console.log('Deleted account', a.empid);
    // sole.post("../../controllers/powerguard/delete_account.php", { id }).then(res => console.log(res));
    renderAccounts();
  };

















  

  const pgaMoreBtn      = document.getElementById('pgaMoreBtn');
  const pgaMoreDropdown = document.getElementById('pgaMoreDropdown');

  pgaMoreBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    pgaMoreDropdown.classList.toggle('pga-dropdown-open');
  });

  document.addEventListener('click', () => {
    pgaMoreDropdown.classList.remove('pga-dropdown-open');
  });

  document.getElementById('pgaSettingsBtn').addEventListener('click', () => {
    pgaMoreDropdown.classList.remove('pga-dropdown-open');
    window.location.href = '../../views/powerguard/settings.php';
  });

  document.getElementById('pgaLogoutBtn').addEventListener('click', () => {
    pgaMoreDropdown.classList.remove('pga-dropdown-open');
      Swal.fire({
        title: "You are about to leave",
        text: "Do you wish to end your current session?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Confirm",
        customClass: {
            popup: 'my-custom-popup',
            actions: 'my-right-buttons'
        }
    }).then((result) => {
        if (result.isConfirmed){
          localStorage.removeItem("userid_admin")
          localStorage.removeItem("login_admin")
          window.location.replace("signin.php");
        }
    });
  });

})();

document.getElementsByClassName("pga-avatar")[0].innerText = localStorage.getItem("pga_avatar")
document.getElementsByClassName("pga-name")[0].innerText = localStorage.getItem("pga_name")
document.getElementsByClassName("pga-role-label")[0].innerText = localStorage.getItem("pga_role")
