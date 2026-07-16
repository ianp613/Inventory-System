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
  let departments        = [];
  let availableSupervisors = [];

  // supervisor overview pagination
  let pgaDeptOvPage     = 1;
  let pgaDeptOvPageSize = 5;

  function loadDepartments(){
    // fetch departments and supervisors in parallel
    Promise.all([
      sole.post("../../controllers/powerguard/administrator/get_departments.php", {
        admin_id: localStorage.getItem("userid_admin")
      }),
      sole.post("../../controllers/powerguard/administrator/get_supervisors.php", {
        admin_id: localStorage.getItem("userid_admin")
      })
    ]).then(([deptRes, supRes]) => {
      departments        = deptRes.data || deptRes || [];
      availableSupervisors = supRes.data || supRes || [];
      renderDepartments();
      renderSupervisorOverview();
    });
  }

  loadDepartments();

  let pgaDeptSearchTerm = '';
  function renderDepartments(){
    const grid = document.getElementById('pgaDeptGrid');
    grid.innerHTML = '';

    const term = pgaDeptSearchTerm.trim().toLowerCase();
    const filtered = !term
      ? departments
      : departments.filter(d =>
          (d.name || '').toLowerCase().includes(term) ||
          (d.sup_name || '').toLowerCase().includes(term)
        );

    if(!filtered.length){
      grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:28px;color:var(--pga-ink-faint);font-size:13px">${term ? 'No departments match your search.' : 'No departments yet. Add one above.'}</div>`;
      return;
    }

    filtered.forEach((dept, di) => {
      const card = document.createElement('div');
      card.className = 'pga-dept-card';

      const currentSupHtml = dept.sup_name
        ? `<div class="pga-dept-current-sup">
             <span class="pga-dept-supervisor-name">
               <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
               ${dept.sup_name}
             </span>
             <span class="pga-remove-x" data-dept-supervisor="${dept.sup_name}" data-dept-id="${dept.id}" data-dept-name="${dept.name}" title="Unassign supervisor">
               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
             </span>
           </div>`
        : `<div class="pga-dept-current-sup pga-dept-unfilled">No supervisor assigned</div>`;

      const options = availableSupervisors.map(s =>
        `<option value="${s.id}|${s.name}" ${s.id == dept.sup_id ? 'selected' : ''}>${s.name}</option>`
      ).join('');

      card.innerHTML = `
        <div class="pga-dept-card-head">
          <div class="pga-dept-name">${dept.name}</div>
          <button class="pga-btn pga-btn-sm pga-btn-delete" data-dept-id="${dept.id}" data-dept-name="${dept.name}" data-dept-has-sup="${!!dept.sup_name}" title="Delete department">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
          </button>
        </div>
        ${currentSupHtml}
        <div style="display:flex;gap:6px">
          <select class="pga-dept-assign-select" data-dept-id="${dept.id}" style="flex:1;font-size:12.5px;padding:7px 26px 7px 10px;border-radius:6px;border:1px solid var(--pga-line);background:var(--pga-bg);color:var(--pga-ink);appearance:none;cursor:pointer;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a9b3ae' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 8px center;">
            <option value="">${dept.sup_name ? 'Reassign to…' : 'Assign supervisor…'}</option>
            ${options}
          </select>
          <button class="pga-btn pga-btn-sm pga-dept-assign-btn" data-dept-id="${dept.id}" data-dept-name="${dept.name}">${dept.sup_name ? 'Reassign' : 'Assign'}</button>
        </div>
      `;
      grid.appendChild(card);
    });

    // wire delete buttons
    grid.querySelectorAll('.pga-btn-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const deptId   = btn.dataset.deptId;
        const deptName = btn.dataset.deptName;
        Swal.fire({
            position: "top",
            title: `Delete ${deptName}?`,
            text: "Please note that all tickets, termninals and assessments that are recorded under this department will also be deleted. This action cannot be undone.",
            icon: null,
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Delete",
            customClass: {
                title: 'left-align-swal',
                htmlContainer: 'left-align-swal',
                popup: 'my-custom-popup',
                actions: 'my-right-buttons'
            }
        }).then((result) => {
            if (result.isConfirmed){
              sole.post("../../controllers/powerguard/administrator/delete_department.php", {
                admin_id: localStorage.getItem("userid_admin"),
                dept_id:  deptId,
                dept_name: deptName
              }).then(res => {
                ss.toast(res.title, res.type, res.message, null, "#16201d");
                if(res.status) loadDepartments();
              });
            }
        });
      });
    });

    // wire unassign buttons
    grid.querySelectorAll('.pga-remove-x').forEach(el => {
      el.addEventListener('click', () => {
        const deptId = el.dataset.deptId;
        const deptName = el.dataset.deptName;
        const sup_name = el.dataset.deptSupervisor;

        Swal.fire({
            position: 'top',
            title: `Remove ${sup_name} from ${deptName}?`,
            text: `Please note that submitted assessments cannot be signed off if there is no supervisor assigned to this department.`,
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
              sole.post("../../controllers/powerguard/administrator/unassign_supervisor.php", {
                admin_id: localStorage.getItem("userid_admin"),
                dept_id:  deptId,
                dept_name:  deptName,
                sup_name: sup_name
              }).then(res => {
                ss.toast(res.title, res.type, res.message, null, "#16201d");
                if(res.status) loadDepartments();
              });     
            }
        });
      });
    });

    // wire assign/reassign buttons
    grid.querySelectorAll('.pga-dept-assign-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const deptId  = btn.dataset.deptId;
        const deptName  = btn.dataset.deptName;
        const select  = grid.querySelector(`.pga-dept-assign-select[data-dept-id="${deptId}"]`);
        if(!select.value) return;
        const sup_id = select.value.split("|")[0]
        const sup_name = select.value.split("|")[1]

        Swal.fire({
            position: 'top',
            title: `Assign ${sup_name} to ${deptName}?`,
            text: `All ongoing and closed tickets under this department will also be assigned to ${sup_name}.`,
            icon: null,
            showCancelButton: true,
            confirmButtonColor: "#16201d",
            confirmButtonText: "Confirm",
            customClass: {
                title: 'left-align-swal',
                htmlContainer: 'left-align-swal',
                popup: 'my-custom-popup',
                actions: 'my-right-buttons'
            }
        }).then((result) => {
            if (result.isConfirmed){
              sole.post("../../controllers/powerguard/administrator/assign_supervisor.php", {
                admin_id:    localStorage.getItem("userid_admin"),
                dept_id:     deptId,
                sup_id:      sup_id,
                sup_name:      sup_name,
                dept_name:    deptName
              }).then(res => {
                ss.toast(res.title, res.type, res.message, null, "#16201d");
                if(res.status) loadDepartments();
              });      
            }
        });
      });
    });
  }

  // search listener
  let pgaDeptSearchDebounce;
  document.getElementById('pgaDeptSearch').addEventListener('input', function(){
    clearTimeout(pgaDeptSearchDebounce);
    const val = this.value;
    pgaDeptSearchDebounce = setTimeout(() => {
      pgaDeptSearchTerm = val;
      renderDepartments();
    }, 200);
  });

  /* ── SUPERVISOR OVERVIEW — paginated + search + alphabetical ── */
  let pgaDeptOvSearchTerm = '';

  function renderSupervisorOverview(){
    const supMap = {};
    availableSupervisors.forEach(s => { supMap[s.id] = { name: s.name, depts: [] }; });
    departments.forEach(d => {
      if(d.sup_id && supMap[d.sup_id]){
        supMap[d.sup_id].depts.push(d.name);
      }
    });

    // sort alphabetically then filter by search
    let sorted = Object.values(supMap).sort((a, b) => a.name.localeCompare(b.name));

    const term = pgaDeptOvSearchTerm.trim().toLowerCase();
    if(term){
      sorted = sorted.filter(s => s.name.toLowerCase().includes(term));
    }

    const totalPages = Math.ceil(sorted.length / pgaDeptOvPageSize) || 1;
    if(pgaDeptOvPage > totalPages) pgaDeptOvPage = totalPages;
    if(pgaDeptOvPage < 1) pgaDeptOvPage = 1;

    const start = (pgaDeptOvPage - 1) * pgaDeptOvPageSize;
    const items = sorted.slice(start, start + pgaDeptOvPageSize);

    const tbody = document.getElementById('pgaDeptOvTbody');

    if(!sorted.length){
      tbody.innerHTML = `
        <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--pga-ink-faint);font-size:13px">
          No supervisors found.
        </td></tr>`;
        document.getElementById('pgaDeptOvPagination').style.display = 'none'
      return;
    }

    tbody.innerHTML = items.map(sup => `
      <tr>
        <td>${sup.name}</td>
        <td>${sup.depts.length
          ? sup.depts.map(n => `<span class="pga-chip">${n}</span>`).join('')
          : '<span style="color:var(--pga-ink-faint);font-size:12px">No departments assigned</span>'
        }</td>
        <td class="pga-mono">${sup.depts.length}</td>
      </tr>
    `).join('');

    // pagination controls
    const pagination = document.getElementById('pgaDeptOvPagination');
    const infoEl     = document.getElementById('pgaDeptOvPaginationInfo');
    const pageNums   = document.getElementById('pgaDeptOvPageNums');
    const prevBtn    = document.getElementById('pgaDeptOvPrev');
    const nextBtn    = document.getElementById('pgaDeptOvNext');

    pagination.style.display = 'flex';

    const end = Math.min(start + pgaDeptOvPageSize, sorted.length);
    infoEl.textContent = `Showing ${start + 1}–${end} of ${sorted.length}`;

    pageNums.innerHTML = '';
    for(let i = 1; i <= totalPages; i++){
      const btn = document.createElement('button');
      btn.className = 'pgs-page-num' + (i === pgaDeptOvPage ? ' pgs-page-active' : '');
      btn.textContent = i;
      btn.addEventListener('click', () => { pgaDeptOvPage = i; renderSupervisorOverview(); });
      pageNums.appendChild(btn);
    }

    prevBtn.disabled = pgaDeptOvPage <= 1;
    nextBtn.disabled = pgaDeptOvPage >= totalPages;
  }

  // rows per page
  document.getElementById('pgaDeptOvRowsPerPage').addEventListener('change', function(){
    pgaDeptOvPageSize = parseInt(this.value);
    pgaDeptOvPage = 1;
    renderSupervisorOverview();
  });

  // search
  let pgaDeptOvSearchDebounce;
  document.getElementById('pgaDeptOvSearch').addEventListener('input', function(){
    clearTimeout(pgaDeptOvSearchDebounce);
    const val = this.value;
    pgaDeptOvSearchDebounce = setTimeout(() => {
      pgaDeptOvSearchTerm = val;
      pgaDeptOvPage = 1;
      renderSupervisorOverview();
    }, 200);
  });

  // prev / next
  document.getElementById('pgaDeptOvPrev').addEventListener('click', () => {
    if(pgaDeptOvPage > 1){ pgaDeptOvPage--; renderSupervisorOverview(); }
  });
  document.getElementById('pgaDeptOvNext').addEventListener('click', () => {
    pgaDeptOvPage++; renderSupervisorOverview();
  });

  /* ── ADD DEPARTMENT — with validation ── */
  document.getElementById('pgaNewDeptName').addEventListener('input', function(){
    this.classList.remove('pga-has-error');
  });

  window.addDepartment = function(){
    const input   = document.getElementById('pgaNewDeptName');
    const name    = input.value.trim();

    input.classList.remove('pga-has-error');

    if(!name){
      input.classList.add('pga-has-error');
      return;
    }

    sole.post("../../controllers/powerguard/administrator/add_department.php", {
      admin_id: localStorage.getItem("userid_admin"),
      name:     name
    }).then(res => {
      ss.toast(res.title, res.type, res.message, null, "#16201d");
      if(res.status){
        input.value = '';
        input.classList.remove('pga-has-error');
        loadDepartments();
      }
    });
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
    // clear password error if auto-generated
    document.getElementById('pgaTechPassword').classList.remove('pga-has-error');
    document.getElementById('pgaTechPasswordError').classList.remove('pga-show');
  };

  // live clear on input
  ['pgaTechFname','pgaTechLname','pgaTechJobtitle','pgaTechUsername','pgaTechPassword'].forEach(id => {
    document.getElementById(id).addEventListener('input', function(){
      this.classList.remove('pga-has-error');
      document.getElementById(id + 'Error').classList.remove('pga-show');
    });
  });

  // live clear on input
  ['pgaTechEmpid'].forEach(id => {
    document.getElementById(id).addEventListener('input', function(){
      document.getElementById('pgaTechUsername').value = this.value
      this.classList.remove('pga-has-error');
      document.getElementById(id + 'Error').classList.remove('pga-show');
    });
  });

  document.getElementById('pgaCreateTechBtn').addEventListener('click', () => {
    const fname    = document.getElementById('pgaTechFname');
    const lname    = document.getElementById('pgaTechLname');
    const empid    = document.getElementById('pgaTechEmpid');
    const jobtitle    = document.getElementById('pgaTechJobtitle');
    const username = document.getElementById('pgaTechUsername');
    const password = document.getElementById('pgaTechPassword');

    // reset all errors
    [fname, lname, empid, jobtitle, username, password].forEach(el => el.classList.remove('pga-has-error'));
    ['pgaTechFnameError','pgaTechLnameError','pgaTechEmpidError','pgaTechJobtitleError','pgaTechUsernameError','pgaTechPasswordError'].forEach(id => {
      document.getElementById(id).classList.remove('pga-show');
    });

    let hasError = false;

    if(!fname.value.trim()){
      fname.classList.add('pga-has-error');
      document.getElementById('pgaTechFnameError').classList.add('pga-show');
      hasError = true;
    }
    if(!lname.value.trim()){
      lname.classList.add('pga-has-error');
      document.getElementById('pgaTechLnameError').classList.add('pga-show');
      hasError = true;
    }
    if(!empid.value.trim()){
      empid.classList.add('pga-has-error');
      document.getElementById('pgaTechEmpidError').classList.add('pga-show');
      hasError = true;
    }
    if(!jobtitle.value.trim()){
      jobtitle.classList.add('pga-has-error');
      document.getElementById('pgaTechJobtitleError').classList.add('pga-show');
      hasError = true;
    }
    if(!username.value.trim()){
      username.classList.add('pga-has-error');
      document.getElementById('pgaTechUsernameError').classList.add('pga-show');
      hasError = true;
    }
    if(!password.value.trim()){
      password.classList.add('pga-has-error');
      document.getElementById('pgaTechPasswordError').classList.add('pga-show');
      hasError = true;
    }

    if(hasError) return;

    sole.post("../../controllers/powerguard/administrator/create_technician.php", {
      fname:       fname.value.trim(),
      lname:       lname.value.trim(),
      email:       document.getElementById('pgaTechEmail').value.trim(),
      phone:       document.getElementById('pgaTechPhone').value.trim(),
      employee_id: empid.value.trim(),
      job_title:   jobtitle.value.trim(),
      username:    username.value.trim(),
      password:    password.value.trim()
    }).then(res => {
      ss.toast(res.title, res.type, res.message, null, "#16201d");
      if(res.status){
        ['pgaTechFname','pgaTechLname','pgaTechEmail','pgaTechPhone','pgaTechEmpid','pgaTechJobtitle','pgaTechUsername','pgaTechPassword'].forEach(id => {
          if(id == "pgaTechJobtitle"){
            document.getElementById(id).value = 'Technician, IT Support';
          }else{
            document.getElementById(id).value = '';
          }
        });
      }
    });
  });










































  /* ── MANAGE ACCOUNTS ── */
  let accounts         = [];
  let accountsFiltered = [];
  let pgaAccSearch     = '';
  let pgaAccPage     = 1;
  let pgaAccPageSize = 5;

  function loadAccounts(){
    sole.post("../../controllers/powerguard/administrator/get_accounts.php", {
      admin_id: localStorage.getItem("userid_admin")
    }).then(res => {
      accounts         = res.data || res || [];
      accountsFiltered = accounts;
      pgaAccSearch     = '';
      document.getElementById('pgaAccSearch').value = '';
      applyAccountsFilter();
    });
  }

  loadAccounts();

  document.querySelector('.pga-tab[data-pane="pgaManageAccounts"]').addEventListener('click', () => {
    loadAccounts();
  });

  function applyAccountsFilter(){
    const roleFilter   = document.getElementById('pgaAccFilterRole').value;
    const statusFilter = document.getElementById('pgaAccFilterStatus').value;
    const term         = pgaAccSearch.trim().toLowerCase();

    accountsFiltered = accounts
      .filter(a => roleFilter   === 'all' || a.privileges   === roleFilter)
      .filter(a => statusFilter === 'all' || a.account === statusFilter)
      .filter(a => {
        if(!term) return true;
        return (
          (a.fname     || '').toLowerCase().includes(term) ||
          (a.lname     || '').toLowerCase().includes(term) ||
          (a.email     || '').toLowerCase().includes(term) ||
          (a.employee_id     || '').toLowerCase().includes(term) ||
          (a.job_title || '').toLowerCase().includes(term) ||
          (a.privileges      || '').toLowerCase().includes(term)
        );
      });

    pgaAccPage = 1; // reset to first page on every filter/search change
    renderAccounts();
  }
  
  function accountButtons(a){
    let buttons = '';
    if(a.privileges != 'administrator'){
      buttons = `
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <button class="pga-btn pga-btn-sm" data-acc-action="edit" data-acc-id="${a.id}">Edit</button>
          <button class="pga-btn pga-btn-sm" data-acc-action="reset" data-acc-id="${a.id}">Reset password</button>
          <button class="pga-btn pga-btn-sm ${a.account==='active' ? 'pga-btn-reject' : 'pga-btn-success'}"
            data-acc-action="toggle" data-acc-id="${a.id}">
            ${a.account === 'active' ? 'Deactivate' : 'Reactivate'}
          </button>
          <button class="pga-btn pga-btn-sm pga-btn-reject" data-acc-action="delete" data-acc-id="${a.id}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            Delete
          </button>
        </div>`
    }else{
      buttons = `
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <button class="pga-btn pga-btn-sm" data-acc-action="edit" data-acc-id="${a.id}">Edit</button>
          <button class="pga-btn pga-btn-sm" data-acc-action="reset" data-acc-id="${a.id}">Reset password</button>
        </div>`
    }
    return buttons
  }

  function roleBadge(role){
    const map = {
      'supervisor': { cls:'pga-badge-blue',   label:'Supervisor' },
      'technician': { cls:'pga-badge-purple',  label:'Technician' },
      'administrator':      { cls:'pga-badge-amber',   label:'Administrator' }
    };
    const r = map[(role||'').toLowerCase()] || { cls:'pga-badge-gray', label: role };
    return `<span class="pga-badge ${r.cls}"><span class="pga-badge-dot"></span> ${r.label}</span>`;
  }

  function statusBadge(status){
    const map = {
      'active':      { cls:'pga-badge-green', label:'Active' },
      'deactivated': { cls:'pga-badge-gray',  label:'Deactivated' },
      'pending':     { cls:'pga-badge-amber', label:'Pending' }
    };
    const s = map[(status||'').toLowerCase()] || { cls:'pga-badge-gray', label: status };
    return `<span class="pga-badge ${s.cls}"><span class="pga-badge-dot"></span> ${s.label}</span>`;
  }

  function renderAccounts(){
    const tbody      = document.querySelector('#pgaAccountsTable tbody');
    const table      = document.getElementById('pgaAccountsTable');
    const pagination = document.getElementById('pgaAccountsPagination');
    tbody.innerHTML  = '';

    if(!accountsFiltered.length){
      const msg = pgaAccSearch ||
        document.getElementById('pgaAccFilterRole').value   !== 'all' ||
        document.getElementById('pgaAccFilterStatus').value !== 'all'
          ? 'No accounts match your search or filter.'
          : 'No accounts found.';

      table.style.display      = 'none';
      pagination.style.display = 'none';

      let emptyEl = document.getElementById('pgaAccountsEmpty');
      if(!emptyEl){
        emptyEl = document.createElement('div');
        emptyEl.id = 'pgaAccountsEmpty';
        emptyEl.style.cssText = 'text-align:center;padding:36px;color:var(--pga-ink-faint);font-size:13px;display:flex;flex-direction:column;align-items:center;gap:8px';
        table.parentNode.insertBefore(emptyEl, table.nextSibling);
      }
      emptyEl.style.display = 'flex';
      emptyEl.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px;opacity:.45"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
        ${msg}
      `;
      return;
    }

    // hide empty state, show table
    table.style.display = '';
    const emptyEl = document.getElementById('pgaAccountsEmpty');
    if(emptyEl) emptyEl.style.display = 'none';

    // pagination math
    const totalPages = Math.ceil(accountsFiltered.length / pgaAccPageSize);
    if(pgaAccPage > totalPages) pgaAccPage = totalPages;
    if(pgaAccPage < 1) pgaAccPage = 1;

    const start = (pgaAccPage - 1) * pgaAccPageSize;
    const items = accountsFiltered.slice(start, start + pgaAccPageSize);

    items.forEach(a => {
      const tr = document.createElement('tr');
      if(a.account === 'deactivated') tr.style.opacity = '0.55';
      tr.innerHTML = `
        <td>
          <div style="font-weight:600;margin-bottom:5px">${a.fname[0]}. ${a.lname}</div>
          ${roleBadge(a.privileges)}
        </td>
        <td style="color:var(--pga-ink-soft)">${a.employee_id}</td>
        <td style="color:var(--pga-ink-soft)">${a.email != '-' ? a.email : '—'}</td>
        <td>${statusBadge(a.account)}</td>
        <td>
          ${accountButtons(a)}
        </td>
      `;
      tbody.appendChild(tr);
    });

    // pagination controls
    const infoEl   = document.getElementById('pgaAccountsPaginationInfo');
    const pageNums = document.getElementById('pgaAccountsPageNums');
    const prevBtn  = document.getElementById('pgaAccountsPrev');
    const nextBtn  = document.getElementById('pgaAccountsNext');

    pagination.style.display = 'flex';
    const end = Math.min(start + pgaAccPageSize, accountsFiltered.length);
    infoEl.textContent = `Showing ${start + 1}–${end} of ${accountsFiltered.length}`;

    pageNums.innerHTML = '';
    for(let i = 1; i <= totalPages; i++){
      const btn = document.createElement('button');
      btn.className = 'pgs-page-num' + (i === pgaAccPage ? ' pgs-page-active' : '');
      btn.textContent = i;
      btn.addEventListener('click', () => { pgaAccPage = i; renderAccounts(); });
      pageNums.appendChild(btn);
    }

    prevBtn.disabled = pgaAccPage <= 1;
    nextBtn.disabled = pgaAccPage >= totalPages;
  }

  /* ── ACCOUNTS TABLE — event delegation (no onclick) ── */
  document.getElementById('pgaAccountsTable').addEventListener('click', function(e){
    const btn = e.target.closest('[data-acc-action]');
    if(!btn) return;

    const action = btn.dataset.accAction;
    const id     = parseInt(btn.dataset.accId);
    const a      = accounts.find(acc => acc.id === id);
    if(!a) return;

    if(action === 'edit')   openEditAccount(id);
    if(action === 'reset')  openResetPassword(id);

    if(action === 'toggle'){
      a.account = a.account === 'active' ? 'deactivated' : 'active';
      sole.post("../../controllers/powerguard/administrator/toggle_account_status.php", {
        admin_id: localStorage.getItem("userid_admin"),
        user_id:  id,
        status:   a.account
      }).then(res => {
        ss.toast(res.title, res.type, res.message, null, "#16201d");
        if(res.status) loadAccounts();
      });
    }

    if(action === 'delete'){
      sole.post("../../controllers/powerguard/administrator/find_user_account.php", {
        user_id : id
      }).then(res => {
        let text = "This action is permanent and cannot be undone."
        if(res.status){
          text = res.privileges == "supervisor" ? `All departments assigned to ${a.fname} ${a.lname} will become vacant. You can reassign a new supervisor, but please note that deleting this account is permanent and cannot be undone. ` : `All workstations claimed by ${a.fname} ${a.lname} will be marked as unclaimed. Other technicians can now claim them, or you can assign the workstations yourself. Please note that this action is permanent and cannot be undone.`
        }
        Swal.fire({
          title: `Delete ${a.fname} ${a.lname}?`,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          confirmButtonText: 'Delete',
          customClass: { popup:'my-custom-popup', actions:'my-right-buttons' }
        }).then(result => {
          if(!result.isConfirmed) return;
          sole.post("../../controllers/powerguard/administrator/delete_account.php", {
            admin_id: localStorage.getItem("userid_admin"),
            user_id:  id
          }).then(res => {
            ss.toast(res.title, res.type, res.message, null, "#16201d");
            if(res.status) loadAccounts();
          });
        });  
      })

    }
  });

  /* ── FILTER / SEARCH CONTROLS ── */
  document.getElementById('pgaAccFilterRole').addEventListener('change', applyAccountsFilter);
  document.getElementById('pgaAccFilterStatus').addEventListener('change', applyAccountsFilter);

  document.getElementById('pgaAccRowsPerPage').addEventListener('change', function(){
    pgaAccPageSize = parseInt(this.value);
    pgaAccPage = 1;
    applyAccountsFilter();
  });

  document.getElementById('pgaAccountsPrev').addEventListener('click', () => {
    if(pgaAccPage > 1){ pgaAccPage--; renderAccounts(); }
  });

  document.getElementById('pgaAccountsNext').addEventListener('click', () => {
    if(pgaAccPage < Math.ceil(accountsFiltered.length / pgaAccPageSize)){ pgaAccPage++; renderAccounts(); }
  });

  let pgaAccSearchDebounce;
  document.getElementById('pgaAccSearch').addEventListener('input', function(){
    clearTimeout(pgaAccSearchDebounce);
    const val = this.value;
    pgaAccSearchDebounce = setTimeout(() => {
      pgaAccSearch = val;
      applyAccountsFilter();
    }, 200);
  });

  /* ── EDIT ACCOUNT ── */
  let _editingId = null;

  window.openEditAccount = function(id){
    const a = accounts.find(acc => acc.id === id);
    if(!a) return;
    _editingId = id;
    document.getElementById('pgaEditAccName').textContent  = a.fname + ' ' + a.lname;
    document.getElementById('pgaEditFname').value          = a.fname  || '';
    document.getElementById('pgaEditLname').value          = a.lname  || '';
    document.getElementById('pgaEditEmail').value          = a.email != '-' ? a.email : '';
    document.getElementById('pgaEditPhone').value          = a.phone != '-' ? a.phone : '';
    document.getElementById('pgaEditEmpid').value          = a.employee_id  || '';
    document.getElementById('pgaEditJobtitle').value       = a.job_title || '';
    document.getElementById('pgaResetPwPanel').style.display  = 'none';
    const panel = document.getElementById('pgaEditAccountPanel');
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeEditAccount = function(){
    document.getElementById('pgaEditAccountPanel').style.display = 'none';
    _editingId = null;
  };

  // live-clear listeners
  ['pgaEditFname','pgaEditLname','pgaEditEmpid','pgaEditJobtitle'].forEach(id => {
    document.getElementById(id).addEventListener('input', function(){
      this.classList.remove('pga-has-error');
      document.getElementById(id + 'Error').classList.remove('pga-show');
    });
  });

  window.saveEditAccount = function(){
    if(_editingId === null) return;

    const fname    = document.getElementById('pgaEditFname');
    const lname    = document.getElementById('pgaEditLname');
    const empid    = document.getElementById('pgaEditEmpid');
    const jobtitle = document.getElementById('pgaEditJobtitle');

    // reset errors
    [fname, lname, empid, jobtitle].forEach(el => el.classList.remove('pga-has-error'));
    ['pgaEditFnameError','pgaEditLnameError','pgaEditEmpidError','pgaEditJobtitleError'].forEach(id => {
      document.getElementById(id).classList.remove('pga-show');
    });

    let hasError = false;

    if(!fname.value.trim()){
      fname.classList.add('pga-has-error');
      document.getElementById('pgaEditFnameError').classList.add('pga-show');
      hasError = true;
    }
    if(!lname.value.trim()){
      lname.classList.add('pga-has-error');
      document.getElementById('pgaEditLnameError').classList.add('pga-show');
      hasError = true;
    }
    if(!empid.value.trim()){
      empid.classList.add('pga-has-error');
      document.getElementById('pgaEditEmpidError').classList.add('pga-show');
      hasError = true;
    }
    if(!jobtitle.value.trim()){
      jobtitle.classList.add('pga-has-error');
      document.getElementById('pgaEditJobtitleError').classList.add('pga-show');
      hasError = true;
    }

    if(hasError) return;

    sole.post("../../controllers/powerguard/administrator/update_account.php", {
      admin_id:  localStorage.getItem("userid_admin"),
      user_id:   _editingId,
      fname:     fname.value.trim(),
      lname:     lname.value.trim(),
      email:     document.getElementById('pgaEditEmail').value.trim(),
      phone:     document.getElementById('pgaEditPhone').value.trim(),
      empid:     empid.value.trim(),
      job_title: jobtitle.value.trim()
    }).then(res => {
      ss.toast(res.title, res.type, res.message, null, "#16201d");
      if(res.status){
        closeEditAccount();
        loadAccounts();
      }
    });
  };

  /* ── RESET PASSWORD ── */
  let _resetId = null;

  window.openResetPassword = function(id){
    const a = accounts.find(acc => acc.id === id);
    if(!a) return;
    _resetId = id;
    if(a.privileges == "administrator"){
      if(a.id == localStorage.getItem("userid_admin")) ss.toast("Warning!","warning","You are about to reset your administrator account password. Please be aware that you will lose access to the system if this password is forgotten.","I understand","#16201d")
      if(a.id != localStorage.getItem("userid_admin")) ss.toast("Warning!","warning","You are about to reset this administrator account password. Please be aware that the user will lose access to the system if they forget their password.","I understand","#16201d")
    }
    document.getElementById('pgaResetPwName').textContent    = a.fname + ' ' + a.lname;
    document.getElementById('pgaResetPwValue').value         = '';
    document.getElementById('pgaEditAccountPanel').style.display = 'none';
    const panel = document.getElementById('pgaResetPwPanel');
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior:'smooth', block:'start' });
  };

  window.closeResetPassword = function(){
    document.getElementById('pgaResetPwPanel').style.display = 'none';
    _resetId = null;
  };

  document.getElementById('pgaResetPwValue').addEventListener('input', function(){
    this.classList.remove('pga-has-error');
  });

  window.generateResetPassword = function(){
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
    let pw = '';
    for(let i = 0; i < 12; i++){
      pw += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const input = document.getElementById('pgaResetPwValue');
    input.value = pw;
    input.classList.remove('pga-has-error');
  };

  window.confirmResetPassword = function(){
    const input   = document.getElementById('pgaResetPwValue');

    input.classList.remove('pga-has-error');

    if(!input.value.trim()){
      input.classList.add('pga-has-error');
      return;
    }

    sole.post("../../controllers/powerguard/administrator/reset_password.php", {
      admin_id: localStorage.getItem("userid_admin"),
      user_id:  _resetId,
      password: input.value.trim()
    }).then(res => {
      ss.toast(res.title, res.type, res.message, null, "#16201d");
      if(res.status) closeResetPassword();
    });
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
          localStorage.removeItem("userid_admin")
          localStorage.removeItem("login_admin")
          window.location.replace("signin.php");
        }
    });
  });





  document.querySelector('.pga-tab[data-pane="pgaDepartments"]').addEventListener('click', () => {
    loadDepartments();
    loadApprovals();
  });
  document.querySelector('.pga-tab[data-pane="pgaApprovals"]').addEventListener('click', () => {
    loadApprovals();
  });
    document.querySelector('.pga-tab[data-pane="pgaReports"]').addEventListener('click', () => {
    ss.toast("Reports Unavailable","info","Gereration of reports will be available soon.",null,"#16201d")
  });

})();

document.getElementsByClassName("pga-avatar")[0].innerText = localStorage.getItem("pga_avatar")
document.getElementsByClassName("pga-name")[0].innerText = localStorage.getItem("pga_name")
document.getElementsByClassName("pga-role-label")[0].innerText = localStorage.getItem("pga_role")
