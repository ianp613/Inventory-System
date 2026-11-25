(() => {
'use strict';

/* ================================================================== helpers */
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const CSRF = $('meta[name=csrf]').content;
let timers = [];

async function api(action, { params = {}, body = null } = {}) {
  const qs = new URLSearchParams({ action, ...params });
  const opt = { credentials: 'same-origin', headers: { Accept: 'application/json' } };
  if (body !== null) {
    opt.method = 'POST';
    opt.headers['X-CSRF-Token'] = CSRF;
    if (body instanceof FormData) opt.body = body;
    else { opt.headers['Content-Type'] = 'application/json'; opt.body = JSON.stringify(body); }
  }
  const r = await fetch('api.php?' + qs, opt);
  let j;
  try { j = await r.json(); } catch { throw new Error('Unexpected server response'); }
  if (r.status === 401) { location.reload(); throw new Error('Signed out'); }
  if (!r.ok || j.error) throw new Error(j.error || 'HTTP ' + r.status);
  return j;
}

function toast(msg, type = '') {
  const t = document.createElement('div');
  t.className = 'toast ' + type;
  t.textContent = msg;
  $('#toasts').append(t);
  setTimeout(() => t.remove(), type === 'error' ? 6000 : 3000);
}
const fail = e => toast(e.message || String(e), 'error');

function modal({ title, html, wide = false, buttons = [{ label: 'Close', value: false }] }) {
  return new Promise(resolve => {
    const bg = document.createElement('div');
    bg.className = 'modal-bg';
    bg.innerHTML = `<div class="modal ${wide ? 'wide' : ''}" role="dialog"><h2>${esc(title)}</h2><div class="mbody">${html}</div>
      <div class="foot">${buttons.map((b, i) => `<button class="btn ${b.cls || ''}" data-i="${i}">${esc(b.label)}</button>`).join('')}</div></div>`;
    $('#modal-root').append(bg);
    const done = v => { bg.remove(); resolve(v); };
    bg.addEventListener('click', e => {
      if (e.target === bg) return done(false);
      const b = e.target.closest('button[data-i]');
      if (!b) return;
      const btn = buttons[+b.dataset.i];
      done(typeof btn.value === 'function' ? btn.value(bg) : btn.value);
    });
    bg.querySelector('input,select')?.focus();
  });
}
const confirmBox = (title, msg, ok = 'Confirm', danger = false) =>
  modal({ title, html: `<p>${msg}</p>`, buttons: [{ label: 'Cancel', value: false }, { label: ok, value: true, cls: danger ? 'danger' : 'primary' }] });

function fmtDur(sec) {
  sec = Math.max(0, Math.floor(sec));
  const d = Math.floor(sec / 86400), h = Math.floor(sec % 86400 / 3600), m = Math.floor(sec % 3600 / 60), s = sec % 60;
  return [d && d + 'd', (d || h) && h + 'h', (d || h || m) && m + 'm', !d && s + 's'].filter(Boolean).join(' ');
}
function fmtMins(m) {
  if (m % 1440 === 0) return `${m / 1440} day${m === 1440 ? '' : 's'}`;
  if (m % 60 === 0) return `${m / 60} hour${m === 60 ? '' : 's'}`;
  return `${m} min`;
}
const fmtDate = ts => ts ? new Date(ts * 1000).toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' }) : '—';
const expLabel = s => s == null ? '—' : s >= 80 ? 'Excellent' : s >= 60 ? 'Good' : s >= 40 ? 'Fair' : 'Poor';
const typeLabel = v => v.type === 'single' ? 'Single-use' : v.type === 'multiple' ? 'Multiple-use' : 'Unlimited';
const STATUS = { unused: ['Unused', ''], in_use: ['In use', 'green'], partial: ['Partly used', 'blue'], used: ['Used up', 'red'] };
const badge = (text, cls = '') => `<span class="badge ${cls}">${esc(text)}</span>`;

async function copyText(t) {
  try { await navigator.clipboard.writeText(t); }
  catch { const ta = document.createElement('textarea'); ta.value = t; document.body.append(ta); ta.select(); document.execCommand('copy'); ta.remove(); }
  toast('Copied', 'ok');
}

function startTicker() { // live countdown for [data-end]
  const tick = () => $$('[data-end]').forEach(el => {
    const left = +el.dataset.end - Date.now() / 1000;
    el.textContent = left > 0 ? fmtDur(left) : 'ended';
  });
  timers.push(setInterval(tick, 1000));
  tick();
}

function makeSortable(table, state, rerender) {
  $$('th.sortable', table).forEach(th => {
    th.classList.toggle('asc', state.key === th.dataset.k && state.dir === 1);
    th.classList.toggle('desc', state.key === th.dataset.k && state.dir === -1);
    th.onclick = () => { state.dir = state.key === th.dataset.k ? -state.dir : 1; state.key = th.dataset.k; rerender(); };
  });
}
function sortRows(rows, { key, dir }) {
  return [...rows].sort((a, b) => {
    let x = a[key], y = b[key];
    if (x == null) x = typeof y === 'number' ? -1 : '';
    if (y == null) y = typeof x === 'number' ? -1 : '';
    return (typeof x === 'number' && typeof y === 'number' ? x - y : String(x).localeCompare(String(y), undefined, { numeric: true, sensitivity: 'base' })) * dir;
  });
}

/* ================================================================== dashboard */
async function pageDashboard(view) {
  view.innerHTML = `<h1>Dashboard</h1><p class="sub">Live overview across all networks.</p><div id="dash"><p class="muted">Loading…</p></div>`;
  const render = async () => {
    const d = await api('dashboard');
    const v = d.vouchers;
    const stat = (n, l) => `<div class="stat"><div class="n">${n}</div><div class="l">${l}</div></div>`;
    $('#dash').innerHTML = `
      <div class="grid stats">
        ${stat(d.active_sessions, 'Active voucher sessions')}
        ${stat(`<span id="d-online">…</span>`, 'Clients online now')}
        ${stat(d.in_cooldown, 'Devices in cooldown')}
        ${stat(d.redeemed_today, 'Vouchers redeemed today')}
        ${stat(v.unused, 'Unused vouchers')}
        ${stat(d.unique_devices, 'Unique devices (all time)')}
      </div>
      <div class="grid two">
        <div class="card"><h2>Redemptions – last 14 days</h2>${chart(d.days)}</div>
        <div class="card"><h2>Vouchers</h2>
          <table><tbody>
            <tr><td>Total</td><td><b>${v.total}</b></td></tr>
            <tr><td>${badge('Unused')}</td><td>${v.unused}</td></tr>
            <tr><td>${badge('In use', 'green')}</td><td>${v.in_use}</td></tr>
            <tr><td>${badge('Partly used', 'blue')}</td><td>${v.partial}</td></tr>
            <tr><td>${badge('Used up', 'red')}</td><td>${v.used}</td></tr>
          </tbody></table></div>
      </div>
      <div class="card"><h2>Networks</h2><div class="tablewrap"><table>
        <thead><tr><th>Network</th><th>Controller</th><th>Wireless clients</th><th>Guests</th><th>APs</th><th>Active sessions</th><th>Redeemed 24h</th></tr></thead>
        <tbody>${d.networks.map(n => `<tr>
          <td><b>${esc(n.name)}</b><div class="muted mono">site: ${esc(n.site_id)}</div></td>
          <td><span class="dot" id="dot-${n.id}"></span><span id="st-${n.id}" class="muted">${n.enabled ? 'checking…' : 'disabled'}</span></td>
          <td id="cl-${n.id}">–</td><td id="gu-${n.id}">–</td><td id="ap-${n.id}">–</td>
          <td>${n.active_sessions}</td><td>${n.redeemed_24h}</td></tr>`).join('')}</tbody></table></div></div>
      <div class="card"><h2>Recent activity</h2><ul class="feed">${d.activity.map(a =>
        `<li><b>${esc(a.action.replace(/_/g, ' '))}</b> ${a.mac ? `<span class="mono">${esc(a.mac)}</span>` : ''} ${esc(a.detail || '')}
          <div class="t">${fmtDate(a.ts)}${a.network_name ? ' · ' + esc(a.network_name) : ''}</div></li>`).join('') || '<li class="muted">Nothing yet.</li>'}</ul></div>`;
    let online = 0, pending = d.networks.filter(n => n.enabled).length;
    d.networks.filter(n => n.enabled).forEach(async n => {
      let r;
      try { r = await api('network_status', { params: { id: n.id } }); } catch (e) { r = { ok: false, error: e.message }; }
      if (!$('#st-' + n.id)) return;
      $('#dot-' + n.id).classList.add(r.ok ? 'on' : 'off');
      $('#st-' + n.id).textContent = r.ok ? `Online (${r.ms} ms)` : r.error;
      if (r.ok) { $('#cl-' + n.id).textContent = r.clients; $('#gu-' + n.id).textContent = r.guests; $('#ap-' + n.id).textContent = r.aps; online += r.clients; }
      if (--pending === 0 && $('#d-online')) $('#d-online').textContent = online;
    });
    if (!pending) $('#d-online').textContent = 0;
  };
  await render();
  timers.push(setInterval(() => render().catch(() => {}), 60000));
}

function chart(days) {
  const keys = Object.keys(days), vals = Object.values(days), max = Math.max(1, ...vals);
  const W = 640, H = 190, pl = 26, pb = 22, pt = 14, bw = (W - pl) / keys.length;
  const bars = keys.map((k, i) => {
    const h = (H - pb - pt) * vals[i] / max, x = pl + i * bw + 4;
    return `<rect class="bar" x="${x}" y="${H - pb - h}" width="${bw - 8}" height="${Math.max(h, vals[i] ? 2 : 0)}" rx="3"><title>${k}: ${vals[i]}</title></rect>
      ${i % 2 === 0 || keys.length < 8 ? `<text x="${x + (bw - 8) / 2}" y="${H - 6}" text-anchor="middle">${k.slice(5)}</text>` : ''}`;
  }).join('');
  return `<svg class="chart" viewBox="0 0 ${W} ${H}" preserveAspectRatio="none"><line x1="${pl}" x2="${W}" y1="${H - pb}" y2="${H - pb}" stroke="#d1d5db"/>
    <text x="0" y="${pt + 4}">${max}</text><text x="0" y="${H - pb}">0</text>${bars}</svg>`;
}

/* ================================================================== vouchers */
async function pageVouchers(view) {
  const nets = (await api('networks')).networks;
  const vs = { page: 1, q: '', status: '', type: '', name: '', per: 25 };
  let selected = new Set(), current = [], lastCreated = null;

  view.innerHTML = `<h1>Vouchers</h1><p class="sub">Create and manage voucher codes for your captive portal.</p>
  <div class="card"><h2>Create vouchers</h2>
    <form id="vf">
      <div class="row"><div><label>Voucher Name</label><input name="name" maxlength="80" required></div>
        <div class="narrow"><label>Amount</label><input name="amount" type="number" min="1" max="1000" value="1" required></div></div>
      <div class="radios">
        <label><input type="radio" name="type" value="single" checked> Single-use</label>
        <label><input type="radio" name="type" value="multiple"> Multiple-use</label>
        <label><input type="radio" name="type" value="unlimited"> Unlimited</label></div>
      <div class="hint" id="type-hint" style="margin:-8px 0 14px"></div>
      <div class="row"><div><label>Usage Time</label><input name="usage_time" type="number" min="1" required></div>
        <div><label>Unit</label><select name="unit"><option value="minutes">Minutes</option><option value="hours">Hours</option><option value="days">Days</option></select></div>
        <div id="mu" class="hidden"><label>Max uses per voucher</label><input name="max_uses" type="number" min="2" value="5"></div>
        <div><label>Valid on</label><select name="network_id"><option value="">All networks</option>${nets.map(n => `<option value="${n.id}">${esc(n.name)}</option>`).join('')}</select></div></div>
      <button class="btn primary" type="submit">Create</button>
    </form>
    <div id="created"></div></div>
  <div class="card"><h2>All vouchers</h2>
    <div class="toolbar">
      <div class="grow"><label>Search</label><input id="q" placeholder="Code or name"></div>
      <div><label>Name</label><select id="fname"></select></div>
      <div><label>Status</label><select id="fstatus"><option value="">All</option><option value="unused">Unused</option><option value="in_use">In use</option><option value="partial">Partly used</option><option value="used">Used up</option></select></div>
      <div><label>Type</label><select id="ftype"><option value="">All</option><option value="single">Single-use</option><option value="multiple">Multiple-use</option><option value="unlimited">Unlimited</option></select></div>
    </div>
    <div class="actions" style="margin-bottom:12px">
      <button class="btn sm" id="b-print">Print selected</button><button class="btn sm" id="b-csv">Export CSV</button>
      <button class="btn sm danger" id="b-del">Delete selected</button><span class="muted" id="selinfo" style="align-self:center"></span></div>
    <div class="tablewrap"><table><thead><tr><th><input type="checkbox" id="all"></th><th>Code</th><th>Name</th><th>Type</th><th>Duration</th><th>Uses</th><th>Status</th><th>Valid on</th><th>Created</th><th></th></tr></thead>
    <tbody id="vbody"><tr><td colspan="10" class="empty">Loading…</td></tr></tbody></table></div>
    <div class="pager"><span class="muted" id="pinfo"></span><button class="btn sm" id="prev">Previous</button><button class="btn sm" id="next">Next</button></div></div>`;

  const f = $('#vf');
  const hints = { single: 'One device can use the code once. Time starts when it is redeemed.', multiple: 'Up to “Max uses” different devices can each redeem the code once; every device gets its own timer.', unlimited: 'Any number of devices can use the code; each device is still held back by the cooldown.' };
  const syncType = () => { const t = f.type.value; $('#mu').classList.toggle('hidden', t !== 'multiple'); $('#type-hint').textContent = hints[t]; };
  f.addEventListener('change', syncType); syncType();

  f.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(f));
    try {
      const r = await api('voucher_create', { body: { ...fd, network_id: fd.network_id || null } });
      lastCreated = r;
      $('#created').innerHTML = `<div class="sect"><div class="alert ok">Created ${r.created.length} voucher${r.created.length > 1 ? 's' : ''} “${esc(r.name)}” (${fmtMins(r.minutes)}).</div>
        <div class="codes">${r.created.slice(0, 60).map(c => `<span>${c.code_fmt}</span>`).join('')}</div>${r.created.length > 60 ? `<p class="muted">…and ${r.created.length - 60} more in the list below.</p>` : ''}
        <div class="actions"><button class="btn sm" id="c-copy">Copy all</button><button class="btn sm" id="c-print">Print</button></div></div>`;
      $('#c-copy').onclick = () => copyText(r.created.map(c => c.code_fmt).join('\n'));
      $('#c-print').onclick = () => printVouchers(r.created.map(c => ({ code_fmt: c.code_fmt, name: r.name, type: r.type, duration_minutes: r.minutes })));
      f.name.value = ''; vs.page = 1; load();
    } catch (err) { fail(err); }
  });

  async function load() {
    try {
      const r = await api('vouchers', { params: vs });
      current = r.rows;
      const sel = $('#fname');
      sel.innerHTML = `<option value="">All</option>` + r.names.map(n => `<option ${n === vs.name ? 'selected' : ''}>${esc(n)}</option>`).join('');
      $('#vbody').innerHTML = r.rows.map(v => {
        const [sl, sc] = STATUS[v.status];
        const uses = v.type === 'single' ? `${v.uses}/1` : v.type === 'multiple' ? `${v.uses}/${v.max_uses}` : `${v.uses}`;
        return `<tr><td><input type="checkbox" class="sel" data-id="${v.id}" ${selected.has(v.id) ? 'checked' : ''}></td>
          <td class="mono"><b>${v.code_fmt}</b></td><td>${esc(v.name)}</td><td>${typeLabel(v)}</td><td>${fmtMins(v.duration_minutes)}</td><td>${uses}</td>
          <td>${badge(sl, sc)}</td><td>${esc(v.network_name || 'All')}</td><td>${fmtDate(v.created_at)}</td>
          <td><button class="btn sm" data-detail="${v.id}">Details</button></td></tr>`;
      }).join('') || `<tr><td colspan="10" class="empty">No vouchers found.</td></tr>`;
      $('#pinfo').textContent = `${r.total} voucher${r.total === 1 ? '' : 's'} · page ${r.page}/${r.pages}`;
      $('#prev').disabled = r.page <= 1; $('#next').disabled = r.page >= r.pages;
      updSel();
    } catch (e) { fail(e); }
  }
  const updSel = () => { $('#selinfo').textContent = selected.size ? `${selected.size} selected` : ''; };
  let qt; $('#q').oninput = e => { clearTimeout(qt); qt = setTimeout(() => { vs.q = e.target.value; vs.page = 1; load(); }, 250); };
  $('#fname').onchange = e => { vs.name = e.target.value; vs.page = 1; load(); };
  $('#fstatus').onchange = e => { vs.status = e.target.value; vs.page = 1; load(); };
  $('#ftype').onchange = e => { vs.type = e.target.value; vs.page = 1; load(); };
  $('#prev').onclick = () => { vs.page--; load(); };
  $('#next').onclick = () => { vs.page++; load(); };
  $('#all').onchange = e => { $$('.sel').forEach(c => { c.checked = e.target.checked; e.target.checked ? selected.add(+c.dataset.id) : selected.delete(+c.dataset.id); }); updSel(); };
  $('#vbody').addEventListener('change', e => { if (e.target.classList.contains('sel')) { e.target.checked ? selected.add(+e.target.dataset.id) : selected.delete(+e.target.dataset.id); updSel(); } });
  $('#vbody').addEventListener('click', async e => {
    const b = e.target.closest('[data-detail]'); if (!b) return;
    try {
      const r = await api('voucher_detail', { params: { id: b.dataset.detail } });
      modal({ title: `Voucher ${r.voucher.code_fmt}`, wide: true, html: `<p><b>${esc(r.voucher.name)}</b> · ${typeLabel(r.voucher)} · ${fmtMins(r.voucher.duration_minutes)}</p>
        <div class="tablewrap"><table><thead><tr><th>Device</th><th>Network</th><th>Started</th><th>Ends</th><th>Status</th></tr></thead><tbody>
        ${r.sessions.map(s => `<tr><td class="mono">${s.mac}</td><td>${esc(s.network_name || '')}</td><td>${fmtDate(s.started_at)}</td><td>${fmtDate(s.expires_at)}</td><td>${badge(s.status, s.status === 'active' ? 'green' : '')}</td></tr>`).join('') || '<tr><td colspan="5" class="empty">Not used yet.</td></tr>'}
        </tbody></table></div>` });
    } catch (err) { fail(err); }
  });
  $('#b-csv').onclick = () => { location.href = 'api.php?' + new URLSearchParams({ action: 'vouchers_export', ...vs }); };
  $('#b-print').onclick = async () => {
    if (!selected.size) return toast('Select vouchers first', 'error');
    const r = await api('vouchers', { params: { per: 200 } });
    printVouchers(r.rows.filter(v => selected.has(v.id)));
  };
  $('#b-del').onclick = async () => {
    if (!selected.size) return toast('Select vouchers first', 'error');
    if (!await confirmBox('Delete vouchers', `Delete ${selected.size} voucher(s)? Devices that are already using them keep their running time.`, 'Delete', true)) return;
    try { await api('voucher_delete', { body: { ids: [...selected] } }); selected.clear(); toast('Deleted', 'ok'); load(); } catch (e) { fail(e); }
  };
  load();
}

function printVouchers(list) {
  const w = window.open('', '_blank');
  if (!w) return toast('Allow pop-ups to print', 'error');
  w.document.write(`<!doctype html><title>Vouchers</title><style>body{font-family:system-ui,sans-serif;margin:16px}
    .g{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.c{border:1.5px dashed #555;border-radius:8px;padding:12px;text-align:center;break-inside:avoid}
    .code{font:700 22px ui-monospace,Menlo,monospace;letter-spacing:.08em;margin:6px 0}.s{font-size:12px;color:#444}</style><div class="g">
    ${list.map(v => `<div class="c"><div class="s">${esc(v.name)}</div><div class="code">${esc(v.code_fmt)}</div>
      <div class="s">${fmtMins(v.duration_minutes)} · ${v.type === 'single' ? 'Single-use' : v.type === 'multiple' ? 'Multiple-use' : 'Unlimited'}</div></div>`).join('')}</div>
    <script>setTimeout(function(){print()},300)<\/script>`);
  w.document.close();
}

/* ================================================================== users */
async function pageUsers(view) {
  const nets = (await api('networks')).networks.filter(n => n.enabled);
  const st = { net: 'all', ssid: '', status: '', q: '', sort: { key: 'name', dir: 1 }, rows: [], errors: [] };
  view.innerHTML = `<h1>User List</h1><p class="sub">Connected devices and voucher sessions. Unauthorize a connection, reconnect a user, or reset their cooldown.</p>
  <div class="card">
    <div class="toolbar">
      <div><label>Site / Network</label><select id="unet"><option value="all">All networks</option>${nets.map(n => `<option value="${n.id}">${esc(n.name)}</option>`).join('')}</select></div>
      <div><label>SSID</label><select id="ussid"><option value="">All SSIDs</option></select></div>
      <div><label>Show</label><select id="ustatus"><option value="">All devices</option><option value="guest">Portal / guest devices</option><option value="active">Active vouchers</option><option value="cooldown">In cooldown</option></select></div>
      <div class="grow"><label>Search</label><input id="uq" placeholder="Name, MAC, IP, voucher"></div>
      <div><button class="btn" id="urefresh">Refresh</button></div></div>
    <div id="uerrs"></div>
    <div class="tablewrap"><table id="utable"></table></div>
    <p class="muted" id="ucount" style="margin:12px 0 0"></p></div>`;

  async function load() {
    $('#ucount').textContent = 'Loading…';
    const targets = st.net === 'all' ? nets : nets.filter(n => String(n.id) === st.net);
    const res = await Promise.allSettled(targets.map(n => api('users', { params: { network_id: n.id } })));
    st.rows = []; st.errors = [];
    res.forEach((r, i) => {
      if (r.status === 'fulfilled') { st.rows.push(...r.value.rows); if (r.value.error) st.errors.push(`${targets[i].name}: ${r.value.error}`); }
      else st.errors.push(`${targets[i].name}: ${r.reason.message}`);
    });
    st.rows.forEach(r => { r._end = r.time_left != null ? Date.now() / 1000 + r.time_left : null; r._cd = r.cooldown_left != null ? Date.now() / 1000 + r.cooldown_left : null; });
    const ssids = [...new Set(st.rows.map(r => r.ssid).filter(Boolean))].sort();
    $('#ussid').innerHTML = `<option value="">All SSIDs</option>` + ssids.map(s => `<option ${s === st.ssid ? 'selected' : ''}>${esc(s)}</option>`).join('');
    render();
  }

  function render() {
    $('#uerrs').innerHTML = st.errors.map(e => `<div class="alert error">${esc(e)}</div>`).join('');
    const q = st.q.toLowerCase();
    let rows = st.rows.filter(r => (!st.ssid || r.ssid === st.ssid)
      && (!st.status || (st.status === 'guest' ? r.is_guest || r.has_session : r.state === st.status))
      && (!q || [r.name, r.mac, r.ip, r.voucher, r.voucher_name].some(x => String(x || '').toLowerCase().includes(q))));
    const key = st.sort.key;
    rows = sortRows(rows.map(r => ({ ...r, _sat: r.satisfaction, _up: r.uptime })), { key: { experience: '_sat', uptime: '_up', time: 'time_left' }[key] || key, dir: st.sort.dir });
    const multi = st.net === 'all' && nets.length > 1;
    const th = (k, l) => `<th class="sortable" data-k="${k}">${l}</th>`;
    $('#utable').innerHTML = `<thead><tr>${th('name', 'Name')}${th('mac', 'MAC')}${multi ? th('network', 'Network') : ''}${th('ssid', 'Connection')}${th('experience', 'Experience')}${th('ip', 'IP Address')}${th('uptime', 'Uptime')}${th('time', 'Voucher / Time')}<th>Actions</th></tr></thead>
      <tbody>${rows.map(r => {
        const exp = r.online ? expLabel(r.satisfaction) : 'Offline';
        let status = '<span class="muted">—</span>';
        if (r.state === 'active') status = `${badge('Active', 'green')} <span data-end="${r._end}"></span><div class="muted mono">${esc(r.voucher)}</div>`;
        else if (r.state === 'cooldown') status = `${badge('Cooldown', 'amber')} <span data-end="${r._cd}"></span>`;
        else if (r.revoked) status = badge('Unauthorized', 'red');
        else if (r.is_guest && r.authorized) status = badge('Guest (no voucher)', 'blue');
        const a = [];
        if (r.online && r.is_guest) a.push(`<button class="btn sm danger" data-act="unauth">Unauthorize</button>`); // only for guest-WiFi / guest SSID connections
        a.push(`<button class="btn sm" data-act="reconnect">Reconnect</button>`);                                    // everyone
        if (r.state === 'cooldown') a.push(`<button class="btn sm" data-act="reset">Reset cooldown</button>`);
        return `<tr data-mac="${r.mac}" data-net="${r.network_id}"><td>${esc(r.name)}</td><td class="mono">${r.mac}</td>${multi ? `<td>${esc(r.network)}</td>` : ''}
          <td>${esc(r.ssid || '—')}</td><td class="exp-${exp}">${exp}</td><td>${esc(r.ip || '—')}</td><td>${r.online && r.uptime ? fmtDur(r.uptime) : '—'}</td>
          <td>${status}</td><td><div class="actions">${a.join('') || '<span class="muted">—</span>'}</div></td></tr>`;
      }).join('') || `<tr><td colspan="9" class="empty">No devices match.</td></tr>`}</tbody>`;
    makeSortable($('#utable'), st.sort, render);
    $('#ucount').textContent = `${rows.length} device${rows.length === 1 ? '' : 's'}`;
    startTickerOnce();
  }
  let tickerOn = false;
  const startTickerOnce = () => { if (!tickerOn) { tickerOn = true; startTicker(); } };

  $('#unet').onchange = e => { st.net = e.target.value; st.ssid = ''; load(); };
  $('#ussid').onchange = e => { st.ssid = e.target.value; render(); };
  $('#ustatus').onchange = e => { st.status = e.target.value; render(); };
  $('#uq').oninput = e => { st.q = e.target.value; render(); };
  $('#urefresh').onclick = async e => {
    const b = e.currentTarget;
    b.disabled = true; b.innerHTML = '<span class="spin"></span> Refreshing…';
    try { await load(); } finally { b.disabled = false; b.textContent = 'Refresh'; }
  };
  $('#utable').addEventListener('click', async e => {
    const b = e.target.closest('[data-act]'); if (!b) return;
    const tr = b.closest('tr'), mac = tr.dataset.mac, network_id = +tr.dataset.net, act = b.dataset.act;
    const msgs = {
      unauth: ['Unauthorize device', `Disconnect <b>${mac}</b> and end its voucher session? The cooldown will start now. You can restore it with “Reconnect” while time is left.`, 'Unauthorize', true],
      reset: ['Reset cooldown', `Let <b>${mac}</b> enter a new voucher right away?`, 'Reset cooldown', false],
      reconnect: ['Reconnect device', `Reconnect <b>${mac}</b>? If it has voucher time left it is re-authorized for the remaining time; otherwise the device is just disconnected so it reconnects.`, 'Reconnect', false],
    }[act];
    if (!await confirmBox(...msgs)) return;
    b.disabled = true;
    try {
      const r = await api({ unauth: 'user_unauthorize', reset: 'user_reset_cooldown', reconnect: 'user_reconnect' }[act], { body: { mac, network_id } });
      toast(act === 'reconnect' ? (r.minutes ? `Reconnected (${r.minutes} min left)` : 'Device told to reconnect') : act === 'reset' ? 'Cooldown reset' : 'Device unauthorized', 'ok');
      load();
    } catch (err) { fail(err); b.disabled = false; }
  });
  await load();
  timers.push(setInterval(() => load().catch(() => {}), 30000));
}

/* ================================================================== captive settings */
const CAPTIVE = [
  ['Content', [
    { k: 'portal_title', l: 'Title', t: 'text' },
    { k: 'portal_subtitle', l: 'Subtitle', t: 'text', hint: 'Use {network} to insert the network name.' },
    { k: 'portal_page_title', l: 'Browser tab title', t: 'text' },
    { k: 'portal_input_label', l: 'Code field label', t: 'text' },
    { k: 'portal_button_text', l: 'Button text', t: 'text' },
    { k: 'portal_terms', l: 'Terms & conditions text', t: 'area' },
    { k: 'portal_footer', l: 'Footer text ("Powered by")', t: 'text' }]],
  ['Logo', [
    { k: 'portal_logo', l: 'Logo image', t: 'file', kind: 'logo' },
    { k: 'portal_logo_size', l: 'Logo max height (px)', t: 'range', min: 24, max: 300 }]],
  ['Background', [
    { k: 'portal_bg_type', l: 'Background type', t: 'select', opts: [['gradient', 'Gradient'], ['color', 'Solid color'], ['image', 'Image']] },
    { k: 'portal_bg_color', l: 'Background color', t: 'color' },
    { k: 'portal_bg_color2', l: 'Gradient end color', t: 'color' },
    { k: 'portal_bg_image', l: 'Background image', t: 'file', kind: 'bg' },
    { k: 'portal_bg_overlay', l: 'Dark overlay on image (%)', t: 'range', min: 0, max: 90 }]],
  ['Colors & style', [
    { k: 'portal_card_bg', l: 'Card color', t: 'color' },
    { k: 'portal_card_opacity', l: 'Card opacity (%) – 0 = no card', t: 'range', min: 0, max: 100 },
    { k: 'portal_text_color', l: 'Text color', t: 'color' },
    { k: 'portal_footer_color', l: 'Footer text color', t: 'color' },
    { k: 'portal_muted_color', l: 'Subtitle / muted color', t: 'color' },
    { k: 'portal_primary_color', l: 'Button color', t: 'color' },
    { k: 'portal_button_text_color', l: 'Button text color', t: 'color' },
    { k: 'portal_radius', l: 'Corner radius (px)', t: 'range', min: 0, max: 40 },
    { k: 'portal_font', l: 'Font', t: 'select', opts: [['system', 'System'], ['serif', 'Serif'], ['rounded', 'Rounded'], ['mono', 'Monospace']] }]],
  ['After connecting', [
    { k: 'portal_success_title', l: 'Success title', t: 'text' },
    { k: 'portal_success_message', l: 'Success message', t: 'text', hint: 'Use {time} for the time left.' },
    { k: 'portal_cooldown_title', l: 'Cooldown title', t: 'text' },
    { k: 'portal_cooldown_message', l: 'Cooldown message', t: 'text', hint: 'Use {time} for the wait that is left.' },
    { k: 'portal_redirect_mode', l: 'Redirect after connecting', t: 'select', opts: [['original', 'Page the guest asked for'], ['custom', 'Custom URL'], ['none', 'Stay on success page']] },
    { k: 'portal_redirect_url', l: 'Custom redirect URL', t: 'text' }]],
  ['Advanced', [{ k: 'portal_custom_css', l: 'Custom CSS', t: 'area', hint: 'Appended to the portal page. Classes: .card .logo h1 .sub .btn input' }]],
];

function fieldHtml(d, v) {
  const id = 'f_' + d.k;
  const attr = `data-k="${d.k}" id="${id}"`;
  switch (d.t) {
    case 'check': return `<label class="check"><input type="checkbox" ${attr} ${+v ? 'checked' : ''}> ${esc(d.l)}</label>`;
    case 'area': return `<label for="${id}">${esc(d.l)}</label><textarea ${attr}>${esc(v)}</textarea>${d.hint ? `<div class="hint">${esc(d.hint)}</div>` : ''}`;
    case 'color': return `<label for="${id}">${esc(d.l)}</label><input type="color" ${attr} value="${esc(v)}">`;
    case 'range': return `<label for="${id}">${esc(d.l)}: <b id="${id}_v">${v}</b></label><input type="range" ${attr} min="${d.min}" max="${d.max}" value="${v}">`;
    case 'select': return `<label for="${id}">${esc(d.l)}</label><select ${attr}>${d.opts.map(([o, l]) => `<option value="${o}" ${o === v ? 'selected' : ''}>${esc(l)}</option>`).join('')}</select>`;
    case 'file': return `<label>${esc(d.l)}</label><input type="hidden" ${attr} value="${esc(v)}">
      <div class="actions"><input type="file" accept="image/png,image/jpeg,image/gif,image/webp" data-up="${d.k}" data-kind="${d.kind}" ><button type="button" class="btn sm" data-clear="${d.k}">Remove</button></div>
      <img class="thumb ${v ? '' : 'hidden'}" id="${id}_img" src="${v ? '../uploads/' + esc(v) : ''}" alt="">`;
    default: return `<label for="${id}">${esc(d.l)}</label><input type="text" ${attr} value="${esc(v)}">${d.hint ? `<div class="hint">${esc(d.hint)}</div>` : ''}`;
  }
}

async function pageCaptive(view) {
  const { settings: S } = await api('settings_get');
  let pvView = 'form';
  view.innerHTML = `<h1>Captive Settings</h1><p class="sub">Customize how the guest portal looks. The preview updates as you type.</p>
  <div class="split"><div class="card"><form id="cf">
    ${CAPTIVE.map(([title, fields]) => `<div class="sect"><h3>${title}</h3>${title === 'Colors & style' || title === 'Background' ? '<div class="colors">' : ''}${fields.map(d => `<div style="margin-bottom:14px">${fieldHtml(d, S[d.k])}</div>`).join('')}${title === 'Colors & style' || title === 'Background' ? '</div>' : ''}</div>`).join('')}
    <div class="sect actions"><button class="btn primary" type="submit">Save settings</button><button class="btn" type="button" id="c-reset">Reset colors &amp; text to default design</button></div></form></div>
    <div class="preview card"><h2>Preview</h2><div class="tabs">${[['form', 'Voucher form'], ['success', 'Connected'], ['cooldown', 'Cooldown'], ['resume', 'Welcome back']].map(([v, l]) => `<button class="btn sm ${v === 'form' ? 'primary' : ''}" data-pv="${v}">${l}</button>`).join('')}</div>
    <iframe id="pv" src="../guest/index.php?preview=1&view=form" title="Portal preview"></iframe></div></div>`;

  const form = $('#cf'), frame = $('#pv');
  const collect = () => {
    const o = {};
    $$('[data-k]', form).forEach(el => { o[el.dataset.k] = el.type === 'checkbox' ? (el.checked ? 1 : 0) : el.type === 'range' ? +el.value : el.value; });
    return o;
  };
  const push = () => frame.contentWindow?.postMessage({ type: 'theme', s: collect(), view: pvView }, location.origin);
  frame.addEventListener('load', push);
  form.addEventListener('input', e => { if (e.target.type === 'range') $('#' + e.target.id + '_v').textContent = e.target.value; push(); });
  form.addEventListener('change', async e => {
    const up = e.target.dataset.up;
    if (!up || !e.target.files[0]) return push();
    const fd = new FormData(); fd.append('file', e.target.files[0]); fd.append('kind', e.target.dataset.kind);
    try {
      const r = await api('upload', { body: fd });
      $('#f_' + up).value = r.file; $('#f_' + up + '_img').src = r.url; $('#f_' + up + '_img').classList.remove('hidden');
      if (up === 'portal_bg_image') { $('#f_portal_bg_type').value = 'image'; }
      toast('Image uploaded – remember to save', 'ok'); push();
    } catch (err) { fail(err); }
    e.target.value = '';
  });
  form.addEventListener('click', e => {
    const c = e.target.closest('[data-clear]'); if (!c) return;
    $('#f_' + c.dataset.clear).value = ''; $('#f_' + c.dataset.clear + '_img').classList.add('hidden'); push();
  });
  $$('[data-pv]').forEach(b => b.onclick = () => {
    pvView = b.dataset.pv;
    $$('[data-pv]').forEach(x => x.classList.toggle('primary', x === b));
    frame.src = `../guest/index.php?preview=1&view=${pvView}`;
  });
  $('#c-reset').onclick = async () => {
    if (!await confirmBox('Reset design', 'Reset all texts, colors and styles to the default DDC design? Your logo and background image are kept. Nothing is saved until you press “Save settings”.', 'Reset')) return;
    const { schema } = await api('settings_get');
    $$('[data-k]', form).forEach(el => {
      const d = schema[el.dataset.k]; if (!d || d.type === 'file') return;
      if (el.type === 'checkbox') el.checked = !!+d.default; else el.value = d.default;
      if (el.type === 'range') $('#' + el.id + '_v').textContent = d.default;
    });
    push();
  };
  form.addEventListener('submit', async e => {
    e.preventDefault();
    try { await api('settings_save', { body: { values: collect() } }); toast('Captive settings saved', 'ok'); } catch (err) { fail(err); }
  });
}

/* ================================================================== user settings */
async function pageUserSettings(view) {
  const { settings: S } = await api('settings_get');
  const cd = S.cooldown_minutes;
  const unit = cd && cd % 1440 === 0 ? 1440 : cd && cd % 60 === 0 ? 60 : 1;
  view.innerHTML = `<h1>User Settings</h1><p class="sub">Rules that apply to every guest on every network.</p>
  <form id="uf" class="card" style="max-width:720px">
    <h2>Cooldown</h2>
    <p class="muted" style="margin-top:-6px">Voucher time runs on the clock from the moment it is redeemed – even if the guest disconnects. When the time is used up, the device must wait for the cooldown before it can enter another voucher.</p>
    <div class="row"><div><label>Cooldown after time is used up</label><input id="cdv" type="number" min="0" value="${cd / unit}"></div>
      <div><label>Unit</label><select id="cdu"><option value="1" ${unit === 1 ? 'selected' : ''}>Minutes</option><option value="60" ${unit === 60 ? 'selected' : ''}>Hours</option><option value="1440" ${unit === 1440 ? 'selected' : ''}>Days</option></select></div></div>
    <div class="hint" style="margin:-8px 0 16px">Set 0 to turn the cooldown off.</div>
    <div class="sect"><h3>Voucher guessing protection</h3>
    <div class="row"><div><label>Wrong codes allowed</label><input id="ma" type="number" min="0" max="100" value="${S.max_attempts}"><div class="hint">0 = no limit</div></div>
      <div><label>Then lock out for (minutes)</label><input id="lm" type="number" min="1" max="1440" value="${S.lockout_minutes}"></div></div></div>
    <div class="sect"><button class="btn primary" type="submit">Save</button></div></form>`;
  $('#uf').onsubmit = async e => {
    e.preventDefault();
    try {
      await api('settings_save', { body: { values: { cooldown_minutes: Math.round($('#cdv').value * $('#cdu').value), max_attempts: $('#ma').value, lockout_minutes: $('#lm').value } } });
      toast('User settings saved', 'ok');
    } catch (err) { fail(err); }
  };
}

/* ================================================================== networks */
async function pageNetworks(view) {
  const { networks } = await api('networks');
  view.innerHTML = `<h1>Networks</h1><p class="sub">UniFi controllers and sites that use this portal.</p>
  <div class="card"><div class="actions" style="margin-bottom:12px"><button class="btn primary" id="n-add">Add network</button></div>
  <div class="tablewrap"><table><thead><tr><th>Name</th><th>Site ID</th><th>Controller</th><th>User</th><th>Enabled</th><th></th></tr></thead><tbody>
  ${networks.map(n => `<tr data-id="${n.id}"><td><b>${esc(n.name)}</b></td><td class="mono">${esc(n.site_id)}</td><td class="mono">${esc(n.scheme)}://${esc(n.host)}:${n.port}</td><td>${esc(n.username)}</td>
    <td>${n.enabled ? badge('Yes', 'green') : badge('No')}</td>
    <td><div class="actions"><button class="btn sm" data-a="test">Test</button><button class="btn sm" data-a="edit">Edit</button><button class="btn sm danger" data-a="del">Delete</button></div></td></tr>`).join('')}
  </tbody></table></div>
  <div class="alert warn" style="margin-top:16px">Site IDs must be unique across controllers: UniFi sends the site ID in the portal URL (<span class="mono">/guest/s/&lt;site&gt;/</span>) and that is how the portal finds the right controller.</div></div>`;

  const edit = async n => {
    const isNew = !n; n = n || { name: '', site_id: 'default', host: '', port: 8443, username: '', scheme: 'https', enabled: 1 };
    const ok = await modal({ title: isNew ? 'Add network' : 'Edit network', html: `
      <div class="row"><div><label>Name</label><input id="e-name" value="${esc(n.name)}"></div></div>
      <div class="row"><div><label>Site ID</label><input id="e-site" value="${esc(n.site_id)}"></div><div><label>Scheme</label><select id="e-scheme"><option ${n.scheme === 'https' ? 'selected' : ''}>https</option><option ${n.scheme === 'http' ? 'selected' : ''}>http</option></select></div></div>
      <div class="row"><div><label>Host / IP</label><input id="e-host" value="${esc(n.host)}"></div><div class="narrow"><label>Port</label><input id="e-port" type="number" value="${n.port}"></div></div>
      <div class="row"><div><label>Username</label><input id="e-user" value="${esc(n.username)}"></div><div><label>Password</label><input id="e-pass" type="password" placeholder="${isNew ? '' : 'unchanged'}" autocomplete="new-password"></div></div>
      <label class="check"><input type="checkbox" id="e-en" ${n.enabled ? 'checked' : ''}> Enabled</label>`,
      buttons: [{ label: 'Cancel', value: false }, { label: 'Save', cls: 'primary', value: root => ({ id: n.id || 0, name: $('#e-name', root).value, site_id: $('#e-site', root).value, scheme: $('#e-scheme', root).value, host: $('#e-host', root).value, port: $('#e-port', root).value, username: $('#e-user', root).value, password: $('#e-pass', root).value, enabled: $('#e-en', root).checked }) }] });
    if (!ok) return;
    try { await api('network_save', { body: ok }); toast('Saved', 'ok'); route(); } catch (e) { fail(e); }
  };
  $('#n-add').onclick = () => edit(null);
  view.addEventListener('click', async e => {
    const b = e.target.closest('[data-a]'); if (!b) return;
    const id = +b.closest('tr').dataset.id, n = networks.find(x => x.id === id);
    if (b.dataset.a === 'edit') edit(n);
    if (b.dataset.a === 'del' && await confirmBox('Delete network', `Delete <b>${esc(n.name)}</b>? Vouchers restricted to it become valid on all networks.`, 'Delete', true)) {
      try { await api('network_delete', { body: { id } }); route(); } catch (err) { fail(err); }
    }
    if (b.dataset.a === 'test') {
      b.disabled = true; b.textContent = 'Testing…';
      try {
        const r = await api('network_status', { params: { id } });
        toast(r.ok ? `${n.name}: connected – ${r.clients} clients, ${r.aps} APs (${r.ms} ms)` : `${n.name}: ${r.error}`, r.ok ? 'ok' : 'error');
      } catch (err) { fail(err); }
      b.disabled = false; b.textContent = 'Test';
    }
  });
}

/* ================================================================== account */
async function pageAccount(view) {
  const me = await api('me');
  view.innerHTML = `<h1>Account</h1><p class="sub">Change the admin username and password.</p>
  <form id="af" class="card" style="max-width:460px">
    <label>Username</label><input id="a-user" value="${esc(me.username)}">
    <label style="margin-top:14px">Current password</label><input id="a-cur" type="password" autocomplete="current-password" required>
    <label style="margin-top:14px">New password (min. 8 characters)</label><input id="a-new" type="password" autocomplete="new-password" minlength="8" required>
    <div style="margin-top:18px"><button class="btn primary">Update</button></div></form>`;
  $('#af').onsubmit = async e => {
    e.preventDefault();
    try { await api('change_password', { body: { username: $('#a-user').value, current: $('#a-cur').value, new: $('#a-new').value } }); toast('Account updated', 'ok'); $('#banner').innerHTML = ''; e.target.reset(); } catch (err) { fail(err); }
  };
}

/* ================================================================== router */
const pages = { dashboard: pageDashboard, vouchers: pageVouchers, users: pageUsers, captive: pageCaptive, usersettings: pageUserSettings, networks: pageNetworks, account: pageAccount };

async function route() {
  timers.forEach(clearInterval); timers = [];
  const name = (location.hash || '#dashboard').slice(1);
  const key = pages[name] ? name : 'dashboard';
  $$('[data-nav]').forEach(a => a.classList.toggle('active', a.dataset.nav === key));
  $('#sidebar').classList.remove('open');
  const view = $('#view');
  const fresh = view.cloneNode(false); // drop listeners from the previous page
  view.replaceWith(fresh);
  try { await pages[key](fresh); } catch (e) { fresh.innerHTML = `<div class="alert error">${esc(e.message)}</div>`; }
}

$('#logout').onclick = async e => { e.preventDefault(); await api('logout', { body: {} }); location.reload(); };
$('#menu').onclick = () => $('#sidebar').classList.toggle('open');
window.addEventListener('hashchange', route);
api('me').then(me => {
  if (me.default_password) $('#banner').innerHTML = `<div class="alert warn">You are still using the default password. <a href="#account">Change it now</a>.</div>`;
}).catch(() => {});
route();
})();
