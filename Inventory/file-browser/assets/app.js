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
        if (coarse.matches) { openItem(it); return; } // phones/tablets: a tap opens (use the checkbox to select)
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
      acts.append(btn('Download', () => downloadPaths([it.path])));
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
    $('#btnDownload').disabled = n === 0;
    $('#btnDownload').textContent = n > 1 ? 'Download (' + n + ')' : 'Download';
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

  /* One file -> downloaded as is. A folder, or several items -> streamed as one ZIP. */
  let zipForm = null;
  function ensureZipForm() {
    if (zipForm) return;
    const frame = el('iframe', { name: 'zipFrame', hidden: true, title: 'download' });
    frame.addEventListener('load', () => {
      try { // only an error reply (JSON) ends up in here; a real download never does
        const txt = (frame.contentDocument && frame.contentDocument.body ? frame.contentDocument.body.innerText : '').trim();
        if (txt.startsWith('{')) { const d = JSON.parse(txt); if (d.error) notice(d.error); }
      } catch (e) { /* ignore */ }
    });
    zipForm = el('form', { method: 'post', action: BASE + 'api.php?action=zip', target: 'zipFrame', hidden: true });
    zipForm.append(el('input', { type: 'hidden', name: 'csrf', value: csrf }), el('input', { type: 'hidden', name: 'paths' }));
    document.body.append(frame, zipForm);
  }
  function downloadPaths(paths) {
    if (!paths.length) return;
    if (paths.length === 1) {
      const it = items.find((i) => i.path === paths[0]);
      if (it && it.type === 'file') return download(it);
    }
    ensureZipForm();
    zipForm.elements.paths.value = JSON.stringify(paths);
    zipForm.submit();
    notice('Preparing ZIP download… your browser will start saving it in a moment.', false);
  }

  /* ---------- video player ---------- */
  ['mp4', 'm4v', 'webm', 'ogv', 'mov', 'mkv'].forEach((x) => { PREVIEW[x] = 'video'; });
  const RATES = [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 3];
  const uid = document.body.dataset.uid || '0';
  const KV = 'fm.video.' + uid; // { last: {...}, pos: { "<path>": { time, duration, size, modified, at } } }
  const KP = 'fm.player';       // { volume, muted, rate }
  const coarse = window.matchMedia('(pointer: coarse)'); // touch screens: one tap opens an item
  const store = {
    get(key, def) { try { const v = localStorage.getItem(key); return v ? JSON.parse(v) : def; } catch (e) { return def; } },
    set(key, val) { try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) { /* private mode / storage full */ } },
  };
  let playerCleanup = null;

  const svg = (d, extra = '') => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="' + d + '"/>' + extra + '</svg>';
  const TEN = '<text x="12" y="15.3" text-anchor="middle" font-size="7.5" font-weight="700" font-family="Arial,Helvetica,sans-serif">10</text>';
  const ICON = {
    play: svg('M8 5v14l11-7z'),
    pause: svg('M6 19h4V5H6v14zm8-14v14h4V5h-4z'),
    back: svg('M12 5V1L7 6l5 5V7c3.3 0 6 2.7 6 6s-2.7 6-6 6-6-2.7-6-6H4c0 4.4 3.6 8 8 8s8-3.6 8-8-3.6-8-8-8z', TEN),
    fwd: svg('M12 5V1l5 5-5 5V7c-3.3 0-6 2.7-6 6s2.7 6 6 6 6-2.7 6-6h2c0 4.4-3.6 8-8 8s-8-3.6-8-8 3.6-8 8-8z', TEN),
    vol: svg('M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.8-1-3.3-2.5-4v8c1.5-.7 2.5-2.2 2.5-4zM14 3.2v2.1c2.9.9 5 3.5 5 6.7s-2.1 5.8-5 6.7v2.1c4-.9 7-4.5 7-8.8s-3-7.9-7-8.8z'),
    mute: svg('M16.5 12c0-1.8-1-3.3-2.5-4v2.2l2.5 2.5v-.7zM19 12c0 .9-.2 1.8-.5 2.6l1.5 1.5c.7-1.2 1-2.6 1-4.1 0-4.3-3-7.9-7-8.8v2.1c2.9.9 5 3.5 5 6.7zM4.3 3L3 4.3 7.7 9H3v6h4l5 5v-6.7l4.2 4.2c-.7.5-1.4.9-2.2 1.1v2.1c1.4-.3 2.6-1 3.7-1.9l2 2 1.3-1.3L4.3 3zM12 4L9.9 6.1 12 8.2V4z'),
    full: svg('M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z'),
    exitFull: svg('M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z'),
    pip: svg('M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 1.98 2 1.98h18c1.1 0 2-.88 2-1.98V5c0-1.1-.9-2-2-2zm0 16.01H3V4.98h18v14.03z'),
  };

  function fmtTime(sec) {
    if (!isFinite(sec) || sec < 0) sec = 0;
    sec = Math.floor(sec);
    const h = Math.floor(sec / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = String(sec % 60).padStart(2, '0');
    return h ? h + ':' + String(m).padStart(2, '0') + ':' + s : m + ':' + s;
  }

  /* remembers the last video and the time per video, so playback resumes where you stopped */
  function readSaved(it) {
    const d = store.get(KV, { pos: {} });
    const r = d.pos && d.pos[it.path];
    if (!r) return null;
    if (it.size != null && r.size != null && r.size !== it.size) return null;         // file was replaced
    if (it.modified && r.modified && r.modified !== it.modified) return null;
    return r;
  }
  function saveProgress(it, time, duration) {
    const d = store.get(KV, { last: null, pos: {} });
    d.pos = d.pos || {};
    const finished = duration > 30 && time >= duration - 10;
    const at = Date.now();
    d.pos[it.path] = { time: finished ? 0 : time, duration, size: it.size, modified: it.modified, at };
    d.last = finished ? null : { path: it.path, name: it.name, ext: it.ext, size: it.size, modified: it.modified, time, duration, at };
    const keys = Object.keys(d.pos);
    if (keys.length > 200) keys.sort((a, b) => d.pos[a].at - d.pos[b].at).slice(0, keys.length - 200).forEach((k) => delete d.pos[k]);
    store.set(KV, d);
  }
  function renderContinue() {
    const box = $('#continue');
    const d = store.get(KV, { last: null });
    if (!d.last || !(d.last.time >= 5)) { box.hidden = true; box.replaceChildren(); return; }
    box.replaceChildren(
      el('span', { textContent: 'Continue watching' }),
      el('strong', { textContent: d.last.name }),
      el('span', { className: 'muted', textContent: 'at ' + fmtTime(d.last.time) }),
      el('span', { className: 'grow' }),
      btn('▶ Play', () => openVideo(d.last)),
      btn('Dismiss', () => {
        const x = store.get(KV, { last: null, pos: {} });
        x.last = null;
        store.set(KV, x);
        renderContinue();
      })
    );
    box.hidden = false;
  }

  function openVideo(it) {
    const url = BASE + 'api.php?action=preview&path=' + encodeURIComponent(it.path);
    const prefs = Object.assign({ volume: 1, muted: false, rate: 1 }, store.get(KP, {}));
    const saved = readSaved(it);
    $('#modalTitle').textContent = it.name;
    $('#modal .modal-box').classList.add('video');

    const mk = (cls, label, html) => {
      const b = el('button', { className: cls, title: label, type: 'button' });
      b.setAttribute('aria-label', label);
      b.innerHTML = html;
      return b;
    };
    const video = el('video', { playsInline: true, preload: 'metadata' });
    const bPlay = mk('p-play', 'Play / Pause (Space)', ICON.play);
    const bBack = mk('p-back', 'Back 10 seconds (←)', ICON.back);
    const bFwd = mk('p-fwd', 'Forward 10 seconds (→)', ICON.fwd);
    const bMute = mk('p-mute', 'Mute (M)', ICON.vol);
    const bFull = mk('p-full', 'Fullscreen (F)', ICON.full);
    const bPip = document.pictureInPictureEnabled ? mk('p-pip', 'Picture in picture', ICON.pip) : null;
    const vol = el('input', { type: 'range', className: 'p-vol', min: '0', max: '1', step: '0.05', title: 'Volume (↑ ↓)' });
    const rate = el('select', { className: 'p-rate', title: 'Playback speed (< >)' });
    RATES.forEach((r) => rate.append(el('option', { value: String(r), textContent: r + '×' })));
    const seek = el('input', { type: 'range', className: 'p-seek', min: '0', max: '1000', step: '1', value: '0', title: 'Seek' });
    const time = el('span', { className: 'p-time', textContent: '0:00 / 0:00' });
    const msg = el('div', { className: 'p-msg' });
    msg.hidden = true;
    const row = el('div', { className: 'p-row' }, bPlay, bBack, bFwd, time, el('span', { className: 'grow' }), bMute, vol, rate);
    if (bPip) row.append(bPip);
    row.append(bFull);
    const box = el('div', { className: 'player', tabIndex: -1 }, video, msg, el('div', { className: 'p-controls' }, seek, row));

    let closed = false;
    let seeking = false;
    let lastSave = 0;
    let msgTimer = null;
    let msgKind = '';
    let idleTimer = null;

    const dur = () => (isFinite(video.duration) ? video.duration : 0);
    const setMsg = (nodes, ms = 0, kind = '') => {
      msg.replaceChildren(...nodes);
      msg.hidden = false;
      msgKind = kind;
      clearTimeout(msgTimer);
      if (ms) msgTimer = setTimeout(() => { msg.hidden = true; }, ms);
    };
    const hideMsg = (kind) => { if (!kind || msgKind === kind) { msg.hidden = true; } };

    const syncPlay = () => { bPlay.innerHTML = video.paused ? ICON.play : ICON.pause; };
    const syncVol = () => {
      const m = video.muted || video.volume === 0;
      bMute.innerHTML = m ? ICON.mute : ICON.vol;
      vol.value = m ? 0 : video.volume;
      vol.style.setProperty('--vol', Math.round(vol.value * 100) + '%');
    };
    const syncTime = () => {
      if (seeking) return;
      const d = dur();
      const t = video.currentTime;
      time.textContent = fmtTime(t) + ' / ' + fmtTime(d);
      seek.value = d ? Math.round((t / d) * 1000) : 0;
      seek.style.setProperty('--played', (d ? (t / d) * 100 : 0) + '%');
      let buf = 0;
      try { for (let i = 0; i < video.buffered.length; i++) if (video.buffered.start(i) <= t && video.buffered.end(i) >= t) buf = video.buffered.end(i); } catch (e) { /* ignore */ }
      seek.style.setProperty('--buffered', (d ? (buf / d) * 100 : 0) + '%');
    };

    const save = (force) => {
      if (closed || video.readyState === 0 || !dur()) return; // never overwrite a saved time before the video has loaded
      const now = Date.now();
      if (!force && now - lastSave < 2000) return;
      lastSave = now;
      saveProgress(it, video.currentTime, dur());
    };
    const savePrefs = () => store.set(KP, { volume: video.volume, muted: video.muted, rate: video.playbackRate });

    const toggle = () => { if (video.paused) video.play().catch(() => {}); else video.pause(); };
    const skip = (s) => {
      const d = dur();
      video.currentTime = Math.max(0, d ? Math.min(d, video.currentTime + s) : video.currentTime + s);
      setMsg([el('span', { textContent: (s > 0 ? '+' : '−') + Math.abs(s) + ' s' })], 600, 'skip');
    };
    const setVolume = (v) => { video.volume = Math.max(0, Math.min(1, v)); if (video.volume > 0) video.muted = false; };
    const setRate = (r) => { video.defaultPlaybackRate = r; video.playbackRate = r; };
    const stepRate = (dir) => {
      let i = RATES.indexOf(video.playbackRate);
      if (i < 0) i = RATES.indexOf(1);
      setRate(RATES[Math.max(0, Math.min(RATES.length - 1, i + dir))]);
    };
    const toggleFull = () => {
      if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
      else if (box.requestFullscreen) box.requestFullscreen().catch(() => {});
      else if (video.webkitEnterFullscreen) video.webkitEnterFullscreen(); // iPhone Safari
    };
    const wake = () => {
      box.classList.remove('idle');
      clearTimeout(idleTimer);
      if (document.fullscreenElement === box && !video.paused) idleTimer = setTimeout(() => box.classList.add('idle'), 3000);
    };

    // controls
    bPlay.addEventListener('click', () => { toggle(); box.focus(); });
    bBack.addEventListener('click', () => { skip(-10); box.focus(); });
    bFwd.addEventListener('click', () => { skip(10); box.focus(); });
    bMute.addEventListener('click', () => { video.muted = !video.muted; box.focus(); });
    bFull.addEventListener('click', toggleFull);
    if (bPip) bPip.addEventListener('click', () => { (document.pictureInPictureElement ? document.exitPictureInPicture() : video.requestPictureInPicture()).catch(() => {}); });
    vol.addEventListener('input', () => setVolume(parseFloat(vol.value)));
    rate.addEventListener('change', () => { setRate(parseFloat(rate.value)); box.focus(); });
    seek.addEventListener('input', () => {
      seeking = true;
      const d = dur();
      time.textContent = fmtTime((seek.value / 1000) * d) + ' / ' + fmtTime(d);
      seek.style.setProperty('--played', seek.value / 10 + '%');
    });
    seek.addEventListener('change', () => { video.currentTime = (seek.value / 1000) * dur(); seeking = false; syncTime(); });
    video.addEventListener('click', () => { toggle(); box.focus(); });
    video.addEventListener('dblclick', toggleFull);
    ['mousemove', 'touchstart', 'click'].forEach((ev) => box.addEventListener(ev, wake, { passive: true }));

    // video events
    video.addEventListener('play', () => { syncPlay(); wake(); });
    video.addEventListener('pause', () => { syncPlay(); save(true); wake(); });
    video.addEventListener('timeupdate', () => { syncTime(); save(false); });
    video.addEventListener('progress', syncTime);
    video.addEventListener('durationchange', syncTime);
    video.addEventListener('volumechange', () => { syncVol(); savePrefs(); });
    video.addEventListener('ratechange', () => { rate.value = String(video.playbackRate); savePrefs(); });
    video.addEventListener('waiting', () => setMsg([el('span', { textContent: 'Buffering…' })], 0, 'buffer'));
    video.addEventListener('playing', () => hideMsg('buffer'));
    video.addEventListener('canplay', () => hideMsg('buffer'));
    video.addEventListener('ended', () => { if (!closed) saveProgress(it, dur(), dur()); syncPlay(); });
    video.addEventListener('loadedmetadata', () => {
      syncTime();
      const d = dur();
      if (saved && saved.time >= 5 && (!d || saved.time < d - 10)) {
        video.currentTime = saved.time;
        setMsg([
          el('span', { textContent: 'Resumed from ' + fmtTime(saved.time) }),
          btn('Start over', () => { video.currentTime = 0; hideMsg(); }),
        ], 8000, 'resume');
      }
    });
    video.addEventListener('error', () => {
      if (closed) return;
      const c = video.error ? video.error.code : 0;
      const text = c === 2 ? 'Network error while loading the video.'
        : c === 3 ? 'The video could not be decoded (damaged file or unsupported codec).'
        : "This browser can't play this video (unsupported format or codec, or the file is missing).";
      setMsg([el('span', { textContent: text }), btn('Download', () => download(it))], 0, 'error');
    });

    const onKey = (e) => {
      if (e.ctrlKey || e.metaKey || e.altKey) return;
      const tag = e.target.tagName;
      if (tag === 'SELECT' || (tag === 'INPUT' && e.target.type !== 'range')) return;
      if ((e.key === ' ' || e.key === 'Enter') && tag === 'BUTTON') return;
      switch (e.key) {
        case ' ': case 'k': case 'K': toggle(); break;
        case 'ArrowLeft': case 'j': case 'J': skip(-10); break;
        case 'ArrowRight': case 'l': case 'L': skip(10); break;
        case 'ArrowUp': setVolume(video.volume + 0.05); break;
        case 'ArrowDown': setVolume(video.volume - 0.05); break;
        case 'm': case 'M': video.muted = !video.muted; break;
        case 'f': case 'F': toggleFull(); break;
        case '>': stepRate(1); break;
        case '<': stepRate(-1); break;
        default: return;
      }
      e.preventDefault();
      wake();
    };
    const onFs = () => { bFull.innerHTML = document.fullscreenElement ? ICON.exitFull : ICON.full; wake(); };
    const onHide = () => save(true);
    const onVis = () => { if (document.visibilityState === 'hidden') save(true); };
    document.addEventListener('keydown', onKey);
    document.addEventListener('fullscreenchange', onFs);
    window.addEventListener('pagehide', onHide);
    document.addEventListener('visibilitychange', onVis);

    playerCleanup = () => {
      save(true);
      closed = true;
      video.pause();
      document.removeEventListener('keydown', onKey);
      document.removeEventListener('fullscreenchange', onFs);
      window.removeEventListener('pagehide', onHide);
      document.removeEventListener('visibilitychange', onVis);
      clearTimeout(idleTimer);
      clearTimeout(msgTimer);
      if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
      video.removeAttribute('src');
      video.load(); // stops downloading
      $('#modal .modal-box').classList.remove('video');
      renderContinue();
    };

    // start
    video.volume = prefs.volume;
    video.muted = prefs.muted;
    setRate(RATES.includes(prefs.rate) ? prefs.rate : 1);
    syncPlay(); syncVol();
    rate.value = String(video.playbackRate);
    const body = $('#modalBody');
    body.replaceChildren(box);
    $('#modal').hidden = false;
    video.src = url;
    video.play().catch(() => {});
    box.focus();
  }

  function openPreview(it) {
    if (PREVIEW[it.ext] === 'video') return openVideo(it);
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
  function closePreview() {
    if (playerCleanup) { playerCleanup(); playerCleanup = null; }
    $('#modal').hidden = true;
    $('#modalBody').replaceChildren();
  }

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

  /* ---------- uploads: queue, per-file progress bar, status and error message ---------- */
  const uploads = [];

  // entries: [{ file, rel }] - rel is the path inside the destination ("a.txt" or "Folder/sub/a.txt")
  function upload(entries, dest = cwd) {
    if (!entries.length) return;
    entries.forEach(({ file, rel }) => {
      const cut = rel.lastIndexOf('/');
      const u = { file, rel, fname: rel.slice(cut + 1), sub: cut >= 0 ? rel.slice(0, cut) : '', dest, state: 'waiting', xhr: null };
      u.li = el('li', { className: 'up waiting' });
      u.name = el('div', { className: 'up-name', textContent: rel, title: rel });
      u.status = el('div', { className: 'up-status', textContent: 'Waiting…' });
      u.fill = el('div', { className: 'fill' });
      u.cancel = btn('Cancel', () => cancelUpload(u));
      u.li.append(u.name, el('div', { className: 'up-size', textContent: fmtSize(file.size) }), el('div', { className: 'bar' }, u.fill), u.status, u.cancel);
      $('#uploadList').append(u.li);
      uploads.push(u);
    });
    $('#uploads').hidden = false;
    updateUploadSummary();
    runUploads();
  }
  const fromFiles = (list) => [...list].map((f) => ({ file: f, rel: f.name }));
  const fromFolderInput = (list) => [...list].map((f) => ({ file: f, rel: f.webkitRelativePath || f.name }));

  /* dragging whole folders in: walk the dropped entries (also finds empty folders) */
  function readAllEntries(reader) {
    return new Promise((resolve, reject) => {
      const all = [];
      const next = () => reader.readEntries((batch) => {
        if (!batch.length) resolve(all); else { all.push(...batch); next(); }
      }, reject);
      next();
    });
  }
  async function walkEntry(entry, prefix, out) {
    if (entry.isFile) {
      const file = await new Promise((res, rej) => entry.file(res, rej));
      out.files.push({ file, rel: prefix + entry.name });
    } else if (entry.isDirectory) {
      const kids = await readAllEntries(entry.createReader());
      if (!kids.length) out.dirs.push(prefix + entry.name);
      for (const k of kids) await walkEntry(k, prefix + entry.name + '/', out);
    }
  }
  async function handleDrop(e) {
    const dest = cwd;
    const dt = e.dataTransfer;
    const plain = [...dt.files];                       // read everything synchronously:
    const entries = [...(dt.items || [])].map((it) => (it.kind === 'file' && it.webkitGetAsEntry ? it.webkitGetAsEntry() : null));
    if (!entries.length || entries.some((x) => !x)) return upload(fromFiles(plain), dest); // browser can't give folder info
    const out = { files: [], dirs: [] };
    notice('Reading dropped items…', false);
    await guard(async () => { for (const en of entries) await walkEntry(en, '', out); });
    for (const d of out.dirs) await guard(() => api('mkpath', { path: dest, sub: d }, 'POST'));
    $('#notice').hidden = true;
    upload(out.files, dest);
    if (!out.files.length && out.dirs.length && dest === cwd) load();
  }

  function setUpload(u, state, text, pct) {
    u.state = state;
    u.li.className = 'up ' + state;
    u.status.textContent = text;
    if (pct != null) u.fill.style.width = pct + '%';
    u.cancel.hidden = !(state === 'waiting' || state === 'uploading' || state === 'saving');
    if (state === 'error') u.status.title = text;
    updateUploadSummary();
  }

  function updateUploadSummary() {
    const n = (s) => uploads.filter((u) => u.state === s).length;
    const active = n('waiting') + n('uploading') + n('saving');
    const parts = [];
    if (active) parts.push(active + ' in progress');
    if (n('done')) parts.push(n('done') + ' done');
    if (n('error')) parts.push(n('error') + ' failed');
    if (n('cancelled')) parts.push(n('cancelled') + ' cancelled');
    $('#uploadSummary').textContent = parts.length ? ' · ' + parts.join(' · ') : '';
  }

  function cancelUpload(u) {
    u.cancelled = true;
    if (u.state === 'waiting') setUpload(u, 'cancelled', 'Cancelled', 0);
    else if (u.xhr) u.xhr.abort();
  }

  const CHUNK = 5 * 1024 * 1024; // each request carries at most 5 MB, so none can hit a proxy timeout

  function newUploadId() {
    const a = new Uint8Array(12);
    crypto.getRandomValues(a);
    return [...a].map((b) => b.toString(16).padStart(2, '0')).join('');
  }

  // sends one chunk; resolves { ok, error, cancelled, saved }
  function sendChunk(u, offset, blob, last) {
    return new Promise((resolve) => {
      const q = new URLSearchParams({
        action: 'uploadchunk', path: u.dest, sub: u.sub, name: u.fname, id: u.id,
        offset: String(offset), total: String(u.file.size), last: last ? '1' : '0',
      });
      const xhr = new XMLHttpRequest();
      u.xhr = xhr;
      xhr.open('POST', BASE + 'api.php?' + q.toString());
      xhr.setRequestHeader('X-CSRF-Token', csrf);
      xhr.setRequestHeader('Content-Type', 'application/octet-stream');
      xhr.upload.onprogress = (e) => {
        if (!e.lengthComputable) return;
        const sent = offset + e.loaded;
        const pct = u.file.size ? Math.min(100, Math.round((sent / u.file.size) * 100)) : 100;
        if (last && e.loaded >= e.total) setUpload(u, 'saving', 'Saving on server…', 100);
        else setUpload(u, 'uploading', 'Uploading ' + pct + '% (' + fmtSize(Math.min(sent, u.file.size)) + ' of ' + fmtSize(u.file.size) + ')', pct);
      };
      xhr.onload = () => {
        let data = {};
        try { data = JSON.parse(xhr.responseText); } catch (e) { /* not JSON */ }
        if (xhr.status === 401) { location.href = BASE + 'index.php'; return resolve({ ok: false, error: 'Signed out' }); }
        if (xhr.status >= 200 && xhr.status < 300 && (data.saved || data.received !== undefined)) {
          return resolve({ ok: true, saved: data.saved });
        }
        const retry = xhr.status === 0 || xhr.status >= 500; // proxy/server hiccup: worth retrying
        let msg = data.error;
        if (!msg && xhr.status === 413) msg = 'The server or proxy rejected the request as too large (HTTP 413).';
        if (!msg && [502, 503, 504, 524, 408].includes(xhr.status)) msg = 'The server or proxy timed out (HTTP ' + xhr.status + ').';
        if (!msg) msg = 'Server returned an error (HTTP ' + xhr.status + ').';
        resolve({ ok: false, error: msg, retry });
      };
      xhr.onerror = () => resolve({ ok: false, retry: true, error: 'Network error - the connection to the server was lost.' });
      xhr.ontimeout = () => resolve({ ok: false, retry: true, error: 'The upload timed out.' });
      xhr.onabort = () => resolve({ ok: false, cancelled: true });
      xhr.send(blob);
    });
  }

  function abortOnServer(u) {
    fetch(BASE + 'api.php?' + new URLSearchParams({ action: 'uploadabort', path: u.dest, sub: u.sub, id: u.id }), {
      method: 'POST', headers: { 'X-CSRF-Token': csrf },
    }).catch(() => {});
  }

  async function sendUpload(u) {
    const total = u.file.size;
    u.id = newUploadId();
    u.cancelled = false;
    setUpload(u, 'uploading', 'Uploading 0%', 0);
    let offset = 0;
    let saved = null;
    do {
      const end = Math.min(offset + CHUNK, total);
      const last = end >= total;
      const blob = u.file.slice(offset, end);
      let r;
      for (let attempt = 1; ; attempt++) {
        r = await sendChunk(u, offset, blob, last);
        if (r.ok || r.cancelled || !r.retry || attempt >= 3) break;
        setUpload(u, 'uploading', r.error + ' Retrying (' + attempt + '/2)…');
        await new Promise((res) => setTimeout(res, 1500 * attempt));
      }
      if (r.cancelled || u.cancelled) { abortOnServer(u); setUpload(u, 'cancelled', 'Cancelled'); return false; }
      if (!r.ok) { abortOnServer(u); setUpload(u, 'error', 'Failed: ' + r.error); return false; }
      saved = r.saved || saved;
      offset = end;
    } while (offset < total);
    setUpload(u, 'done', saved && saved !== u.fname ? 'Done - saved as "' + saved + '" (name already existed)' : 'Done', 100);
    return true;
  }

  const MAX_PARALLEL = 3; // a few files at once makes folders with many small files much faster
  let activeUploads = 0;
  let uploadRefresh = false;
  function runUploads() {
    while (activeUploads < MAX_PARALLEL) {
      const u = uploads.find((x) => x.state === 'waiting');
      if (!u) break;
      activeUploads++;
      sendUpload(u)
        .then((ok) => { if (ok && u.dest === cwd) uploadRefresh = true; })
        .catch((err) => setUpload(u, 'error', 'Failed: ' + err.message))
        .finally(() => {
          activeUploads--;
          runUploads();
          if (activeUploads === 0 && uploadRefresh && !uploads.some((x) => x.state === 'waiting')) {
            uploadRefresh = false;
            if (!searchTerm) load();
          }
        });
    }
  }

  $('#uploadClear').addEventListener('click', () => {
    for (let i = uploads.length - 1; i >= 0; i--) {
      if (['done', 'error', 'cancelled'].includes(uploads[i].state)) {
        uploads[i].li.remove();
        uploads.splice(i, 1);
      }
    }
    if (!uploads.length) $('#uploads').hidden = true;
    updateUploadSummary();
  });
  $('#uploadHide').addEventListener('click', () => { $('#uploads').hidden = true; });

  /* ---------- wiring ---------- */
  $('#btnUpload').addEventListener('click', () => $('#fileInput').click());
  $('#fileInput').addEventListener('change', (e) => { upload(fromFiles(e.target.files)); e.target.value = ''; });
  $('#btnUploadFolder').addEventListener('click', () => $('#folderInput').click());
  $('#folderInput').addEventListener('change', (e) => { upload(fromFolderInput(e.target.files)); e.target.value = ''; });
  $('#btnDownload').addEventListener('click', () => downloadPaths([...selected]));
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
  dz.addEventListener('drop', (e) => { if (!searchTerm) handleDrop(e); });

  cwd = pathFromHash();
  load();
  renderContinue();
})();
