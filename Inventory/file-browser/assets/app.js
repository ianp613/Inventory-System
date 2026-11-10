(() => {
  const $ = (s) => document.querySelector(s);
  const csrf = document.body.dataset.csrf;
  const BASE = document.body.dataset.base || '';
  const PREVIEW = { png: 'img', jpg: 'img', jpeg: 'img', gif: 'img', webp: 'img', pdf: 'pdf', txt: 'txt', log: 'txt', csv: 'txt', json: 'txt' };

  let cwd = '';
  let items = [];
  let selected = new Set();
  let clip = null; // { mode: 'copy'|'move', paths: [] }
  let sortKey = 'name';
  let sortDir = 1;
  let searchTerm = '';
  let sizeToken = 0;

  /* ---------- helpers ---------- */
  async function api(action, params = {}, method = 'GET', form = null) {
    let url = BASE + 'api.php?action=' + encodeURIComponent(action);
    const opt = { method, headers: {} };
    if (method === 'GET') {
      url += '&' + new URLSearchParams(params).toString();
    } else {
      opt.headers['X-CSRF-Token'] = csrf;
      if (form) {
        opt.body = form;
      } else {
        opt.headers['Content-Type'] = 'application/json';
        opt.body = JSON.stringify(params);
      }
    }
    const res = await fetch(url, opt);
    let data = {};
    try { data = await res.json(); } catch (e) { /* ignore */ }
    if (res.status === 401) { location.href = BASE + 'index.php'; throw new Error('Signed out'); }
    if (!res.ok) throw new Error(data.error || 'Request failed (' + res.status + ')');
    return data;
  }

  function fmtSize(n) {
    if (n == null) return '';
    if (n < 1024) return n + ' B';
    const u = ['KB', 'MB', 'GB', 'TB'];
    let i = -1;
    do { n /= 1024; i++; } while (n >= 1024 && i < u.length - 1);
    return n.toFixed(n >= 100 ? 0 : 1) + ' ' + u[i];
  }
  function fmtDate(ts) {
    if (!ts) return '';
    const d = new Date(ts * 1000);
    return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  function join(a, b) { return a ? a + '/' + b : b; }
  function el(tag, props = {}, ...kids) {
    const e = document.createElement(tag);
    Object.assign(e, props);
    kids.forEach((k) => e.append(k));
    return e;
  }
  function notice(msg, isError = true) {
    const n = $('#notice');
    n.textContent = msg;
    n.className = 'notice ' + (isError ? 'err' : 'ok');
    n.hidden = false;
    clearTimeout(notice.t);
    notice.t = setTimeout(() => { n.hidden = true; }, isError ? 8000 : 3000);
  }
  async function guard(fn) {
    try { await fn(); } catch (e) { notice(e.message); }
  }

  /* ---------- navigation ---------- */
  function pathFromHash() {
    const h = decodeURIComponent(location.hash.replace(/^#\/?/, ''));
    return h;
  }
  function go(path) {
    searchTerm = '';
    $('#search').value = '';
    location.hash = '#/' + encodeURIComponent(path).replace(/%2F/g, '/');
    if (path === cwd) load();
  }
  window.addEventListener('hashchange', () => { cwd = pathFromHash(); load(); });

  async function load() {
    selected.clear();
    updateButtons();
    await guard(async () => {
      const data = searchTerm
        ? await api('search', { path: cwd, q: searchTerm })
        : await api('list', { path: cwd });
      items = data.items;
      render();
      loadFolderSizes();
    });
  }

  /* ---------- rendering ---------- */
  function renderCrumbs() {
    const nav = $('#crumbs');
    nav.replaceChildren();
    const home = el('a', { href: '#/', textContent: 'Home' });
    nav.append(home);
    let acc = '';
    cwd.split('/').filter(Boolean).forEach((seg) => {
      acc = join(acc, seg);
      nav.append(el('span', { textContent: ' / ' }));
      nav.append(el('a', { href: '#/' + encodeURI(acc), textContent: seg }));
    });
    if (searchTerm) nav.append(el('span', { className: 'muted', textContent: '  — results for "' + searchTerm + '"' }));
  }

  function sorted() {
    const arr = items.slice();
    arr.sort((a, b) => {
      if (a.type !== b.type) return a.type === 'dir' ? -1 : 1;
      let r;
      if (sortKey === 'size') r = (a.size ?? -1) - (b.size ?? -1);
      else if (sortKey === 'modified') r = a.modified - b.modified;
      else r = a.name.localeCompare(b.name, undefined, { numeric: true, sensitivity: 'base' });
      return r * sortDir;
    });
    return arr;
  }

  function render() {
    renderCrumbs();
    const tbody = $('#rows');
    tbody.replaceChildren();
    sorted().forEach((it) => {
      const tr = el('tr');
      tr.dataset.path = it.path;
      if (selected.has(it.path)) tr.className = 'sel';

      const cb = el('input', { type: 'checkbox', checked: selected.has(it.path) });
      cb.addEventListener('change', () => {
        cb.checked ? selected.add(it.path) : selected.delete(it.path);
        tr.className = cb.checked ? 'sel' : '';
        updateButtons();
      });

      const icon = el('span', { className: 'ico', textContent: it.type === 'dir' ? '📁' : '📄' });
      const link = el('span', { className: 'name', textContent: it.name });
      const nameCell = el('td', {}, icon, link);

      // click selects the row (Ctrl/Cmd+click adds to the selection); double-click opens it
      tr.addEventListener('click', (e) => {
        if (e.target.closest('input, button')) return;
        if (!(e.ctrlKey || e.metaKey)) selected.clear();
        selected.has(it.path) && (e.ctrlKey || e.metaKey) ? selected.delete(it.path) : selected.add(it.path);
        syncSelection();
      });
      tr.addEventListener('dblclick', (e) => {
        if (e.target.closest('input, button')) return;
        window.getSelection()?.removeAllRanges();
        openItem(it);
      });
      if (searchTerm) {
        const parent = it.path.split('/').slice(0, -1).join('/');
        nameCell.append(el('div', { className: 'muted small', textContent: '/' + parent }));
      }

      const sizeCell = el('td', { className: 'c-size', textContent: it.type === 'dir' ? '…' : fmtSize(it.size) });
      sizeCell.dataset.size = it.path;

      const acts = el('td', { className: 'c-act' });
      if (it.type === 'file') acts.append(btn('Download', () => download(it)));
      acts.append(btn('Rename', () => rename(it)));
      acts.append(btn('Delete', () => remove([it.path]), 'danger'));

      tr.append(el('td', { className: 'c-check' }, cb), nameCell, sizeCell, el('td', { className: 'c-mod', textContent: fmtDate(it.modified) }), acts);
      tbody.append(tr);
    });
    $('#empty').hidden = items.length > 0;
    $('#empty').textContent = searchTerm ? 'No matches.' : 'This folder is empty.';
    $('#checkAll').checked = false;
    updateStatus();
  }

  function syncSelection() {
    document.querySelectorAll('#rows tr').forEach((tr) => {
      const on = selected.has(tr.dataset.path);
      tr.className = on ? 'sel' : '';
      tr.querySelector('input[type=checkbox]').checked = on;
    });
    updateButtons();
  }

  function openItem(it) {
    if (it.type === 'dir') go(it.path);
    else if (PREVIEW[it.ext]) openPreview(it);
    else download(it);
  }

  function btn(label, fn, cls = '') {
    const b = el('button', { textContent: label, className: 'small ' + cls });
    b.addEventListener('click', fn);
    return b;
  }

  function updateStatus(extra = '') {
    const dirs = items.filter((i) => i.type === 'dir').length;
    const files = items.length - dirs;
    const fileBytes = items.reduce((s, i) => s + (i.size || 0), 0);
    $('#status').textContent = dirs + ' folder(s), ' + files + ' file(s) · files in view: ' + fmtSize(fileBytes) + extra;
  }

  function updateButtons() {
    const n = selected.size;
    $('#btnCopy').disabled = n === 0;
    $('#btnCut').disabled = n === 0;
    $('#btnDelete').disabled = n === 0;
    $('#btnPaste').disabled = !clip || !!searchTerm;
    $('#btnPaste').textContent = clip ? 'Paste here (' + clip.paths.length + ' ' + clip.mode + ')' : 'Paste here';
  }

  /* folder sizes are computed one at a time so large trees don't hammer the disk */
  async function loadFolderSizes() {
    const token = ++sizeToken;
    let total = 0;
    for (const it of items.filter((i) => i.type === 'dir')) {
      if (token !== sizeToken) return;
      const cell = document.querySelector('[data-size="' + CSS.escape(it.path) + '"]');
      if (cell) cell.textContent = 'calculating…';
      try {
        const r = await api('foldersize', { path: it.path });
        if (token !== sizeToken) return;
        it.size = r.size;
        total += r.size;
        if (cell) { cell.textContent = fmtSize(r.size); cell.title = r.files + ' file(s)'; }
      } catch (e) {
        if (cell) cell.textContent = '—';
      }
    }
    if (token === sizeToken) {
      const all = items.reduce((s, i) => s + (i.size || 0), 0);
      updateStatus(' · total with folders: ' + fmtSize(all));
    }
  }

  /* ---------- actions ---------- */
  function download(it) {
    location.href = BASE + 'api.php?action=download&path=' + encodeURIComponent(it.path);
  }

  function openPreview(it) {
    const url = BASE + 'api.php?action=preview&path=' + encodeURIComponent(it.path);
    const body = $('#modalBody');
    body.replaceChildren();
    $('#modalTitle').textContent = it.name;
    const kind = PREVIEW[it.ext];
    if (kind === 'img') body.append(el('img', { src: url, alt: it.name }));
    else if (kind === 'pdf') body.append(el('iframe', { src: url }));
    else {
      const pre = el('pre', { textContent: 'Loading…' });
      body.append(pre);
      fetch(url).then((r) => r.text()).then((t) => { pre.textContent = t.length > 200000 ? t.slice(0, 200000) + '\n… (truncated)' : t; });
    }
    $('#modal').hidden = false;
  }
  function closePreview() { $('#modal').hidden = true; $('#modalBody').replaceChildren(); }

  function rename(it) {
    const name = prompt('New name:', it.name);
    if (!name || name === it.name) return;
    guard(async () => { await api('rename', { path: it.path, name }, 'POST'); await load(); });
  }

  function remove(paths) {
    const msg = paths.length === 1 ? 'Delete "' + paths[0].split('/').pop() + '"? Folders are deleted with everything inside.' : 'Delete ' + paths.length + ' items? Folders are deleted with everything inside.';
    if (!confirm(msg)) return;
    guard(async () => { await api('delete', { paths }, 'POST'); notice('Deleted.', false); await load(); });
  }

  async function upload(fileList) {
    if (!fileList.length) return;
    const fd = new FormData();
    fd.append('path', cwd);
    [...fileList].forEach((f) => fd.append('files[]', f));
    notice('Uploading ' + fileList.length + ' file(s)…', false);
    await guard(async () => {
      const r = await api('upload', {}, 'POST', fd);
      notice('Uploaded ' + r.saved.length + ' file(s).', false);
      await load();
    });
  }

  /* ---------- wiring ---------- */
  $('#btnUpload').addEventListener('click', () => $('#fileInput').click());
  $('#fileInput').addEventListener('change', (e) => { upload(e.target.files); e.target.value = ''; });
  $('#btnMkdir').addEventListener('click', () => {
    const name = prompt('Folder name:');
    if (!name) return;
    guard(async () => { await api('mkdir', { path: cwd, name }, 'POST'); await load(); });
  });
  $('#btnCopy').addEventListener('click', () => { clip = { mode: 'copy', paths: [...selected] }; updateButtons(); notice('Copied. Open the destination folder and click Paste here.', false); });
  $('#btnCut').addEventListener('click', () => { clip = { mode: 'move', paths: [...selected] }; updateButtons(); notice('Ready to move. Open the destination folder and click Paste here.', false); });
  $('#btnPaste').addEventListener('click', () => {
    if (!clip) return;
    const c = clip;
    guard(async () => {
      await api(c.mode, { paths: c.paths, dest: cwd }, 'POST');
      if (c.mode === 'move') clip = null;
      notice(c.mode === 'copy' ? 'Copied.' : 'Moved.', false);
      await load();
    });
  });
  $('#btnDelete').addEventListener('click', () => remove([...selected]));
  $('#checkAll').addEventListener('change', (e) => {
    selected = e.target.checked ? new Set(items.map((i) => i.path)) : new Set();
    render();
    $('#checkAll').checked = e.target.checked;
    updateButtons();
  });
  document.querySelectorAll('th[data-sort]').forEach((th) => th.addEventListener('click', () => {
    const k = th.dataset.sort;
    if (sortKey === k) sortDir *= -1; else { sortKey = k; sortDir = 1; }
    render();
    loadFolderSizesFromCache();
  }));
  function loadFolderSizesFromCache() {
    items.filter((i) => i.type === 'dir' && i.size != null).forEach((it) => {
      const cell = document.querySelector('[data-size="' + CSS.escape(it.path) + '"]');
      if (cell) cell.textContent = fmtSize(it.size);
    });
  }

  let st;
  $('#search').addEventListener('input', (e) => {
    clearTimeout(st);
    st = setTimeout(() => { searchTerm = e.target.value.trim(); load(); }, 350);
  });

  $('#modalClose').addEventListener('click', closePreview);
  $('#modal').addEventListener('click', (e) => { if (e.target.id === 'modal') closePreview(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePreview(); });

  const dz = $('#dropzone');
  ['dragenter', 'dragover'].forEach((ev) => dz.addEventListener(ev, (e) => { e.preventDefault(); dz.classList.add('drag'); }));
  ['dragleave', 'drop'].forEach((ev) => dz.addEventListener(ev, (e) => { e.preventDefault(); dz.classList.remove('drag'); }));
  dz.addEventListener('drop', (e) => { if (!searchTerm) upload(e.dataTransfer.files); });

  cwd = pathFromHash();
  load();
})();
