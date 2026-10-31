/**
 * Palo RSBSA - script.js: single JavaScript file for every page.
 *
 * Sections:
 *   1. CONFIG   - endpoint paths and settings (edit these to match your PHP files)
 *   2. Api      - fetch wrapper for all PHP calls
 *   3. Auth     - login / logout / session helpers (single administrator account)
 *   4. Pages    - one init function per page, chosen by <body data-page="...">
 *   5. Boot     - runs the right page on DOMContentLoaded
 *
 * To add a new page: add its init function to Pages, then set
 * <body data-page="your-page"> in that HTML file and load only this script.
 *
 * Expected PHP response format (JSON):
 *   success: { "success": true,  "message": "...", "data": { ... } }
 *   failure: { "success": false, "message": "...", "errors": { "field": "msg" } }
 */
(function () {
  'use strict';

  /* =====================================================================
   * 1. CONFIG
   * ===================================================================== */
  var CONFIG = {
    API_BASE: 'api',                  // folder that holds your PHP endpoints
    ENDPOINTS: {
      LOGIN: 'login.php',             // POST {username, password}
      LOGOUT: 'logout.php',           // POST
      SESSION: 'session.php',         // GET -> {data: {authenticated, user}}
      DASHBOARD: 'dashboard.php',     // GET -> summary figures (see Pages.dashboard)
      FARMERS: 'farmers.php',         // GET -> paged farmer list (see Pages.farmers)
      DUPLICATES: 'duplicates.php',   // GET -> duplicate flags (see Pages.duplicates)
      UPDATE_DUPLICATE: 'update_duplicate.php', // POST -> resolve / reopen / save remarks
      EXPORT: 'export.php',           // GET -> downloads a report file (see Pages.reports)
      DELETE_FARMER: 'delete_farmer.php',       // POST {id} -> deletes a farmer, parcels and attached files
      UPDATE_PROFILE: 'update_profile.php',     // POST {name} -> changes the logged-in user's display name
      CHANGE_PASSWORD: 'change_password.php',   // POST {current_password, new_password}
      CHECK_LOT: 'check_lot.php',     // GET -> duplicate / remaining-area check (see Pages.register)
      GET_FARMER: 'get_farmer.php',   // GET ?id=  -> one farmer record for updating
      ACTIVITY: 'activity.php',                 // GET -> activity log (see Pages.activity)
      ACTIVITY_CLEAR: 'activity_clear.php',     // POST {period, mode, password, preview?} -> counts or deletes log entries
      LOG_EVENT: 'log_event.php',               // POST {event} -> records a browser-only action (printing, settings)
      PARCEL_HISTORY: 'parcel_history.php',     // GET ?parcel_id= -> history of one parcel
      BACKUP_LIST: 'backup_list.php',           // GET -> saved backups + automatic backup settings
      BACKUP_CREATE: 'backup_create.php',       // POST -> make a backup now
      BACKUP_DOWNLOAD: 'backup_download.php',   // GET ?name= -> download a saved backup
      BACKUP_DELETE: 'backup_delete.php',       // POST {name}
      BACKUP_SETTINGS: 'backup_settings.php',   // POST {auto, frequency, keep}
      BACKUP_RESTORE: 'backup_restore.php',     // POST multipart {password, name | file}
      SAVE_FARMER: 'save_farmer.php'  // POST multipart -> create or update a farmer
    },
    DASHBOARD_URL: 'dashboard.php',  // where to go after a successful login
    LOGIN_URL: 'index.php',
    STORAGE_KEYS: {
      REMEMBERED_USERNAME: 'rsbsa_remembered_username',
      USER: 'rsbsa_user'
    },
    REQUEST_TIMEOUT_MS: 15000,
    SAVE_TIMEOUT_MS: 60000,           // saving uploads can take longer
    MAX_UPLOAD_MB: 5,                 // per attached file
    FARMERS_URL: 'farmers.php',
    DEFAULTS: { municipality: '', province: '' }  // pre-filled on new records, e.g. { municipality: 'Palo', province: 'Leyte' }
  };

  /* =====================================================================
   * 2. Api - fetch wrapper (session is a PHP cookie, so credentials are sent)
   * ===================================================================== */
  function buildUrl(endpoint) {
    return CONFIG.API_BASE.replace(/\/+$/, '') + '/' + endpoint.replace(/^\/+/, '');
  }

  function request(endpoint, options) {
    options = options || {};
    var controller = new AbortController();
    var timer = setTimeout(function () { controller.abort(); }, options.timeout || CONFIG.REQUEST_TIMEOUT_MS);

    var init = {
      method: options.method || 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      signal: controller.signal
    };

    if (options.body instanceof FormData) {
      init.body = options.body;       // browser sets the multipart boundary
    } else if (options.body !== undefined) {
      init.headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(options.body);
    }

    return fetch(buildUrl(endpoint), init)
      .then(function (res) {
        return res.text().then(function (text) {
          var json = null;
          try { json = text ? JSON.parse(text) : null; } catch (e) { /* not JSON */ }

          if (!res.ok || !json || json.success === false) {
            var err = new Error((json && json.message) || 'Request failed (' + res.status + ').');
            err.status = res.status;
            err.errors = (json && json.errors) || {};
            throw err;
          }
          return json;
        });
      })
      .catch(function (err) {
        if (err.name === 'AbortError') {
          var t = new Error('The server took too long to respond. Please try again.');
          t.status = 0;
          t.errors = {};
          throw t;
        }
        if (!('status' in err)) {
          err.message = 'Cannot reach the server. Check your connection.';
          err.status = 0;
          err.errors = {};
        }
        throw err;
      })
      .finally(function () { clearTimeout(timer); });
  }

  var Api = {
    get: function (endpoint) { return request(endpoint); },
    post: function (endpoint, body, timeout) { return request(endpoint, { method: 'POST', body: body || {}, timeout: timeout }); },
    request: request,

    /** Download a file from PHP. Resolves { blob, name }. JSON responses are treated as errors. */
    download: function (endpoint, fallbackName) {
      return fetch(buildUrl(endpoint), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (res) {
          var type = res.headers.get('Content-Type') || '';
          if (!res.ok || type.indexOf('application/json') !== -1) {
            return res.text().then(function (text) {
              var json = null;
              try { json = text ? JSON.parse(text) : null; } catch (e) { /* not JSON */ }
              var err = new Error((json && json.message) || 'Download failed (' + res.status + ').');
              err.status = res.status;
              err.errors = (json && json.errors) || {};
              throw err;
            });
          }
          var cd = res.headers.get('Content-Disposition') || '';
          var m = /filename\*?=(?:UTF-8'')?"?([^";]+)"?/i.exec(cd);
          var name = m ? decodeURIComponent(m[1]) : (fallbackName || 'download');
          return res.blob().then(function (blob) { return { blob: blob, name: name }; });
        })
        .catch(function (err) {
          if (!('status' in err)) {
            err.message = 'Cannot reach the server. Check your connection.';
            err.status = 0;
            err.errors = {};
          }
          throw err;
        });
    },

    /** Hand a blob to the browser as a download. */
    saveFile: function (blob, name) {
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = name;
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    }
  };

  /* =====================================================================
   * 3. Auth - the real session lives in PHP; this only caches user info
   * ===================================================================== */
  function cacheUser(user) {
    try { sessionStorage.setItem(CONFIG.STORAGE_KEYS.USER, JSON.stringify(user || {})); } catch (e) {}
  }

  function clearUser() {
    try { sessionStorage.removeItem(CONFIG.STORAGE_KEYS.USER); } catch (e) {}
  }

  var Auth = {
    getUser: function () {
      try { return JSON.parse(sessionStorage.getItem(CONFIG.STORAGE_KEYS.USER)) || null; }
      catch (e) { return null; }
    },

    login: function (username, password) {
      return Api.post(CONFIG.ENDPOINTS.LOGIN, { username: username, password: password })
        .then(function (res) {
          cacheUser(res.data && res.data.user);
          return res;
        });
    },

    logout: function (reason) {
      return Api.post(CONFIG.ENDPOINTS.LOGOUT, reason ? { reason: reason } : {})
        .catch(function () { /* leave anyway */ })
        .then(function () {
          clearUser();
          window.location.replace(CONFIG.LOGIN_URL);
        });
    },

    /** Resolves true/false depending on the PHP session. */
    check: function () {
      return Api.get(CONFIG.ENDPOINTS.SESSION)
        .then(function (res) {
          var ok = !!(res.data && res.data.authenticated);
          if (ok) cacheUser(res.data.user); else clearUser();
          return ok;
        })
        .catch(function () { clearUser(); return false; });
    },

    /** Call on protected pages: redirects to the login page when not logged in. */
    requireLogin: function () {
      return Auth.check().then(function (ok) {
        if (!ok) window.location.replace(CONFIG.LOGIN_URL);
        return ok;
      });
    },

    /** Call on the login page: skips the form when already logged in. */
    redirectIfLoggedIn: function () {
      return Auth.check().then(function (ok) {
        if (ok) window.location.replace(CONFIG.DASHBOARD_URL);
        return ok;
      });
    }
  };

  /* =====================================================================
   * 4. Pages - one init function per page
   * ===================================================================== */
  var Pages = {};

  /* ---------- Login page (index.php) ---------- */
  Pages.login = function () {
    var form = document.getElementById('login-form');
    var usernameInput = document.getElementById('username');
    var passwordInput = document.getElementById('password');
    var rememberInput = document.getElementById('remember');
    var toggleBtn = document.getElementById('toggle-password');
    var submitBtn = document.getElementById('submit-btn');
    var alertBox = document.getElementById('alert');

    function showAlert(message, type) {
      alertBox.textContent = message;
      alertBox.className = 'alert ' + (type || 'error');
      alertBox.hidden = false;
    }

    function hideAlert() {
      alertBox.hidden = true;
      alertBox.textContent = '';
    }

    var flashMsg = Flash.take();   // e.g. "You were logged out because the system was not used for a while."
    if (flashMsg) showAlert(flashMsg, 'success');

    function setFieldError(input, message) {
      var field = input.closest('.field');
      var err = document.getElementById(input.id + '-error');
      field.classList.toggle('invalid', !!message);
      err.textContent = message || '';
    }

    function clearErrors() {
      setFieldError(usernameInput, '');
      setFieldError(passwordInput, '');
      hideAlert();
    }

    function setLoading(isLoading) {
      submitBtn.disabled = isLoading;
      submitBtn.classList.toggle('loading', isLoading);
      submitBtn.querySelector('.btn-text').textContent = isLoading ? 'Logging in...' : 'Log In';
    }

    function validate() {
      var ok = true;
      if (!usernameInput.value.trim()) {
        setFieldError(usernameInput, 'Username is required.');
        ok = false;
      }
      if (!passwordInput.value) {
        setFieldError(passwordInput, 'Password is required.');
        ok = false;
      }
      return ok;
    }

    // remember username
    try {
      var saved = localStorage.getItem(CONFIG.STORAGE_KEYS.REMEMBERED_USERNAME);
      if (saved) {
        usernameInput.value = saved;
        rememberInput.checked = true;
        passwordInput.focus();
      }
    } catch (e) { /* storage unavailable */ }

    function persistUsername() {
      try {
        if (rememberInput.checked) {
          localStorage.setItem(CONFIG.STORAGE_KEYS.REMEMBERED_USERNAME, usernameInput.value.trim());
        } else {
          localStorage.removeItem(CONFIG.STORAGE_KEYS.REMEMBERED_USERNAME);
        }
      } catch (e) {}
    }

    // show / hide password
    toggleBtn.addEventListener('click', function () {
      var show = passwordInput.type === 'password';
      passwordInput.type = show ? 'text' : 'password';
      toggleBtn.textContent = show ? 'Hide' : 'Show';
      toggleBtn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      passwordInput.focus();
    });

    usernameInput.addEventListener('input', function () { setFieldError(usernameInput, ''); });
    passwordInput.addEventListener('input', function () { setFieldError(passwordInput, ''); });

    // submit
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearErrors();
      if (!validate()) return;

      setLoading(true);

      Auth.login(usernameInput.value.trim(), passwordInput.value)
        .then(function () {
          persistUsername();
          showAlert('Login successful. Redirecting...', 'success');
          window.location.replace(CONFIG.DASHBOARD_URL);
        })
        .catch(function (err) {
          setLoading(false);
          passwordInput.value = '';

          if (err.errors) {
            if (err.errors.username) setFieldError(usernameInput, err.errors.username);
            if (err.errors.password) setFieldError(passwordInput, err.errors.password);
          }

          if (err.status === 401) {
            showAlert(err.message || 'Invalid username or password.');
          } else if (err.status === 429) {
            showAlert(err.message || 'Too many attempts. Please wait and try again.');
          } else {
            showAlert(err.message);
          }
          passwordInput.focus();
        });
    });

    // already logged in?
    Auth.redirectIfLoggedIn();
  };

  /* ---------- Shared helpers for admin pages ---------- */
  var Util = {
    /** Escape text before putting it into innerHTML. */
    esc: function (value) {
      return String(value === null || value === undefined ? '' : value)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    },
    /** Tell the server about something done only in the browser (for the activity log). Never blocks or fails the action. */
    logEvent: function (data) {
      try { Api.post(CONFIG.ENDPOINTS.LOG_EVENT, data).catch(function () {}); } catch (e) { /* ignore */ }
    },
    int: function (n) { return Number(n || 0).toLocaleString('en-PH'); },
    ha: function (n) {
      return Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    date: function (value) {
      if (!value) return '';
      var d = new Date(String(value).replace(' ', 'T'));
      if (isNaN(d.getTime())) return String(value);
      return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }
  };

  /** Confirmation window (works on every page). Resolves true when the admin confirms. */
  var Ui = {
    confirm: function (opts) {
      opts = opts || {};
      return new Promise(function (resolve) {
        var dlg = document.createElement('dialog');
        dlg.className = 'modal modal-sm';
        dlg.setAttribute('aria-label', opts.title || 'Confirm');
        dlg.innerHTML =
          '<header class="modal-head"><h2>' + Util.esc(opts.title || 'Are you sure?') + '</h2></header>' +
          '<div class="modal-body">' + (opts.lines || []).map(function (l, i) {
            return '<p class="' + (i === 0 && opts.lines.length > 1 ? 'confirm-lead' : '') + '">' + Util.esc(l) + '</p>';
          }).join('') + '</div>' +
          '<footer class="modal-foot">' +
            '<button type="button" class="btn-outline" data-r="0">Cancel</button>' +
            '<button type="button" class="' + (opts.danger ? 'btn-danger' : 'btn-solid') + '" data-r="1">' +
              Util.esc(opts.confirmText || 'OK') + '</button>' +
          '</footer>';
        document.body.appendChild(dlg);

        var done = false;
        function finish(value) {
          if (done) return;
          done = true;
          try { dlg.close(); } catch (e) { /* already closed */ }
          dlg.remove();
          resolve(value);
        }
        dlg.addEventListener('click', function (e) {
          var b = e.target.closest('[data-r]');
          if (b) finish(b.getAttribute('data-r') === '1');
          else if (e.target === dlg) finish(false);   // click on the backdrop
        });
        dlg.addEventListener('cancel', function (e) { e.preventDefault(); finish(false); });  // Esc key
        dlg.showModal();
        dlg.querySelector('[data-r="0"]').focus();    // the safe choice is focused first
      });
    }
  };

  /** A one-time message that survives a page change (for example "record deleted"). */
  var Flash = {
    set: function (message) { try { sessionStorage.setItem('rsbsa_flash', message); } catch (e) {} },
    take: function () {
      try {
        var m = sessionStorage.getItem('rsbsa_flash');
        sessionStorage.removeItem('rsbsa_flash');
        return m;
      } catch (e) { return null; }
    }
  };

  /* ---------- Settings: theme + auto-logout ----------
   * Saved in this browser (localStorage "rsbsa_settings") because the system is used offline on one computer:
   *   { theme: "default" | "dark", autoLogout: true|false, autoLogoutMinutes: 15 }
   * The inline script in each page <head> applies the theme before the page is drawn.
   */
  var Settings = (function () {
    var KEY = 'rsbsa_settings';
    var ACTIVE_KEY = 'rsbsa_last_active';     // shared by all open tabs so one busy tab keeps the others logged in
    var MINUTES = [1, 2, 5, 10, 15, 30, 60];
    var WARN_SECONDS = 30;

    function read() {
      try { return JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { return {}; }
    }
    function write(s) { try { localStorage.setItem(KEY, JSON.stringify(s)); } catch (e) { /* storage unavailable */ } }

    function get() {
      var s = read();
      var m = Number(s.autoLogoutMinutes);
      return {
        theme: s.theme === 'dark' ? 'dark' : 'default',
        autoLogout: s.autoLogout === true,
        autoLogoutMinutes: MINUTES.indexOf(m) !== -1 ? m : 15
      };
    }
    function set(patch) {
      var s = get();
      Object.keys(patch).forEach(function (k) { s[k] = patch[k]; });
      write(s);
      applyTheme();
      return s;
    }
    function applyTheme() {
      var root = document.documentElement;
      if (get().theme === 'dark') root.setAttribute('data-theme', 'dark'); else root.removeAttribute('data-theme');
    }

    /* ----- idle watcher (admin pages only) ----- */
    var started = false, warnDlg = null, tick = null, lastWrite = 0;

    function now() { return Date.now(); }
    function touch(force) {
      if (warnDlg) return;                      // while the warning is open only its buttons count
      var t = now();
      if (!force && t - lastWrite < 1000) return;
      lastWrite = t;
      try { localStorage.setItem(ACTIVE_KEY, String(t)); } catch (e) {}
    }
    function lastActive() {
      try { return Number(localStorage.getItem(ACTIVE_KEY)) || now(); } catch (e) { return now(); }
    }

    function closeWarning() {
      if (!warnDlg) return;
      try { warnDlg.close(); } catch (e) {}
      warnDlg.remove();
      warnDlg = null;
    }
    function doLogout() {
      closeWarning();
      clearInterval(tick);
      Flash.set('You were logged out because the system was not used for a while.');
      Auth.logout('inactivity');
    }
    function showWarning(secondsLeft) {
      if (!warnDlg) {
        warnDlg = document.createElement('dialog');
        warnDlg.className = 'modal modal-sm idle-warning';
        warnDlg.innerHTML =
          '<div class="modal-body"><h2 class="modal-title">Still there?</h2>' +
          '<p>You will be logged out in <strong data-s>' + secondsLeft + '</strong> seconds because the system has not been used.</p></div>' +
          '<footer class="modal-foot"><button type="button" class="btn-outline" data-r="0">Log out now</button>' +
          '<button type="button" class="btn-solid" data-r="1">Stay logged in</button></footer>';
        warnDlg.addEventListener('cancel', function (e) { e.preventDefault(); });
        warnDlg.addEventListener('click', function (e) {
          var b = e.target.closest('[data-r]');
          if (!b) return;
          if (b.getAttribute('data-r') === '1') { closeWarning(); touch(true); } else { doLogout(); }
        });
        document.body.appendChild(warnDlg);
        warnDlg.showModal();
        warnDlg.querySelector('[data-r="1"]').focus();
      }
      var el = warnDlg.querySelector('[data-s]');
      if (el) el.textContent = secondsLeft;
    }

    function check() {
      var s = get();
      if (!s.autoLogout) { closeWarning(); return; }
      var limit = s.autoLogoutMinutes * 60;
      var idle = (now() - lastActive()) / 1000;
      if (warnDlg && idle < limit - WARN_SECONDS) { closeWarning(); return; }   // another tab was used
      if (idle >= limit) { doLogout(); return; }
      if (idle >= limit - WARN_SECONDS) showWarning(Math.max(1, Math.ceil(limit - idle)));
    }

    function startIdleWatch() {
      if (started) return;
      started = true;
      touch(true);
      ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'wheel', 'input'].forEach(function (ev) {
        window.addEventListener(ev, function () { touch(false); }, { passive: true, capture: true });
      });
      tick = setInterval(check, 1000);
    }

    return { get: get, set: set, applyTheme: applyTheme, startIdleWatch: startIdleWatch, MINUTES: MINUTES, _check: check };
  })();
  Settings.applyTheme();

  /** Sidebar, top bar and logout: call once on every admin page. */
  var Shell = {
    init: function (activeNav) {
      var sidebar = document.getElementById('sidebar');
      var scrim = document.getElementById('scrim');
      var menuBtn = document.getElementById('menu-btn');
      var logoutBtn = document.getElementById('logout-btn');
      var userName = document.getElementById('user-name');

      // highlight the current page
      var links = document.querySelectorAll('.nav a[data-nav]');
      for (var i = 0; i < links.length; i++) {
        links[i].classList.toggle('active', links[i].getAttribute('data-nav') === activeNav);
      }

      // show who is logged in
      var user = Auth.getUser();
      if (user && userName) userName.textContent = user.name || user.username || 'Administrator';

      // mobile menu
      function setMenu(open) {
        sidebar.classList.toggle('open', open);
        scrim.hidden = !open;
        menuBtn.setAttribute('aria-expanded', String(open));
      }
      menuBtn.addEventListener('click', function () { setMenu(!sidebar.classList.contains('open')); });
      scrim.addEventListener('click', function () { setMenu(false); });

      Settings.startIdleWatch();

      // logout
      logoutBtn.addEventListener('click', function () {
        Ui.confirm({
          title: 'Log out?',
          lines: ['Do you want to log out of the system?', 'Anything you have not saved will be lost.'],
          confirmText: 'Log Out'
        }).then(function (yes) {
          if (!yes) return;
          logoutBtn.disabled = true;
          Auth.logout();
        });
      });
    }
  };

  /* ---------- Dashboard page (dashboard.php) ----------
   * GET api/dashboard.php should return:
   * {
   *   "success": true,
   *   "data": {
   *     "totals": { "farmers": 0, "parcels": 0, "total_area_ha": 0, "duplicate_flags": 0 },
   *     "by_commodity": [ { "commodity": "Rice", "farmers": 0, "area_ha": 0 } ],
   *     "by_barangay":  [ { "barangay": "Capirawan", "farmers": 0, "area_ha": 0 } ],
   *     "by_month":     [ { "month": "2026-10", "label": "Oct 2026", "farmers": 0 } ],   (last 12 months, oldest first)
   *     "recent_updates": [
   *       { "rsbsa_no": "", "name": "", "barangay": "", "total_area_ha": 0, "date_updated": "YYYY-MM-DD HH:MM:SS" }
   *     ]
   *   }
   * }
   */
  Pages.dashboard = function () {
    var alertBox = document.getElementById('dash-alert');
    var refreshBtn = document.getElementById('refresh-btn');
    var lastUpdated = document.getElementById('last-updated');

    function showError(message) {
      alertBox.textContent = message;
      alertBox.className = 'alert error';
      alertBox.hidden = false;
    }

    function setText(id, text) { document.getElementById(id).textContent = text; }

    function renderTotals(t) {
      t = t || {};
      setText('stat-farmers', Util.int(t.farmers));
      setText('stat-parcels', Util.int(t.parcels));
      setText('stat-area', Util.ha(t.total_area_ha));
      setText('stat-duplicates', Util.int(t.duplicate_flags));
    }

    var SVGNS = 'http://www.w3.org/2000/svg';

    var COMMODITY_ORDER = ['Rice', 'Vegetable', 'Root Crops', 'Banana', 'Coconut', 'Livestock', 'Fishery'];   // a commodity keeps its color when the barangay filter changes

    /* donut: share of the total active area per commodity (colors c1..c7 from the stylesheet) */
    function renderCommodity(rows) {
      var box = document.getElementById('commodity-chart');
      rows = (rows || []).filter(function (r) { return Number(r.area_ha) > 0 || Number(r.farmers) > 0; });
      var total = 0;
      rows.forEach(function (r) { total += Number(r.area_ha) || 0; });
      if (!rows.length || total <= 0) { box.innerHTML = '<p class="empty">' + (brgySel.value ? 'No active parcels in this barangay yet.' : 'No data yet.') + '</p>'; return; }

      var R = 56, C = 2 * Math.PI * R, offset = 0, arcs = '', items = '';
      rows.forEach(function (r, i) {
        var share = (Number(r.area_ha) || 0) / total, len = share * C, ci = COMMODITY_ORDER.indexOf(r.commodity), col = 'var(--c' + (((ci < 0 ? i : ci) % 7) + 1) + ')';
        if (len > 0) {
          arcs += '<circle cx="85" cy="85" r="' + R + '" stroke="' + col + '" stroke-dasharray="' + len.toFixed(2) + ' ' + (C - len).toFixed(2) +
            '" stroke-dashoffset="' + (-offset).toFixed(2) + '" transform="rotate(-90 85 85)"><title>' + Util.esc(r.commodity) + ': ' + Util.ha(r.area_ha) + ' ha</title></circle>';
          offset += len;
        }
        items += '<li style="animation-delay:' + (0.25 + i * 0.07).toFixed(2) + 's"><span class="sw" style="background:' + col + '"></span><span class="nm">' + Util.esc(r.commodity) + '</span>' +
          '<span class="vl">' + Util.ha(r.area_ha) + ' ha &middot; ' + Math.round(share * 100) + '%</span></li>';
      });
      box.innerHTML =
        '<svg class="donut" viewBox="0 0 170 170" role="img" aria-label="Share of total active area by commodity">' +
        '<defs><mask id="donut-sweep" maskUnits="userSpaceOnUse" x="0" y="0" width="170" height="170"><circle class="sweep" cx="85" cy="85" r="' + R + '" pathLength="100" transform="rotate(-90 85 85)"/></mask></defs>' +
        '<circle class="donut-bg" cx="85" cy="85" r="' + R + '"/><g mask="url(#donut-sweep)">' + arcs + '</g>' +
        '<text class="donut-num" x="85" y="86">' + Util.ha(total) + '</text><text class="donut-cap" x="85" y="102">hectares</text></svg>' +
        '<ul class="legend">' + items + '</ul>';
    }

    /* horizontal bars: farmers per barangay (top 10) */
    function renderBarangay(rows) {
      var box = document.getElementById('barangay-chart');
      if (!rows || !rows.length) { box.innerHTML = '<p class="empty">No data yet.</p>'; return; }
      var max = 0;
      rows.forEach(function (r) { max = Math.max(max, Number(r.farmers) || 0); });
      box.innerHTML = rows.map(function (r, i) {
        var pct = max > 0 ? Math.round((Number(r.farmers) || 0) / max * 100) : 0;
        return '<div class="bar-row">' +
          '<div class="bar-row-head"><span>' + Util.esc(r.barangay || '(no barangay)') + '</span>' +
          '<span>' + Util.int(r.farmers) + (Number(r.farmers) === 1 ? ' farmer' : ' farmers') + ' &middot; ' + Util.ha(r.area_ha) + ' ha</span></div>' +
          '<div class="bar-track"><div class="bar-fill alt" data-w="' + pct + '" style="width:0;transition-delay:' + (i * 0.06).toFixed(2) + 's"></div></div></div>';
      }).join('');
      setTimeout(function () {
        Array.prototype.forEach.call(box.querySelectorAll('.bar-fill[data-w]'), function (el) { el.style.width = el.getAttribute('data-w') + '%'; });
      }, 40);
    }

    /* columns: registrations per month, last 12 months (current month in amber) */
    var lastMonths = null;
    function renderMonths(rows) {
      lastMonths = rows;
      var box = document.getElementById('month-chart');
      if (!rows || !rows.length) { box.innerHTML = '<p class="empty">No data yet.</p>'; return; }
      var max = 0, sum = 0;
      rows.forEach(function (r) { max = Math.max(max, r.farmers); sum += r.farmers; });
      if (sum === 0) { box.innerHTML = '<p class="empty">No registrations in the last 12 months.</p>'; return; }
      var step = max <= 5 ? 1 : (max <= 10 ? 2 : (max <= 25 ? 5 : (max <= 50 ? 10 : (max <= 100 ? 20 : Math.pow(10, Math.floor(Math.log(max) / Math.LN10)))))); 
      var top = Math.ceil(max / step) * step;
      var W = Math.max(box.clientWidth || 760, 480), H = 230, L = 34, Rt = 8, T = 20, B = 28, cw = (W - L - Rt) / rows.length, bw = Math.min(34, cw * 0.55);
      function y(v) { return T + (H - T - B) * (1 - v / top); }
      var g = '', cols = '';
      for (var v = 0; v <= top; v += step) {
        g += '<line class="grid" x1="' + L + '" x2="' + (W - Rt) + '" y1="' + y(v) + '" y2="' + y(v) + '"/><text class="yl" x="' + (L - 6) + '" y="' + (y(v) + 4) + '">' + v + '</text>';
      }
      rows.forEach(function (r, i) {
        var x = L + cw * i + (cw - bw) / 2, h = (H - T - B) * (r.farmers / top), cx = L + cw * i + cw / 2;
        cols += '<rect class="col' + (i === rows.length - 1 ? ' now' : '') + '" style="animation-delay:' + (i * 0.05).toFixed(2) + 's" x="' + x.toFixed(1) + '" y="' + y(r.farmers).toFixed(1) + '" width="' + bw.toFixed(1) + '" height="' + h.toFixed(1) + '" rx="3"><title>' +
          Util.esc(r.label) + ': ' + r.farmers + ' registered</title></rect>';
        if (r.farmers > 0) cols += '<text class="val" style="animation-delay:' + (0.45 + i * 0.05).toFixed(2) + 's" x="' + cx.toFixed(1) + '" y="' + (y(r.farmers) - 5).toFixed(1) + '">' + r.farmers + '</text>';
        cols += '<text class="mon" x="' + cx.toFixed(1) + '" y="' + (H - 9) + '">' + Util.esc(r.label.replace(/ \d{2}(\d{2})$/, " '$1")) + '</text>';
      });
      box.innerHTML = '<svg width="' + W + '" height="' + H + '" viewBox="0 0 ' + W + ' ' + H + '" role="img" aria-label="Farmers registered per month, last 12 months: ' + sum + ' in total">' + g + cols + '</svg>';
      box.scrollLeft = box.scrollWidth;   // on a narrow screen, show the latest months first
    }

    function renderRecent(rows) {
      var body = document.getElementById('recent-body');
      if (!rows || !rows.length) { body.innerHTML = '<tr><td colspan="5" class="empty">No records yet.</td></tr>'; return; }
      body.innerHTML = rows.map(function (r) {
        return '<tr><td>' + Util.esc(r.rsbsa_no) + '</td>' +
          '<td>' + Util.esc(r.name) + '</td>' +
          '<td>' + Util.esc(r.barangay) + '</td>' +
          '<td class="num">' + Util.ha(r.total_area_ha) + '</td>' +
          '<td>' + Util.esc(Util.date(r.date_updated)) + '</td></tr>';
      }).join('');
    }

    var brgySel = document.getElementById('commodity-barangay');
    function fillBarangays(list) {
      var keep = brgySel.value;
      brgySel.innerHTML = '<option value="">All</option>' + (list || []).map(function (b) {
        return '<option value="' + Util.esc(b) + '">' + Util.esc(b) + '</option>';
      }).join('');
      brgySel.value = keep;
    }

    function load() {
      refreshBtn.disabled = true;
      alertBox.hidden = true;

      Api.get(CONFIG.ENDPOINTS.DASHBOARD + (brgySel.value ? '?barangay=' + encodeURIComponent(brgySel.value) : ''))
        .then(function (res) {
          var d = res.data || {};
          fillBarangays(d.barangays);
          renderTotals(d.totals);
          renderCommodity(d.by_commodity);
          renderBarangay(d.by_barangay);
          renderMonths(d.by_month);
          renderRecent(d.recent_updates);
          lastUpdated.textContent = 'Updated ' + new Date().toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' });
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showError(err.message);
        })
        .then(function () { refreshBtn.disabled = false; });
    }

    var rz = null;
    window.addEventListener('resize', function () { clearTimeout(rz); rz = setTimeout(function () { if (lastMonths) renderMonths(lastMonths); }, 150); });

    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('dashboard');
      refreshBtn.addEventListener('click', load);
      brgySel.addEventListener('change', load);
      load();
    });
  };

  /* ---------- Parcel history window (used by register.php and farmers.php) ----------
   * GET api/parcel_history.php?parcel_id=
   *   -> { success, data: { parcel: {...}, farmer: { name, rsbsa_no },
   *        entries: [ { changed_at, event, changes: [ { label, old, new } ], snapshot: {...} } ] } }   (newest first)
   * Print: the page prints the timeline only. Export: api/export.php?report=parcel_history&parcel_id=&format=xlsx|csv
   */
  var ParcelHistory = (function () {
    var dlg = null, body = null, current = null;

    function ensure() {
      if (dlg) return;
      dlg = document.createElement('dialog');
      dlg.className = 'modal';
      dlg.id = 'history-dialog';
      dlg.innerHTML =
        '<header class="modal-head"><h2>Parcel History</h2>' +
        '<button type="button" class="modal-close" data-h="close" aria-label="Close">&times;</button></header>' +
        '<div class="modal-body" data-h="body"></div>' +
        '<footer class="modal-foot">' +
        '<button type="button" class="btn-outline" data-h="print" disabled>Print</button>' +
        '<button type="button" class="btn-outline" data-h="xlsx" disabled>Export Excel</button>' +
        '<button type="button" class="btn-outline" data-h="csv" disabled>Export CSV</button>' +
        '<span class="spacer"></span>' +
        '<button type="button" class="btn-solid" data-h="close">Close</button></footer>';
      document.body.appendChild(dlg);
      body = dlg.querySelector('[data-h="body"]');

      dlg.addEventListener('click', function (e) {
        var t = e.target.closest('[data-h]');
        if (!t) return;
        var act = t.getAttribute('data-h');
        if (act === 'close') dlg.close();
        else if (act === 'print') print();
        else if (act === 'xlsx' || act === 'csv') download(act, t);
      });
      window.addEventListener('afterprint', function () { document.body.classList.remove('print-history-mode'); });
    }

    function label(v) { return v === '' || v === null || v === undefined ? '(blank)' : String(v); }

    function stamp(v) {
      if (!v) return '';
      var d = new Date(String(v).replace(' ', 'T'));
      if (isNaN(d.getTime())) return String(v);
      return d.toLocaleString('en-PH', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
    }

    function render(h) {
      var p = h.parcel || {}, f = h.farmer || {};
      var html = '<div class="hist-head"><strong>' + Util.esc(f.name || '') + '</strong>' +
        '<span class="muted"> ' + Util.esc(f.rsbsa_no || '') + '</span>' +
        '<div class="muted">Lot ' + Util.esc(p.cadastral_lot_no || '') + (p.barangay ? ', ' + Util.esc(p.barangay) : '') +
        ' &middot; ' + Util.esc(p.commodity || '') + ' &middot; now: ' +
        Util.esc(p.parcel_status === 'Inactive' ? 'In-active' : p.parcel_status || '') + '</div></div>';
      var entries = h.entries || [];
      if (!entries.length) return html + '<p class="muted">No history recorded for this parcel.</p>';

      html += '<ol class="hist-list">';
      entries.forEach(function (e) {
        var s = e.snapshot || {};
        html += '<li class="hist-item"><div class="hist-when"><strong>' + Util.esc(e.event) + '</strong> &middot; ' +
          Util.esc(stamp(e.changed_at)) + '</div>';
        if (e.changes && e.changes.length) {
          html += '<table class="hist-table"><thead><tr><th>Field</th><th>Previous</th><th>New</th></tr></thead><tbody>' +
            e.changes.map(function (c) {
              return '<tr><td>' + Util.esc(c.label) + '</td><td>' + Util.esc(label(c.old)) + '</td><td>' + Util.esc(label(c.new)) + '</td></tr>';
            }).join('') + '</tbody></table>';
        } else {
          html += '<p class="muted">' + (e.event === 'Registered' ? 'Parcel registered.' : 'Record as it was when history started.') + '</p>';
        }
        html += '<p class="hist-snap">Then: Lot ' + Util.esc(s.cadastral_lot_no || '') + ' &middot; Declarant ' + Util.esc(s.tax_declarant || '') +
          ' &middot; ' + Util.esc(s.parcel_status === 'Inactive' ? 'In-active' : s.parcel_status || '') +
          (s.area_ha !== undefined ? ' &middot; ' + Util.ha(s.area_ha) + ' ha' : '') +
          (s.farming_role ? ' &middot; ' + Util.esc(s.farming_role) : '') + '</p></li>';
      });
      return html + '</ol>';
    }

    function setButtons(on) {
      qsa('[data-h="print"], [data-h="xlsx"], [data-h="csv"]').forEach(function (b) { b.disabled = !on; });
    }
    function qsa(sel) { return Array.prototype.slice.call(dlg.querySelectorAll(sel)); }

    function print() {
      if (!current) return;
      var host = document.getElementById('history-print');
      if (!host) {
        host = document.createElement('div');
        host.id = 'history-print';
        document.body.appendChild(host);
      }
      host.innerHTML = '<img class="print-seal" src="assets/img/palo-seal.png" alt=""><h1 class="pf-title">Palo RSBSA - Parcel History</h1>' + render(current) +
        '<p class="muted">Printed ' + Util.esc(new Date().toLocaleString('en-PH')) + '</p>';
      document.body.classList.add('print-history-mode');
      Util.logEvent({ event: 'print_history', parcel_id: current.parcel.id });
      window.print();
    }

    function download(format, btn) {
      if (!current) return;
      var text = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Preparing...';
      var stampName = new Date().toISOString().slice(0, 10);
      Api.download(CONFIG.ENDPOINTS.EXPORT + '?report=parcel_history&parcel_id=' + encodeURIComponent(current.parcel.id) + '&format=' + format,
        'rsbsa-parcel-history-' + stampName + '.' + format)
        .then(function (file) { Api.saveFile(file.blob, file.name); })
        .catch(function (err) { body.insertAdjacentHTML('afterbegin', '<p class="alert error">' + Util.esc(err.message) + '</p>'); })
        .then(function () { btn.disabled = false; btn.textContent = text; });
    }

    return {
      open: function (parcelId) {
        ensure();
        current = null;
        setButtons(false);
        body.innerHTML = '<p class="muted">Loading...</p>';
        if (!dlg.open) dlg.showModal();
        return Api.get(CONFIG.ENDPOINTS.PARCEL_HISTORY + '?parcel_id=' + encodeURIComponent(parcelId))
          .then(function (res) {
            current = res.data;
            body.innerHTML = render(current);
            setButtons(true);
          })
          .catch(function (err) {
            if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
            body.innerHTML = '<p class="alert error">' + Util.esc(err.message) + '</p>';
          });
      }
    };
  })();

  /* ---------- Register / update farmer (register.php) ----------
   * register.php            -> new record
   * register.php?id=123     -> update record 123
   *
   * GET  api/check_lot.php?lot=&barangay=&farmer_id=&surname=&first_name=&spouse_surname=&spouse_first_name=
   *   -> { success, data: {
   *          registered: bool,              // lot + tax declaration already registered to another farmer
   *          spouse_conflict: bool,         // the registered owner is this farmer's spouse
   *          tax_declared_area_ha: 0,       // area on the existing tax declaration
   *          remaining_area_ha: 0,          // declared area minus areas already registered
   *          owners: ["Dela Cruz, Pedro"],
   *          message: "optional text to show" } }
   *
   * GET  api/get_farmer.php?id=  -> { success, data: { farmer: { ...same shape as the payload below...,
   *          date_updated, parcels: [ { id, ..., tax_file_name } ] } } }
   *
   * POST api/save_farmer.php (multipart/form-data)
   *   payload                 JSON string (shape below)
   *   photo                   2x2 picture (required for a new farmer; on update only to replace it)
   *   valid_id_file           scan / photo of the valid ID (same rule)
   *   tax_declaration_{i}     file for parcel i (only if a new file was chosen)
   *   supporting_docs_{i}[]   files for parcel i (only when the lot is a duplicate)
   *   -> { success, message, data: { id, rsbsa_no, flagged: bool } }
   *   Validation errors: HTTP 422 { success:false, message, errors: { "surname": "...", "parcels.0.area_ha": "..." } }
   *
   * payload = {
   *   id, rsbsa_no, surname, first_name, middle_name, suffix, birth_date, valid_id_type, valid_id_no,
   *   address: { zone_sitio, barangay, municipality, province },
   *   civil_status: Single | Married | Widowed | Separated | Annulled,
   *   spouse: { surname, first_name, middle_name } | null, is_married_woman, maiden_name,
   *   children: [ "name", ... ],
   *   parcels: [ { id, cadastral_lot_no, area_ha, tax_declared_area_ha, cultivated_area_ha,
   *                location: { zone_sitio, barangay, municipality, province },
   *                commodity, crop_areas: { rice, vegetable, root_crops, banana, coconut, fruit_bearing } | null,
   *                livestock: [ { type, heads } ] | null, fish_size | null,
   *                farming_role, tax_declarant, association, parcel_status: Active | Inactive | Deceased,
   *                duplicate_flag, keep_existing_tax_file } ]
   * }
   * When updating, parcels that are missing from the payload were removed by the admin.
   */
  Pages.register = function () {
    var CROP_COMMODITIES = ['Rice', 'Vegetable', 'Root Crops', 'Banana', 'Coconut'];
    var CROP_KEYS = ['rice', 'vegetable', 'root_crops', 'banana', 'coconut', 'fruit_bearing'];
    var ANIMALS = ['Cow', 'Carabao', 'Sheep', 'Goat', 'Swine', 'Poultry', 'Other'];
    var ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];

    // server error keys that do not match a data-f name directly
    var KEY_MAP = {
      'address.zone_sitio': 'addr_zone_sitio', 'address.barangay': 'addr_barangay',
      'address.municipality': 'addr_municipality', 'address.province': 'addr_province',
      'spouse.surname': 'spouse_surname', 'spouse.first_name': 'spouse_first_name',
      'spouse.middle_name': 'spouse_middle_name'
    };
    var PARCEL_KEY_MAP = {
      'location.zone_sitio': 'loc_zone_sitio', 'location.barangay': 'loc_barangay',
      'location.municipality': 'loc_municipality', 'location.province': 'loc_province'
    };

    function $(id) { return document.getElementById(id); }
    function qa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
    function fld(root, name) { return root.querySelector('[data-f="' + name + '"]'); }
    function val(root, name) { var el = fld(root, name); return el ? String(el.value).trim() : ''; }
    function num(root, name) { var v = val(root, name); return v === '' ? null : Number(v); }
    function setVal(root, name, v) { var el = fld(root, name); if (el && v !== null && v !== undefined) el.value = v; }
    function checked(root, name) { var el = fld(root, name); return !!(el && el.checked); }
    function round4(n) { return Math.round(n * 10000) / 10000; }

    var form = $('reg-form');
    var alertBox = $('reg-alert');
    var childrenList = $('children-list');
    var parcelsList = $('parcels-list');
    var saveBtn = $('save-btn');
    var tplParcel = $('tpl-parcel');
    var tplChild = $('tpl-child');
    var editId = new URLSearchParams(window.location.search).get('id');
    var dirty = false;
    var lastNameInput = null;

    /* ---------- alerts and field errors ---------- */
    function showAlert(message, type) {
      alertBox.textContent = message;
      alertBox.className = 'alert ' + (type || 'error');
      alertBox.hidden = false;
      alertBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function hideAlert() { alertBox.hidden = true; alertBox.textContent = ''; }

    function setInvalid(input, msg) {
      var f = input.closest('.field');
      if (!f) return;
      f.classList.add('invalid');
      var e = f.querySelector('.field-error');
      if (e) e.textContent = msg;
    }
    function clearInvalid(input) {
      var f = input.closest && input.closest('.field');
      if (!f) return;
      f.classList.remove('invalid');
      var e = f.querySelector('.field-error');
      if (e) e.textContent = '';
    }
    function clearAllErrors() {
      qa('.field.invalid', form).forEach(function (f) { f.classList.remove('invalid'); });
      qa('.field-error', form).forEach(function (e) { e.textContent = ''; });
    }

    function setLoading(on) {
      saveBtn.disabled = on;
      saveBtn.classList.toggle('loading', on);
      saveBtn.querySelector('.btn-text').textContent = on ? 'Saving...' : (editId ? 'Save Changes' : 'Save Record');
    }

    /* ---------- children ---------- */
    function addChild(name) {
      var row = tplChild.content.firstElementChild.cloneNode(true);
      if (name) fld(row, 'child_name').value = name;
      childrenList.appendChild(row);
      return row;
    }

    /* ---------- parcels ---------- */
    function parcelCards() { return qa('.parcel-card', parcelsList); }

    function renumber() {
      var cards = parcelCards();
      cards.forEach(function (c, i) {
        c.querySelector('.parcel-no').textContent = i + 1;
        c.querySelector('[data-act="remove-parcel"]').hidden = cards.length === 1;
      });
    }

    function addAnimalRow(card, type, heads) {
      var opts = ANIMALS.map(function (a) {
        return '<option' + (a === type ? ' selected' : '') + '>' + Util.esc(a) + '</option>';
      }).join('');
      var row = document.createElement('div');
      row.className = 'inline-row animal-row';
      row.innerHTML =
        '<div class="field"><label><span class="lbl sr-only">Animal</span><select data-f="animal_type">' + opts + '</select></label><small class="field-error"></small></div>' +
        '<div class="field"><label><span class="lbl sr-only">No. of heads</span><input type="number" data-f="animal_heads" min="0" step="1" inputmode="numeric" placeholder="No. of heads"></label><small class="field-error"></small></div>' +
        '<button type="button" class="link-danger" data-act="remove-animal">Remove</button>';
      if (heads !== undefined && heads !== null) fld(row, 'animal_heads').value = heads;
      card.querySelector('[data-list="animals"]').appendChild(row);
    }

    /** Show only the groups that apply to the chosen commodity. */
    function updateGroups(card) {
      var c = val(card, 'commodity');
      card.querySelector('[data-group="crop"]').hidden = CROP_COMMODITIES.indexOf(c) === -1;
      card.querySelector('[data-group="livestock"]').hidden = c !== 'Livestock';
      card.querySelector('[data-group="fishery"]').hidden = c !== 'Fishery';
      if (c === 'Livestock' && !qa('.animal-row', card).length) addAnimalRow(card);
    }

    function fillParcel(card, p) {
      var loc = p.location || {};
      var crops = p.crop_areas || {};
      card.dataset.parcelId = p.id || '';
      var hb = card.querySelector('[data-act="parcel-history"]');
      if (hb) hb.hidden = !p.id;
      setVal(card, 'cadastral_lot_no', p.cadastral_lot_no);
      setVal(card, 'area_ha', p.area_ha);
      setVal(card, 'tax_declared_area_ha', p.tax_declared_area_ha);
      setVal(card, 'cultivated_area_ha', p.cultivated_area_ha);
      setVal(card, 'loc_zone_sitio', loc.zone_sitio);
      setVal(card, 'loc_barangay', loc.barangay);
      setVal(card, 'loc_municipality', loc.municipality);
      setVal(card, 'loc_province', loc.province);
      setVal(card, 'commodity', p.commodity);
      setVal(card, 'fish_size', p.fish_size);
      setVal(card, 'farming_role', p.farming_role);
      setVal(card, 'tax_declarant', p.tax_declarant);
      setVal(card, 'association', p.association);
      setVal(card, 'parcel_status', p.parcel_status || 'Active');
      syncParcelStatus(card);
      CROP_KEYS.forEach(function (k) { setVal(card, 'crop_' + k, crops[k]); });
      (p.livestock || []).forEach(function (a) { addAnimalRow(card, a.type, a.heads); });
      if (p.tax_file_name) {
        card.dataset.existingFile = p.tax_file_name;
        var note = card.querySelector('[data-role="existing-file"]');
        var link = p.tax_file_url && !/^\s*(javascript|data|vbscript):/i.test(p.tax_file_url)
          ? ' (<a href="' + Util.esc(p.tax_file_url) + '" target="_blank" rel="noopener">open</a>)' : '';
        note.innerHTML = 'Current file: ' + Util.esc(p.tax_file_name) + link + '. Choose a new file only to replace it.';
        note.hidden = false;
      }
    }

    function addParcel(p) {
      var card = tplParcel.content.firstElementChild.cloneNode(true);
      parcelsList.appendChild(card);
      if (p) {
        fillParcel(card, p);
      } else {
        setVal(card, 'loc_municipality', CONFIG.DEFAULTS.municipality);
        setVal(card, 'loc_province', CONFIG.DEFAULTS.province);
      }
      updateGroups(card);
      renumber();
      updateTotals();
      return card;
    }

    function copyAddress(card) {
      setVal(card, 'loc_zone_sitio', val(form, 'addr_zone_sitio'));
      setVal(card, 'loc_barangay', val(form, 'addr_barangay'));
      setVal(card, 'loc_municipality', val(form, 'addr_municipality'));
      setVal(card, 'loc_province', val(form, 'addr_province'));
      qa('[data-f^="loc_"]', card).forEach(clearInvalid);
      scheduleLotCheck(card);
    }

    /* ---------- running totals ---------- */
    function updateTotals() {
      var total = 0, cultivated = 0;
      var cards = parcelCards();
      var inactive = 0;
      cards.forEach(function (c) {
        // Inactive / Deceased parcels are kept on record but not counted
        if (val(c, 'parcel_status') && val(c, 'parcel_status') !== 'Active') { inactive++; return; }
        total += num(c, 'area_ha') || 0;
        cultivated += num(c, 'cultivated_area_ha') || 0;
      });
      $('sum-parcels').textContent = cards.length + (inactive ? ' (' + inactive + ' not counted)' : '');
      $('sum-area').textContent = Util.ha(total);
      $('sum-cultivated').textContent = Util.ha(cultivated);
    }

    /* ---------- family toggles ---------- */
    function syncFamily() {
      var cs = val(form, 'civil_status');
      $('maiden-group').hidden = !checked(form, 'is_married_woman');
      $('spouse-group').hidden = cs === 'Single';
      $('spouse-hint').hidden = cs === 'Single' || cs === 'Married' || cs === '';
    }

    function syncParcelStatus(card) {
      var st = val(card, 'parcel_status') || 'Active';
      var hint = card.querySelector('[data-role="status-hint"]');
      card.classList.toggle('parcel-off', st !== 'Active');
      if (hint) {
        hint.textContent = st === 'Active'
          ? 'Active parcels are counted in the farmer\'s total area.'
          : 'This parcel is kept on record but its area is NOT counted in the farmer\'s total area.';
      }
    }

    /* ---------- 2x2 picture preview ---------- */
    function showPhoto(src) {
      var img = $('photo-preview');
      if (src) { img.src = src; }
      img.hidden = !src;
      $('photo-empty').hidden = !!src;
    }

    function existingFileNote(role, name, url) {
      var note = form.querySelector('[data-role="' + role + '"]');
      if (!name) { note.hidden = true; return; }
      var link = url && !/^\s*(javascript|data|vbscript):/i.test(url)
        ? ' (<a href="' + Util.esc(url) + '" target="_blank" rel="noopener">open</a>)' : '';
      note.innerHTML = 'Current file: ' + Util.esc(name) + link + '. Choose a new file only to replace it.';
      note.hidden = false;
    }

    /* ---------- duplicate lot check (server decides, form shows the result) ---------- */
    function setNotice(card, type, text) {
      var n = card.querySelector('[data-role="lot-notice"]');
      if (!type) { n.hidden = true; n.textContent = ''; n.className = 'notice'; return; }
      n.className = 'notice ' + type;
      n.textContent = text;
      n.hidden = false;
    }

    function scheduleLotCheck(card) {
      clearTimeout(card._lotTimer);
      card._lotTimer = setTimeout(function () { runLotCheck(card); }, 450);
    }

    function runLotCheck(card) {
      var lot = val(card, 'cadastral_lot_no');
      if (!lot) { applyLotResult(card, {}); return; }

      var q = {
        lot: lot,
        barangay: val(card, 'loc_barangay'),
        surname: val(form, 'surname'),
        first_name: val(form, 'first_name'),
        civil_status: val(form, 'civil_status'),
        spouse_surname: val(form, 'spouse_surname'),
        spouse_first_name: val(form, 'spouse_first_name')
      };
      if (editId) q.farmer_id = editId;
      var query = Object.keys(q).map(function (k) { return k + '=' + encodeURIComponent(q[k]); }).join('&');

      var token = card._lotToken = (card._lotToken || 0) + 1;
      Api.get(CONFIG.ENDPOINTS.CHECK_LOT + '?' + query)
        .then(function (res) { if (token === card._lotToken) applyLotResult(card, res.data || {}); })
        .catch(function () { /* the server validates again on save */ });
    }

    function applyLotResult(card, d) {
      var support = card.querySelector('[data-group="support"]');
      var taxField = fld(card, 'tax_declared_area_ha');

      delete card.dataset.duplicate;
      delete card.dataset.block;
      delete card.dataset.remaining;
      support.hidden = true;

      if (d.registered && d.tax_declared_area_ha && !taxField.value) {
        taxField.value = d.tax_declared_area_ha;
        updateTotals();
      }

      if (d.spouse_conflict) {
        card.dataset.block = '1';
        setNotice(card, 'error', d.message ||
          'Your spouse is already registered with this lot. Use the Update form of the registered spouse instead.');
      } else if (d.registered) {
        card.dataset.duplicate = '1';
        support.hidden = false;
        var text = d.message || 'This lot is already registered' +
          (d.owners && d.owners.length ? ' to ' + d.owners.join('; ') : '') + '.';
        if (d.remaining_area_ha !== undefined && d.remaining_area_ha !== null) {
          card.dataset.remaining = d.remaining_area_ha;
          text += ' Remaining area on the tax declaration: ' + Util.ha(d.remaining_area_ha) + ' ha.';
        }
        text += ' Supporting documents are required, and the record will be flagged as a duplication.';
        setNotice(card, 'warn', text);
      } else if (d.needs_barangay) {
        setNotice(card, 'info', 'Enter the farm Barangay so the system can check whether this lot is already registered.');
      } else {
        setNotice(card, null);
      }
    }

    /* ---------- file checks ---------- */
    function checkFiles(input) {
      var files = Array.prototype.slice.call(input.files || []);
      for (var i = 0; i < files.length; i++) {
        var ext = files[i].name.split('.').pop().toLowerCase();
        var imageOnly = input.getAttribute('data-f') === 'photo';
        if (ALLOWED_EXT.indexOf(ext) === -1 || (imageOnly && ext === 'pdf')) {
          input.value = '';
          setInvalid(input, imageOnly ? 'Only JPG or PNG pictures are allowed.' : 'Only PDF, JPG or PNG files are allowed.');
          return false;
        }
        if (files[i].size > CONFIG.MAX_UPLOAD_MB * 1024 * 1024) {
          input.value = '';
          setInvalid(input, 'Each file must be ' + CONFIG.MAX_UPLOAD_MB + ' MB or smaller.');
          return false;
        }
      }
      return true;
    }

    /* ---------- validation ---------- */
    function validate() {
      var first = null;
      function bad(el, msg) { if (!el) return; setInvalid(el, msg); if (!first) first = el; }
      function req(root, name, msg) {
        var el = fld(root, name);
        if (el && !String(el.value).trim()) bad(el, msg || 'This field is required.');
      }

      // basic information
      ['surname', 'first_name', 'valid_id_type', 'valid_id_no', 'addr_barangay', 'addr_municipality', 'addr_province']
        .forEach(function (n) { req(form, n); });

      req(form, 'civil_status', 'Select the civil status.');
      if (!editId) {
        if (!fld(form, 'photo').files.length) bad(fld(form, 'photo'), 'Attach the 2x2 picture.');
        if (!fld(form, 'valid_id_file').files.length) bad(fld(form, 'valid_id_file'), 'Attach the valid ID.');
      }

      var dobEl = fld(form, 'birth_date');
      if (!dobEl.value) {
        bad(dobEl, 'Date of birth is required.');
      } else {
        var dob = new Date(dobEl.value + 'T00:00:00');
        if (isNaN(dob.getTime()) || dob > new Date() || dob.getFullYear() < 1900) bad(dobEl, 'Enter a valid date of birth.');
      }

      // family
      var spouseAny = val(form, 'civil_status') !== 'Single' &&
        (val(form, 'spouse_surname') || val(form, 'spouse_first_name') || val(form, 'spouse_middle_name'));
      if (spouseAny) {
        req(form, 'spouse_surname', 'Spouse surname is required.');
        req(form, 'spouse_first_name', 'Spouse first name is required.');
      }
      if (checked(form, 'is_married_woman')) req(form, 'maiden_name', 'Mother maiden name is required for a married woman.');
      qa('[data-f="child_name"]', childrenList).forEach(function (el) {
        if (!el.value.trim()) bad(el, 'Enter the child\'s name or remove this row.');
      });

      // parcels
      var cards = parcelCards();
      if (!cards.length) { showAlert('Add at least one parcel.'); return form; }

      var lotGroups = {};
      cards.forEach(function (card) {
        ['cadastral_lot_no', 'loc_barangay', 'loc_municipality', 'loc_province', 'commodity', 'farming_role', 'tax_declarant']
          .forEach(function (n) { req(card, n); });

        var area = num(card, 'area_ha');
        var declared = num(card, 'tax_declared_area_ha');
        var cult = num(card, 'cultivated_area_ha');
        var areaEl = fld(card, 'area_ha');
        var declEl = fld(card, 'tax_declared_area_ha');
        var cultEl = fld(card, 'cultivated_area_ha');

        if (area === null || !(area > 0)) bad(areaEl, 'Enter an area greater than 0.');
        if (declared === null || !(declared > 0)) bad(declEl, 'Enter the area shown on the tax declaration.');
        if (cult === null || cult < 0) bad(cultEl, 'Enter the cultivated area (0 or more).');

        if (area > 0 && declared > 0 && area > declared) bad(areaEl, 'Area cannot exceed the tax declaration area (' + Util.ha(declared) + ' ha).');
        if (area > 0 && cult !== null && cult > area) bad(cultEl, 'Cultivated area cannot exceed the parcel area.');

        if (card.dataset.remaining !== undefined && card.dataset.remaining !== '' && area > Number(card.dataset.remaining)) {
          bad(areaEl, 'Only ' + Util.ha(card.dataset.remaining) + ' ha remain on this tax declaration.');
        }

        // two parcels of the same lot in this form must not exceed the tax declaration
        var lot = val(card, 'cadastral_lot_no').toLowerCase();
        if (lot) {
          var key = lot + '|' + val(card, 'loc_barangay').toLowerCase();
          lotGroups[key] = lotGroups[key] || { sum: 0, declared: declared };
          lotGroups[key].sum += area || 0;
          if (declared > 0 && lotGroups[key].sum > lotGroups[key].declared + 1e-9 && !(area > declared)) {
            bad(areaEl, 'Parcels with this lot number add up to more than the tax declaration area.');
          }
        }

        // commodity specifics
        var c = val(card, 'commodity');
        if (CROP_COMMODITIES.indexOf(c) !== -1) {
          CROP_KEYS.forEach(function (k) {
            var el = fld(card, 'crop_' + k);
            if (el.value !== '' && Number(el.value) < 0) bad(el, 'Must be 0 or more.');
          });
        }
        if (c === 'Fishery') req(card, 'fish_size', 'Enter the size of the fish cage / pond.');
        if (c === 'Livestock') {
          var rows = qa('.animal-row', card);
          var any = false;
          rows.forEach(function (r) {
            var h = fld(r, 'animal_heads');
            if (h.value === '' || Number(h.value) < 0 || Math.floor(Number(h.value)) !== Number(h.value)) {
              bad(h, 'Enter a whole number.');
            } else if (Number(h.value) > 0) { any = true; }
          });
          if (!any && !first) bad(fld(rows[0], 'animal_heads'), 'Enter the number of heads.');
        }

        // tax declaration file
        var tf = fld(card, 'tax_file');
        if (!tf.files.length && !card.dataset.existingFile) bad(tf, 'Attach the tax declaration.');

        // lot flags from the server check
        if (card.dataset.block === '1') bad(fld(card, 'cadastral_lot_no'), 'Not allowed: your spouse is already registered with this lot.');
        if (card.dataset.duplicate === '1') {
          var sd = fld(card, 'support_docs');
          if (!sd.files.length) bad(sd, 'Attach the supporting documents.');
        }
      });

      return first;
    }

    /* ---------- build the request ---------- */
    function collectParcel(card) {
      var c = val(card, 'commodity');
      var p = {
        id: card.dataset.parcelId || null,
        cadastral_lot_no: val(card, 'cadastral_lot_no'),
        area_ha: round4(num(card, 'area_ha') || 0),
        tax_declared_area_ha: round4(num(card, 'tax_declared_area_ha') || 0),
        cultivated_area_ha: round4(num(card, 'cultivated_area_ha') || 0),
        parcel_status: val(card, 'parcel_status') || 'Active',
        location: {
          zone_sitio: val(card, 'loc_zone_sitio'),
          barangay: val(card, 'loc_barangay'),
          municipality: val(card, 'loc_municipality'),
          province: val(card, 'loc_province')
        },
        commodity: c,
        crop_areas: null,
        livestock: null,
        fish_size: null,
        farming_role: val(card, 'farming_role'),
        tax_declarant: val(card, 'tax_declarant'),
        association: val(card, 'association'),
        duplicate_flag: card.dataset.duplicate === '1',
        keep_existing_tax_file: !!card.dataset.existingFile && !fld(card, 'tax_file').files.length
      };

      if (CROP_COMMODITIES.indexOf(c) !== -1) {
        p.crop_areas = {};
        CROP_KEYS.forEach(function (k) { p.crop_areas[k] = round4(num(card, 'crop_' + k) || 0); });
      }
      if (c === 'Livestock') {
        p.livestock = qa('.animal-row', card).map(function (r) {
          return { type: val(r, 'animal_type'), heads: Number(val(r, 'animal_heads')) || 0 };
        });
      }
      if (c === 'Fishery') p.fish_size = val(card, 'fish_size');
      return p;
    }

    function buildFormData() {
      var spouseAny = val(form, 'civil_status') !== 'Single' &&
        (val(form, 'spouse_surname') || val(form, 'spouse_first_name') || val(form, 'spouse_middle_name'));
      var married = checked(form, 'is_married_woman');

      var payload = {
        id: editId || null,
        rsbsa_no: val(form, 'rsbsa_no'),
        surname: val(form, 'surname'),
        first_name: val(form, 'first_name'),
        middle_name: val(form, 'middle_name'),
        suffix: val(form, 'suffix'),
        birth_date: val(form, 'birth_date'),
        valid_id_type: val(form, 'valid_id_type'),
        valid_id_no: val(form, 'valid_id_no'),
        address: {
          zone_sitio: val(form, 'addr_zone_sitio'),
          barangay: val(form, 'addr_barangay'),
          municipality: val(form, 'addr_municipality'),
          province: val(form, 'addr_province')
        },
        spouse: spouseAny ? {
          surname: val(form, 'spouse_surname'),
          first_name: val(form, 'spouse_first_name'),
          middle_name: val(form, 'spouse_middle_name')
        } : null,
        civil_status: val(form, 'civil_status'),
        is_married_woman: married,
        maiden_name: married ? val(form, 'maiden_name') : '',
        children: qa('[data-f="child_name"]', childrenList).map(function (el) { return el.value.trim(); }),
        parcels: parcelCards().map(collectParcel)
      };

      var fd = new FormData();
      fd.append('payload', JSON.stringify(payload));
      ['photo', 'valid_id_file'].forEach(function (n) {
        var el = fld(form, n);
        if (el.files.length) fd.append(n, el.files[0]);
      });
      parcelCards().forEach(function (card, i) {
        var tf = fld(card, 'tax_file');
        if (tf.files.length) fd.append('tax_declaration_' + i, tf.files[0]);
        if (card.dataset.duplicate === '1') {
          Array.prototype.forEach.call(fld(card, 'support_docs').files, function (f) {
            fd.append('supporting_docs_' + i + '[]', f);
          });
        }
      });
      return fd;
    }

    /* ---------- server-side errors ---------- */
    function applyServerErrors(errors) {
      var leftovers = [];
      var firstEl = null;

      Object.keys(errors || {}).forEach(function (key) {
        var target = null;
        var parts = key.split('.');

        if (parts[0] === 'parcels' && parts.length >= 3) {
          var card = parcelCards()[Number(parts[1])];
          var rest = parts.slice(2).join('.');
          if (card) target = fld(card, PARCEL_KEY_MAP[rest] || rest);
        } else if (parts[0] === 'children' && parts.length === 2) {
          var rows = qa('[data-f="child_name"]', childrenList);
          target = rows[Number(parts[1])] || null;
        } else {
          target = fld(form, KEY_MAP[key] || key);
        }

        if (target) {
          setInvalid(target, errors[key]);
          if (!firstEl) firstEl = target;
        } else {
          leftovers.push(errors[key]);
        }
      });

      if (firstEl) {
        firstEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstEl.focus({ preventScroll: true });
      }
      return leftovers;
    }

    /* ---------- submit ---------- */
    function resetForm() {
      form.reset();
      childrenList.innerHTML = '';
      parcelsList.innerHTML = '';
      addParcel();
      showPhoto(null);
      syncFamily();
      $('date-updated').value = Util.date(new Date().toISOString());
      updateTotals();
      dirty = false;
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      hideAlert();
      clearAllErrors();

      var first = validate();
      if (first) {
        if (first !== form) {
          showAlert('Please fix the highlighted fields.');
          first.scrollIntoView({ behavior: 'smooth', block: 'center' });
          first.focus({ preventScroll: true });
        }
        return;
      }

      setLoading(true);
      Api.post(CONFIG.ENDPOINTS.SAVE_FARMER, buildFormData(), CONFIG.SAVE_TIMEOUT_MS)
        .then(function (res) {
          var d = res.data || {};
          var msg = res.message || 'Record saved.';
          if (d.rsbsa_no) msg += ' RSBSA No.: ' + d.rsbsa_no + '.';
          dirty = false;

          if (editId) {
            showAlert(msg, d.flagged ? 'error' : 'success');
          } else {
            resetForm();
            showAlert(msg + ' The form is ready for the next farmer.', d.flagged ? 'error' : 'success');
          }
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          var left = applyServerErrors(err.errors);
          showAlert(err.message + (left.length ? ' ' + left.join(' ') : ''));
        })
        .then(function () { setLoading(false); });
    });

    /* ---------- events (delegated) ---------- */
    form.addEventListener('input', function (e) {
      var t = e.target;
      clearInvalid(t);
      dirty = true;
      var name = t.getAttribute('data-f');
      if (name === 'area_ha' || name === 'cultivated_area_ha') updateTotals();
    });

    form.addEventListener('change', function (e) {
      var t = e.target;
      var name = t.getAttribute('data-f');
      var card = t.closest('.parcel-card');

      if (name === 'is_married_woman') syncFamily();
      if (name === 'civil_status') { syncFamily(); parcelCards().forEach(scheduleLotCheck); }
      if (card && name === 'parcel_status') { syncParcelStatus(card); updateTotals(); }
      if (name === 'photo') {
        if (checkFiles(t) && t.files.length) showPhoto(URL.createObjectURL(t.files[0]));
        return;
      }
      if (t.type === 'file') checkFiles(t);
      if (card && name === 'commodity') updateGroups(card);
      if (card && (name === 'cadastral_lot_no' || name === 'loc_barangay')) scheduleLotCheck(card);
      if (!card && (name === 'surname' || name === 'first_name' || name === 'spouse_surname' || name === 'spouse_first_name')) {
        parcelCards().forEach(scheduleLotCheck);
      }
    });

    form.addEventListener('focusin', function (e) {
      if (e.target.classList && e.target.classList.contains('name-input')) lastNameInput = e.target;
    });

    form.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-act], [data-insert]');
      if (!btn) return;

      // Ñ / ñ helper
      if (btn.hasAttribute('data-insert')) {
        var target = lastNameInput || fld(form, 'surname');
        var ch = btn.getAttribute('data-insert');
        var s = target.selectionStart === null ? target.value.length : target.selectionStart;
        var en = target.selectionEnd === null ? s : target.selectionEnd;
        target.value = target.value.slice(0, s) + ch + target.value.slice(en);
        target.focus();
        target.setSelectionRange(s + ch.length, s + ch.length);
        target.dispatchEvent(new Event('input', { bubbles: true }));
        return;
      }

      var act = btn.getAttribute('data-act');
      var card = btn.closest('.parcel-card');

      if (act === 'add-child') {
        addChild().querySelector('input').focus();
      } else if (act === 'remove-child') {
        btn.closest('.child-row').remove();
        dirty = true;
      } else if (act === 'add-parcel') {
        var c = addParcel();
        c.scrollIntoView({ behavior: 'smooth', block: 'start' });
        dirty = true;
      } else if (act === 'parcel-history') {
        if (card.dataset.parcelId) ParcelHistory.open(card.dataset.parcelId);
      } else if (act === 'remove-parcel') {
        if (parcelCards().length > 1) {
          var saved = !!card.dataset.parcelId;   // already stored in the database
          var label = 'Parcel ' + card.querySelector('.parcel-no').textContent +
            (val(card, 'cadastral_lot_no') ? ' (Lot ' + val(card, 'cadastral_lot_no') + ')' : '');
          Ui.confirm({
            title: 'Remove this parcel?',
            danger: true,
            confirmText: 'Remove parcel',
            lines: saved
              ? [label, 'The parcel and its attached files (tax declaration and any supporting documents) will be permanently deleted when you click Save Changes.']
              : [label, 'This parcel has not been saved yet and will simply be removed from the form.']
          }).then(function (yes) {
            if (!yes) return;
            card.remove();
            renumber();
            updateTotals();
            dirty = true;
          });
        }
      } else if (act === 'add-animal') {
        addAnimalRow(card);
      } else if (act === 'remove-animal') {
        btn.closest('.animal-row').remove();
        dirty = true;
      } else if (act === 'copy-address') {
        copyAddress(card);
      }
    });

    $('delete-btn').addEventListener('click', function () {
      var name = [val(form, 'surname'), [val(form, 'first_name'), val(form, 'middle_name'), val(form, 'suffix')].filter(Boolean).join(' ')]
        .filter(Boolean).join(', ');
      var n = qa('.parcel-card', parcelsList).filter(function (c) { return !!c.dataset.parcelId; }).length;
      Ui.confirm({
        title: 'Delete this farmer?',
        danger: true,
        confirmText: 'Delete permanently',
        lines: [
          name || 'This farmer',
          'The farmer record' + (n ? ', ' + n + ' parcel(s)' : '') +
            ' and all attached files (tax declarations and supporting documents) will be permanently deleted. Unsaved changes on this form are discarded.',
          'This cannot be undone.'
        ]
      }).then(function (yes) {
        if (!yes) return;
        $('delete-btn').disabled = true;
        Api.post(CONFIG.ENDPOINTS.DELETE_FARMER, { id: editId })
          .then(function (res) {
            dirty = false;
            Flash.set(res.message || 'Farmer deleted.');
            window.location.replace(CONFIG.FARMERS_URL);
          })
          .catch(function (err) {
            if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
            $('delete-btn').disabled = false;
            showAlert(err.message);
          });
      });
    });

    $('cancel-btn').addEventListener('click', function () {
      if (!dirty || window.confirm('Discard the changes on this form?')) {
        dirty = false;
        window.location.href = CONFIG.DASHBOARD_URL;
      }
    });

    window.addEventListener('beforeunload', function (e) {
      if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });

    /* ---------- update mode: load the existing record ---------- */
    function loadFarmer() {
      $('page-title').textContent = 'Update Farmer Record';
      $('delete-btn').hidden = false;
      var parcelHint = $('sec-parcels').closest('section').querySelector('.hint');
      parcelHint.textContent += ' To remove a parcel use its Remove Parcel button; its attached files are deleted when you save.' +
        ' A farmer needs at least one parcel; to remove everything use Delete Farmer.';
      document.title = 'Update Farmer | Palo RSBSA';
      saveBtn.querySelector('.btn-text').textContent = 'Save Changes';
      $('photo-req').hidden = true;
      $('idfile-req').hidden = true;
      showAlert('Loading record...', 'success');

      Api.get(CONFIG.ENDPOINTS.GET_FARMER + '?id=' + encodeURIComponent(editId))
        .then(function (res) {
          var f = (res.data && res.data.farmer) || {};
          var a = f.address || {};
          var s = f.spouse || {};

          ['rsbsa_no', 'surname', 'first_name', 'middle_name', 'suffix', 'birth_date', 'valid_id_type', 'valid_id_no', 'maiden_name']
            .forEach(function (n) { setVal(form, n, f[n]); });
          setVal(form, 'addr_zone_sitio', a.zone_sitio);
          setVal(form, 'addr_barangay', a.barangay);
          setVal(form, 'addr_municipality', a.municipality);
          setVal(form, 'addr_province', a.province);
          setVal(form, 'spouse_surname', s.surname);
          setVal(form, 'spouse_first_name', s.first_name);
          setVal(form, 'spouse_middle_name', s.middle_name);
          setVal(form, 'civil_status', f.civil_status);
          fld(form, 'is_married_woman').checked = !!f.is_married_woman;
          syncFamily();
          showPhoto(f.photo_url);
          existingFileNote('existing-photo', f.photo_name, f.photo_url);
          existingFileNote('existing-idfile', f.valid_id_file_name, f.valid_id_file_url);

          (f.children || []).forEach(addChild);
          parcelsList.innerHTML = '';
          if (f.parcels && f.parcels.length) f.parcels.forEach(addParcel); else addParcel();
          $('date-updated').value = Util.date(f.date_updated);
          updateTotals();
          dirty = false;
          hideAlert();
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showAlert(err.message);
          addParcel();
        });
    }

    /* ---------- start ---------- */
    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init(editId ? 'farmers' : 'register');
      $('date-updated').value = Util.date(new Date().toISOString());
      syncFamily();
      if (editId) { loadFarmer(); } else { addParcel(); }
      dirty = false;
    });
  };

  /* ---------- Farmers list (farmers.php) ----------
   * GET api/farmers.php?q=&commodity=&role=&barangay=&flag=open|resolved&sort=&dir=asc|desc&page=1&per_page=25
   *   sort is one of: rsbsa_no, name, total_area_ha, date_updated
   *   q should match name, RSBSA no., cadastral lot no. and barangay (farm or residential)
   *   -> { success, data: {
   *          rows: [ { id, rsbsa_no, name, barangay, commodities: ["Rice"], parcels: 2,
   *                    total_area_ha: 4.85, date_updated: "YYYY-MM-DD HH:MM:SS", flagged: false, flag_state: null | "open" | "resolved" } ],
   *          total: 120, page: 1, per_page: 25,
   *          barangays: ["Capirawan", "Libertad"]      // options for the barangay filter
   *        } }
   * The "View" window uses GET api/get_farmer.php?id= (see Pages.register).
   */
  Pages.farmers = function () {
    var state = { page: 1, per_page: 25, sort: 'date_updated', dir: 'desc' };
    var token = 0;
    var searchTimer = null;

    function $(id) { return document.getElementById(id); }
    var alertBox = $('list-alert');
    var body = $('farmers-body');

    function showError(msg) {
      alertBox.textContent = msg;
      alertBox.className = 'alert error';
      alertBox.hidden = false;
    }

    function fullName(surname, first, middle, suffix) {
      var rest = [first, middle, suffix].filter(Boolean).join(' ');
      return [surname, rest].filter(Boolean).join(', ');
    }

    function safeUrl(u) { return u && !/^\s*(javascript|data|vbscript):/i.test(u) ? u : ''; }

    /* ---------- list ---------- */
    function query() {
      var q = {
        q: $('f-q').value.trim(),
        commodity: $('f-commodity').value,
        role: $('f-role').value,
        barangay: $('f-barangay').value,
        flag: $('f-flag').value,
        sort: state.sort,
        dir: state.dir,
        page: state.page,
        per_page: state.per_page
      };
      return Object.keys(q).map(function (k) { return k + '=' + encodeURIComponent(q[k]); }).join('&');
    }

    function renderSortHeaders() {
      Array.prototype.forEach.call(document.querySelectorAll('.sort'), function (b) {
        var on = b.getAttribute('data-sort') === state.sort;
        b.classList.toggle('active', on);
        b.classList.toggle('asc', on && state.dir === 'asc');
        b.classList.toggle('desc', on && state.dir === 'desc');
      });
    }

    function fillBarangays(list) {
      var sel = $('f-barangay');
      var current = sel.value;
      if (!list || !list.length) return;
      sel.innerHTML = '<option value="">All</option>' + list.map(function (b) {
        return '<option' + (b === current ? ' selected' : '') + '>' + Util.esc(b) + '</option>';
      }).join('');
    }

    function renderRows(rows) {
      if (!rows || !rows.length) {
        body.innerHTML = '<tr><td colspan="8" class="empty">No farmers found.</td></tr>';
        return;
      }
      body.innerHTML = rows.map(function (r) {
        var tags = (r.commodities || []).map(function (c) { return '<span class="tag">' + Util.esc(c) + '</span>'; }).join('');
        var id = encodeURIComponent(r.id);
        return '<tr>' +
          '<td>' + Util.esc(r.rsbsa_no) + '</td>' +
          '<td><button type="button" class="link-btn name-link" data-view="' + Util.esc(r.id) + '">' + Util.esc(r.name) + '</button>' +
            (r.flag_state === 'open' ? '<span class="badge">Duplicate</span>' : (r.flag_state === 'resolved' ? '<span class="badge ok">Resolved</span>' : '')) + '</td>' +
          '<td>' + Util.esc(r.barangay) + '</td>' +
          '<td>' + tags + '</td>' +
          '<td class="num">' + Util.int(r.parcels) + '</td>' +
          '<td class="num">' + Util.ha(r.total_area_ha) + '</td>' +
          '<td>' + Util.esc(Util.date(r.date_updated)) + '</td>' +
          '<td class="actions">' +
            '<button type="button" class="link-btn" data-view="' + Util.esc(r.id) + '">View</button>' +
            '<a class="link-btn" href="register.php?id=' + id + '">Edit</a>' +
            '<button type="button" class="link-danger" data-delete="' + Util.esc(r.id) + '" data-name="' + Util.esc(r.name) +
              '" data-parcels="' + Util.esc(r.parcels) + '">Delete</button></td>' +
          '</tr>';
      }).join('');
    }

    function renderPagination(total) {
      var nav = $('pagination');
      var pages = Math.max(1, Math.ceil(total / state.per_page));
      if (pages <= 1) { nav.innerHTML = ''; return; }

      var start = Math.max(1, state.page - 2);
      var end = Math.min(pages, start + 4);
      start = Math.max(1, end - 4);

      var html = '<button type="button" data-page="' + (state.page - 1) + '"' + (state.page <= 1 ? ' disabled' : '') + '>Prev</button>';
      if (start > 1) html += '<button type="button" data-page="1">1</button>' + (start > 2 ? '<span class="muted">...</span>' : '');
      for (var p = start; p <= end; p++) {
        html += '<button type="button" data-page="' + p + '"' + (p === state.page ? ' class="current" aria-current="page"' : '') + '>' + p + '</button>';
      }
      if (end < pages) html += (end < pages - 1 ? '<span class="muted">...</span>' : '') + '<button type="button" data-page="' + pages + '">' + pages + '</button>';
      html += '<button type="button" data-page="' + (state.page + 1) + '"' + (state.page >= pages ? ' disabled' : '') + '>Next</button>';
      nav.innerHTML = html;
    }

    function load() {
      var mine = ++token;
      alertBox.hidden = true;
      renderSortHeaders();

      Api.get(CONFIG.ENDPOINTS.FARMERS + '?' + query())
        .then(function (res) {
          if (mine !== token) return;
          var d = res.data || {};
          var total = Number(d.total) || 0;

          // if the page is past the end (e.g. after filtering), go back to the last one
          var pages = Math.max(1, Math.ceil(total / state.per_page));
          if (state.page > pages) { state.page = pages; load(); return; }

          fillBarangays(d.barangays);
          renderRows(d.rows);
          renderPagination(total);

          if (!total) {
            $('result-info').textContent = '0 farmers';
          } else {
            var from = (state.page - 1) * state.per_page + 1;
            var to = Math.min(total, from + (d.rows ? d.rows.length : 0) - 1);
            $('result-info').textContent = 'Showing ' + Util.int(from) + '-' + Util.int(to) + ' of ' + Util.int(total) + ' farmers';
          }
        })
        .catch(function (err) {
          if (mine !== token) return;
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          body.innerHTML = '<tr><td colspan="8" class="empty">Could not load the list.</td></tr>';
          $('result-info').textContent = '';
          showError(err.message);
        });
    }

    function reload() { state.page = 1; load(); }

    /* ---------- details window ---------- */
    var dialog = $('view-dialog');
    var viewBody = $('view-body');
    viewBody.addEventListener('click', function (e) {
      var b = e.target.closest('[data-history]');
      if (b) ParcelHistory.open(b.getAttribute('data-history'));
    });

    function row(label, value) {
      return '<div><dt>' + Util.esc(label) + '</dt><dd>' + (value ? Util.esc(value) : '&ndash;') + '</dd></div>';
    }

    function placeText(a) {
      a = a || {};
      return [a.zone_sitio, a.barangay, a.municipality, a.province].filter(Boolean).join(', ');
    }

    function detailHtml(f) {
      var s = f.spouse || {};
      var photo = safeUrl(f.photo_url);
      var idUrl = safeUrl(f.valid_id_file_url);
      var html = '<div class="profile-head">' +
        (photo ? '<img class="profile-photo" src="' + Util.esc(photo) + '" alt="2x2 picture">' : '<div class="profile-photo none">No photo</div>') +
        '<div><h3 class="profile-name">' + Util.esc(fullName(f.surname, f.first_name, f.middle_name, f.suffix)) + '</h3>' +
        '<p class="muted">' + Util.esc(f.rsbsa_no || '') + (f.civil_status ? ' &middot; ' + Util.esc(f.civil_status) : '') + '</p>' +
        (idUrl ? '<p><a href="' + Util.esc(idUrl) + '" target="_blank" rel="noopener">Open valid ID (' + Util.esc(f.valid_id_file_name || 'file') + ')</a></p>' : '<p class="muted">No valid ID file attached.</p>') +
        '</div></div>';
      html += '<h3 class="detail-title">Basic Information</h3><dl class="detail-grid">' +
        row('Registration Number', f.rsbsa_no) +
        row('Civil Status', f.civil_status) +
        row('Name', fullName(f.surname, f.first_name, f.middle_name, f.suffix)) +
        row('Date of Birth', Util.date(f.birth_date)) +
        row('Valid ID', [f.valid_id_type, f.valid_id_no].filter(Boolean).join(' - ')) +
        row('Residential Address', placeText(f.address)) +
        '</dl>';

      html += '<h3 class="detail-title">Family Background</h3><dl class="detail-grid">' +
        row('Spouse', fullName(s.surname, s.first_name, s.middle_name)) +
        row('Mother Maiden Name', f.is_married_woman ? f.maiden_name : '') +
        row('Children', (f.children || []).join('; ')) +
        '</dl>';

      html += '<h3 class="detail-title">Record</h3><dl class="detail-grid">' +
        row('Date Updated', Util.date(f.date_updated)) +
        '</dl>';

      var total = 0;
      var parcels = f.parcels || [];
      var notCounted = 0;
      parcels.forEach(function (p) {
        if (p.parcel_status && p.parcel_status !== 'Active') notCounted++; else total += Number(p.area_ha) || 0;
      });
      html += '<h3 class="detail-title">Parcels (' + parcels.length + ' &middot; ' + Util.ha(total) + ' ha total' +
        (notCounted ? ', ' + notCounted + ' not counted' : '') + ')</h3>';

      parcels.forEach(function (p, i) {
        var crops = [];
        var labels = { rice: 'Rice', vegetable: 'Vegetable', root_crops: 'Root Crops', banana: 'Banana', coconut: 'Coconut', fruit_bearing: 'Fruit Bearing' };
        Object.keys(p.crop_areas || {}).forEach(function (k) {
          if (Number(p.crop_areas[k]) > 0) crops.push((labels[k] || k) + ': ' + Util.ha(p.crop_areas[k]) + ' ha');
        });
        var animals = (p.livestock || []).map(function (a) { return a.type + ': ' + Util.int(a.heads); });
        var url = safeUrl(p.tax_file_url);

        html += '<div class="detail-parcel"><h4>Parcel ' + (i + 1) +
          (p.id ? ' <button type="button" class="link-btn" data-history="' + Util.esc(p.id) + '">History</button>' : '') +
          (p.parcel_status && p.parcel_status !== 'Active' ? '<span class="badge off">' + (p.parcel_status === 'Inactive' ? 'In-active' : 'Deceased') + '</span>' : '') +
          (p.duplicate_flag ? (p.flag_status === 'resolved' ? '<span class="badge ok">Resolved</span>' : '<span class="badge">Duplicate</span>') : '') + '</h4><dl class="detail-grid">' +
          row('Parcel Remark', p.parcel_status === 'Inactive' ? 'In-active (area not counted)' : p.parcel_status === 'Deceased' ? 'Deceased (area not counted)' : 'Active') +
          row('Cadastral Lot No.', p.cadastral_lot_no) +
          row('Area (ha)', Util.ha(p.area_ha)) +
          row('Area per Tax Declaration (ha)', Util.ha(p.tax_declared_area_ha)) +
          row('Actual Cultivated Area (ha)', Util.ha(p.cultivated_area_ha)) +
          row('Farm Location', placeText(p.location)) +
          row('Sectoral Commodity', p.commodity) +
          (crops.length ? row('Area per Crop', crops.join('; ')) : '') +
          (animals.length ? row('No. of Heads', animals.join('; ')) : '') +
          (p.fish_size ? row('Fish Cage / Pond Size', p.fish_size) : '') +
          row('Farming Status', p.farming_role) +
          row('Tax Declarant / Owner', p.tax_declarant) +
          row('Name of Association', p.association) +
          '</dl>' +
          (url ? '<p><a href="' + Util.esc(url) + '" target="_blank" rel="noopener">Open tax declaration</a></p>' : '') +
          '</div>';
      });

      return html;
    }

    var viewing = null;   // the record shown in the details window

    function openView(id) {
      viewing = { id: id, name: '', parcels: 0 };
      $('view-delete').disabled = true;
      $('view-title').textContent = 'Farmer Record';
      $('view-edit').href = 'register.php?id=' + encodeURIComponent(id);
      viewBody.innerHTML = '<p class="muted">Loading...</p>';
      if (!dialog.open) dialog.showModal();

      Api.get(CONFIG.ENDPOINTS.GET_FARMER + '?id=' + encodeURIComponent(id))
        .then(function (res) {
          var f = (res.data && res.data.farmer) || {};
          $('view-title').textContent = fullName(f.surname, f.first_name, f.middle_name, f.suffix) || 'Farmer Record';
          viewBody.innerHTML = detailHtml(f);
          viewing = { id: id, name: fullName(f.surname, f.first_name, f.middle_name, f.suffix), parcels: (f.parcels || []).length };
          $('view-delete').disabled = false;
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          viewBody.innerHTML = '<div class="alert error">' + Util.esc(err.message) + '</div>';
        });
    }

    /* ---------- events ---------- */
    $('f-q').addEventListener('input', function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(reload, 350);
    });
    ['f-commodity', 'f-role', 'f-barangay', 'f-flag'].forEach(function (id) {
      $(id).addEventListener('change', reload);
    });
    $('filter-form').addEventListener('submit', function (e) { e.preventDefault(); reload(); });

    $('f-reset').addEventListener('click', function () {
      $('filter-form').reset();
      state.sort = 'date_updated';
      state.dir = 'desc';
      reload();
    });

    $('per-page').addEventListener('change', function () {
      state.per_page = Number(this.value) || 25;
      reload();
    });

    Array.prototype.forEach.call(document.querySelectorAll('.sort'), function (b) {
      b.addEventListener('click', function () {
        var key = b.getAttribute('data-sort');
        if (state.sort === key) {
          state.dir = state.dir === 'asc' ? 'desc' : 'asc';
        } else {
          state.sort = key;
          state.dir = key === 'total_area_ha' || key === 'date_updated' ? 'desc' : 'asc';
        }
        reload();
      });
    });

    $('pagination').addEventListener('click', function (e) {
      var b = e.target.closest('button[data-page]');
      if (!b || b.disabled) return;
      state.page = Number(b.getAttribute('data-page')) || 1;
      load();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function showOk(msg) {
      alertBox.textContent = msg;
      alertBox.className = 'alert success';
      alertBox.hidden = false;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /** Ask first, then delete the farmer, the parcels and every attached file. */
    function deleteFarmer(id, name, parcels) {
      Ui.confirm({
        title: 'Delete this farmer?',
        danger: true,
        confirmText: 'Delete permanently',
        lines: [
          name || 'This farmer',
          'The farmer record' + (parcels ? ', ' + parcels + ' parcel(s)' : '') +
            ' and all attached files (tax declarations and supporting documents) will be permanently deleted.',
          'This cannot be undone.'
        ]
      }).then(function (yes) {
        if (!yes) return;
        alertBox.hidden = true;
        Api.post(CONFIG.ENDPOINTS.DELETE_FARMER, { id: id })
          .then(function (res) {
            var d = res.data || {};
            var msg = res.message || 'Farmer deleted.';
            if (d.orphaned_flags > 0) {
              msg += ' ' + d.orphaned_flags + ' duplicate flag(s) of other farmers named this farmer as the registered owner; review them on the Duplicate Flags page.';
            }
            if (dialog.open) dialog.close();
            showOk(msg);
            load();
          })
          .catch(function (err) {
            if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
            if (dialog.open) dialog.close();
            showError(err.message);
            load();
          });
      });
    }

    body.addEventListener('click', function (e) {
      var d = e.target.closest('[data-delete]');
      if (d) {
        deleteFarmer(d.getAttribute('data-delete'), d.getAttribute('data-name'), Number(d.getAttribute('data-parcels')) || 0);
        return;
      }
      var b = e.target.closest('[data-view]');
      if (b) openView(b.getAttribute('data-view'));
    });

    $('view-delete').addEventListener('click', function () {
      if (viewing) deleteFarmer(viewing.id, viewing.name, viewing.parcels);
    });

    $('view-close').addEventListener('click', function () { dialog.close(); });
    $('view-close-2').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); }); // click on backdrop

    /* ---------- start ---------- */
    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('farmers');
      var flash = Flash.take();
      if (flash) showOk(flash);
      load();
    });
  };

  /* ---------- Duplicate flags (duplicates.php) ----------
   * A flag is created by save_farmer.php when a parcel is saved with duplicate_flag = true.
   *
   * GET api/duplicates.php?status=open|resolved|all&q=&page=1&per_page=25
   *   -> { success, data: {
   *          rows: [ { id, lot_no, barangay,
   *                    farmer_id, farmer_name, farmer_rsbsa_no,                 // the farmer who was flagged
   *                    registered_to: "Dela Cruz, Pedro", registered_farmer_id, // the farmer already holding the lot
   *                    area_ha, tax_declared_area_ha,
   *                    status: "open" | "resolved", remarks, flagged_at, resolved_at,
   *                    documents: [ { name, url } ] } ],
   *          total, page, per_page,
   *          counts: { open: 0, resolved: 0 } } }
   *
   * POST api/update_duplicate.php  (JSON) { id, status: "open" | "resolved", remarks }
   *   -> { success, message }       Validation errors: HTTP 422 { errors: { remarks: "..." } }
   */
  Pages.duplicates = function () {
    var state = { status: 'open', page: 1, per_page: 25 };
    var token = 0;
    var searchTimer = null;
    var rowsById = {};
    var current = null; // the flag open in the review window

    function $(id) { return document.getElementById(id); }
    var alertBox = $('dup-alert');
    var body = $('dup-body');
    var dialog = $('review-dialog');

    function showAlert(msg, type) {
      alertBox.textContent = msg;
      alertBox.className = 'alert ' + (type || 'error');
      alertBox.hidden = false;
    }

    function safeUrl(u) { return u && !/^\s*(javascript|data|vbscript):/i.test(u) ? u : ''; }
    function short(text, n) { text = String(text || ''); return text.length > n ? text.slice(0, n - 1) + '...' : text; }

    function statusBadge(s) {
      return s === 'resolved' ? '<span class="badge ok">Resolved</span>' : '<span class="badge open">Open</span>';
    }

    /* ---------- list ---------- */
    function query() {
      var q = { status: state.status, q: $('d-q').value.trim(), page: state.page, per_page: state.per_page };
      return Object.keys(q).map(function (k) { return k + '=' + encodeURIComponent(q[k]); }).join('&');
    }

    function renderRows(rows) {
      rowsById = {};
      if (!rows || !rows.length) {
        body.innerHTML = '<tr><td colspan="10" class="empty">No duplicate flags found.</td></tr>';
        return;
      }
      body.innerHTML = rows.map(function (r) {
        rowsById[r.id] = r;
        var docs = (r.documents || []).length;
        return '<tr>' +
          '<td>' + Util.esc(r.lot_no) + '</td>' +
          '<td>' + Util.esc(r.barangay) + '</td>' +
          '<td>' + Util.esc(r.farmer_name) +
            (docs ? '<span class="doc-count">' + docs + ' document' + (docs > 1 ? 's' : '') + '</span>' : '<span class="doc-count">No documents</span>') + '</td>' +
          '<td>' + Util.esc(r.registered_to) + '</td>' +
          '<td class="num">' + Util.ha(r.area_ha) + '</td>' +
          '<td class="num">' + Util.ha(r.tax_declared_area_ha) + '</td>' +
          '<td>' + Util.esc(Util.date(r.flagged_at)) + '</td>' +
          '<td>' + statusBadge(r.status) + '</td>' +
          '<td class="remarks-cell">' + Util.esc(short(r.remarks, 80)) + '</td>' +
          '<td class="actions no-print"><button type="button" class="link-btn" data-review="' + Util.esc(r.id) + '">Review</button></td>' +
          '</tr>';
      }).join('');
    }

    function renderPagination(total) {
      var nav = $('pagination');
      var pages = Math.max(1, Math.ceil(total / state.per_page));
      if (pages <= 1) { nav.innerHTML = ''; return; }
      var start = Math.max(1, state.page - 2);
      var end = Math.min(pages, start + 4);
      start = Math.max(1, end - 4);

      var html = '<button type="button" data-page="' + (state.page - 1) + '"' + (state.page <= 1 ? ' disabled' : '') + '>Prev</button>';
      if (start > 1) html += '<button type="button" data-page="1">1</button>' + (start > 2 ? '<span class="muted">...</span>' : '');
      for (var p = start; p <= end; p++) {
        html += '<button type="button" data-page="' + p + '"' + (p === state.page ? ' class="current" aria-current="page"' : '') + '>' + p + '</button>';
      }
      if (end < pages) html += (end < pages - 1 ? '<span class="muted">...</span>' : '') + '<button type="button" data-page="' + pages + '">' + pages + '</button>';
      html += '<button type="button" data-page="' + (state.page + 1) + '"' + (state.page >= pages ? ' disabled' : '') + '>Next</button>';
      nav.innerHTML = html;
    }

    function load() {
      var mine = ++token;
      Api.get(CONFIG.ENDPOINTS.DUPLICATES + '?' + query())
        .then(function (res) {
          if (mine !== token) return;
          var d = res.data || {};
          var total = Number(d.total) || 0;
          var pages = Math.max(1, Math.ceil(total / state.per_page));
          if (state.page > pages) { state.page = pages; load(); return; }

          var c = d.counts || {};
          $('count-open').textContent = Util.int(c.open);
          $('count-resolved').textContent = Util.int(c.resolved);

          renderRows(d.rows);
          renderPagination(total);

          var label = { open: 'Open', resolved: 'Resolved', all: 'All' }[state.status];
          $('result-info').textContent = total ? Util.int(total) + ' flag' + (total > 1 ? 's' : '') : 'No flags';
          $('print-meta').textContent = label + ' flags · ' + Util.int(total) + ' record(s) · Printed ' +
            new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
        })
        .catch(function (err) {
          if (mine !== token) return;
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          body.innerHTML = '<tr><td colspan="10" class="empty">Could not load the flags.</td></tr>';
          $('result-info').textContent = '';
          showAlert(err.message);
        });
    }

    function reload() { state.page = 1; load(); }

    /* ---------- review window ---------- */
    function infoRow(label, value) {
      return '<div><dt>' + Util.esc(label) + '</dt><dd>' + (value ? value : '&ndash;') + '</dd></div>';
    }

    function openReview(id) {
      var r = rowsById[id];
      if (!r) return;
      current = r;

      $('review-alert').hidden = true;
      $('review-remarks-error').textContent = '';
      $('review-remarks').closest('.field').classList.remove('invalid');
      $('review-title').textContent = 'Duplicate Flag: Lot ' + r.lot_no;

      var farmerLink = r.farmer_id
        ? '<a href="register.php?id=' + encodeURIComponent(r.farmer_id) + '">' + Util.esc(r.farmer_name) + '</a>'
        : Util.esc(r.farmer_name);
      var ownerLink = r.registered_farmer_id
        ? '<a href="register.php?id=' + encodeURIComponent(r.registered_farmer_id) + '">' + Util.esc(r.registered_to) + '</a>'
        : Util.esc(r.registered_to);

      $('review-info').innerHTML = '<dl class="detail-grid">' +
        infoRow('Cadastral Lot No.', Util.esc(r.lot_no)) +
        infoRow('Barangay', Util.esc(r.barangay)) +
        infoRow('Flagged Farmer', farmerLink) +
        infoRow('RSBSA No. of Flagged Farmer', Util.esc(r.farmer_rsbsa_no)) +
        infoRow('Already Registered To', ownerLink) +
        infoRow('Area Applied (ha)', Util.ha(r.area_ha)) +
        infoRow('Area per Tax Declaration (ha)', Util.ha(r.tax_declared_area_ha)) +
        infoRow('Flagged On', Util.esc(Util.date(r.flagged_at))) +
        infoRow('Status', statusBadge(r.status)) +
        (r.status === 'resolved' ? infoRow('Resolved On', Util.esc(Util.date(r.resolved_at))) : '') +
        '</dl>';

      var docs = (r.documents || []).filter(function (d) { return safeUrl(d.url); });
      $('review-docs').innerHTML = docs.length
        ? '<ul class="doc-list">' + docs.map(function (d) {
            return '<li><a href="' + Util.esc(safeUrl(d.url)) + '" target="_blank" rel="noopener">' + Util.esc(d.name || 'Document') + '</a></li>';
          }).join('') + '</ul>'
        : '<p class="muted">No supporting documents were attached.</p>';

      $('review-remarks').value = r.remarks || '';
      $('review-toggle').textContent = r.status === 'resolved' ? 'Reopen Flag' : 'Mark as Resolved';
      $('review-toggle').className = r.status === 'resolved' ? 'btn-outline' : 'btn-solid';

      if (!dialog.open) dialog.showModal();
    }

    function submitReview(newStatus) {
      var remarks = $('review-remarks').value.trim();
      var field = $('review-remarks').closest('.field');
      field.classList.remove('invalid');
      $('review-remarks-error').textContent = '';
      $('review-alert').hidden = true;

      if (newStatus === 'resolved' && !remarks) {
        field.classList.add('invalid');
        $('review-remarks-error').textContent = 'Enter your remarks before resolving this flag.';
        $('review-remarks').focus();
        return;
      }

      var buttons = [$('review-save'), $('review-toggle')];
      buttons.forEach(function (b) { b.disabled = true; });

      Api.post(CONFIG.ENDPOINTS.UPDATE_DUPLICATE, { id: current.id, status: newStatus, remarks: remarks })
        .then(function (res) {
          dialog.close();
          showAlert(res.message || 'Flag updated.', 'success');
          load();
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          if (err.errors && err.errors.remarks) {
            field.classList.add('invalid');
            $('review-remarks-error').textContent = err.errors.remarks;
          }
          var box = $('review-alert');
          box.textContent = err.message;
          box.className = 'alert error';
          box.hidden = false;
        })
        .then(function () { buttons.forEach(function (b) { b.disabled = false; }); });
    }

    /* ---------- events ---------- */
    Array.prototype.forEach.call(document.querySelectorAll('.tab'), function (tab) {
      tab.addEventListener('click', function () {
        Array.prototype.forEach.call(document.querySelectorAll('.tab'), function (t) {
          var on = t === tab;
          t.classList.toggle('active', on);
          t.setAttribute('aria-selected', String(on));
        });
        state.status = tab.getAttribute('data-status');
        reload();
      });
    });

    $('d-q').addEventListener('input', function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(reload, 350);
    });

    $('per-page').addEventListener('change', function () {
      state.per_page = Number(this.value) || 25;
      reload();
    });

    $('pagination').addEventListener('click', function (e) {
      var b = e.target.closest('button[data-page]');
      if (!b || b.disabled) return;
      state.page = Number(b.getAttribute('data-page')) || 1;
      load();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    body.addEventListener('click', function (e) {
      var b = e.target.closest('[data-review]');
      if (b) openReview(b.getAttribute('data-review'));
    });

    $('review-close').addEventListener('click', function () { dialog.close(); });
    $('review-cancel').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); });

    $('review-save').addEventListener('click', function () { submitReview(current.status); });
    $('review-toggle').addEventListener('click', function () {
      submitReview(current.status === 'resolved' ? 'open' : 'resolved');
    });

    $('print-btn').addEventListener('click', function () {
      Util.logEvent({ event: 'print_duplicates', detail: $('print-meta').textContent });
      window.print();
    });

    /* ---------- start ---------- */
    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('duplicates');
      load();
    });
  };

  /* ---------- Reports & export (reports.php) ----------
   * GET api/export.php?report=farmers|duplicates&format=xlsx|csv&...filters
   *   farmers    filters: commodity, role, barangay, updated_from (YYYY-MM-DD), updated_to
   *   duplicates filters: status = all | open | resolved
   *   Success: send the file with  Content-Type  and  Content-Disposition: attachment; filename="..."
   *   Failure: send JSON { success:false, message } with an error status code (the page shows the message).
   *
   * The printable registration form is built in the browser from api/get_farmer.php
   * and api/farmers.php?q= (the farmer search), so it needs no extra endpoint.
   */
  Pages.reports = function () {
    var selected = null;
    var searchTimer = null;
    var searchToken = 0;

    function $(id) { return document.getElementById(id); }
    var alertBox = $('rep-alert');

    function showAlert(msg, type) {
      alertBox.textContent = msg;
      alertBox.className = 'alert ' + (type || 'error');
      alertBox.hidden = false;
      alertBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function qs(obj) {
      return Object.keys(obj).map(function (k) { return k + '=' + encodeURIComponent(obj[k]); }).join('&');
    }

    function fullName(surname, first, middle, suffix) {
      var rest = [first, middle, suffix].filter(Boolean).join(' ');
      return [surname, rest].filter(Boolean).join(', ');
    }

    function placeText(a) {
      a = a || {};
      return [a.zone_sitio, a.barangay, a.municipality, a.province].filter(Boolean).join(', ');
    }

    /* ---------- downloads ---------- */
    /* the filters of a report as query parameters, or null (with a message shown) when they are not valid */
    function reportParams(report, format) {
      var params = { report: report, format: format };

      alertBox.hidden = true;
      $('r-range-error').textContent = '';
      $('r-to').closest('.field').classList.remove('invalid');

      if (report === 'farmers') {
        params.commodity = $('r-commodity').value;
        params.role = $('r-role').value;
        params.barangay = $('r-barangay').value;
        params.updated_from = $('r-from').value;
        params.updated_to = $('r-to').value;

        if (params.updated_from && params.updated_to && params.updated_from > params.updated_to) {
          $('r-to').closest('.field').classList.add('invalid');
          $('r-range-error').textContent = 'The end date must be on or after the start date.';
          return null;
        }
      } else {
        params.status = $('r-dup-status').value;
      }
      return params;
    }

    function runDownload(btn) {
      var report = btn.getAttribute('data-report');
      var format = btn.getAttribute('data-format');
      var params = reportParams(report, format);
      if (!params) return;

      var label = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Preparing...';

      var stamp = new Date().toISOString().slice(0, 10);
      Api.download(CONFIG.ENDPOINTS.EXPORT + '?' + qs(params), 'rsbsa-' + report + '-' + stamp + '.' + format)
        .then(function (file) {
          Api.saveFile(file.blob, file.name);
          showAlert('Download started: ' + file.name, 'success');
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showAlert(err.message);
        })
        .then(function () { btn.disabled = false; btn.textContent = label; });
    }

    /* ---------- preview ----------
     * GET api/export.php?report=&format=json&...filters
     *   -> { data: { title, subtitle, headers: [], rows: [[...]], total, truncated } }   (first 100 rows) */
    var previewDlg = null;
    function ensurePreview() {
      if (previewDlg) return;
      previewDlg = document.createElement('dialog');
      previewDlg.className = 'modal preview-modal';
      previewDlg.setAttribute('aria-label', 'Report preview');
      previewDlg.innerHTML =
        '<header class="modal-head"><h2 data-p="title">Preview</h2>' +
        '<button type="button" class="modal-close" data-p="close" aria-label="Close">&times;</button></header>' +
        '<div class="modal-body"><p class="muted" data-p="info"></p><div class="table-wrap preview-wrap" data-p="table"></div></div>' +
        '<footer class="modal-foot">' +
        '<button type="button" class="btn-solid" data-p="xlsx">Download Excel (.xlsx)</button>' +
        '<button type="button" class="btn-outline" data-p="csv">Download CSV</button>' +
        '<span class="spacer"></span>' +
        '<button type="button" class="btn-outline" data-p="close">Close</button></footer>';
      document.body.appendChild(previewDlg);
      previewDlg.addEventListener('click', function (e) {
        var t = e.target.closest ? e.target.closest('[data-p]') : null;
        if (e.target === previewDlg || (t && t.getAttribute('data-p') === 'close')) { previewDlg.close(); return; }
        if (t && (t.getAttribute('data-p') === 'xlsx' || t.getAttribute('data-p') === 'csv')) {
          var b = document.querySelector('[data-export][data-report="' + previewDlg._report + '"][data-format="' + t.getAttribute('data-p') + '"]');
          if (b) { previewDlg.close(); runDownload(b); }
        }
      });
    }

    function runPreview(btn) {
      var report = btn.getAttribute('data-report');
      var params = reportParams(report, 'json');
      if (!params) return;
      ensurePreview();
      var label = btn.textContent;
      btn.disabled = true; btn.textContent = 'Loading...';
      Api.get(CONFIG.ENDPOINTS.EXPORT + '?' + qs(params))
        .then(function (res) {
          var d = res.data || {};
          previewDlg._report = report;
          previewDlg.querySelector('[data-p="title"]').textContent = 'Preview: ' + (d.title || 'Report');
          previewDlg.querySelector('[data-p="info"]').textContent =
            (d.subtitle || '') + (d.truncated ? ' | showing the first ' + (d.rows || []).length + ' of ' + Util.int(d.total) + ' rows. The download has all of them.' : '');
          var rows = d.rows || [];
          previewDlg.querySelector('[data-p="table"]').innerHTML = rows.length
            ? '<table class="preview-table"><thead><tr><th class="num">#</th>' + (d.headers || []).map(function (h) { return '<th>' + Util.esc(h) + '</th>'; }).join('') + '</tr></thead><tbody>' +
              rows.map(function (r, i) {
                return '<tr><td class="num">' + (i + 1) + '</td>' + r.map(function (c) { return '<td' + (typeof c === 'number' ? ' class="num"' : '') + '>' + Util.esc(c === null || c === undefined ? '' : String(c)) + '</td>'; }).join('') + '</tr>';
              }).join('') + '</tbody></table>'
            : '<p class="empty">No records match these filters.</p>';
          previewDlg.querySelector('[data-p="xlsx"]').disabled = !rows.length;
          previewDlg.querySelector('[data-p="csv"]').disabled = !rows.length;
          if (!previewDlg.open) previewDlg.showModal();
          previewDlg.querySelector('.modal-body').scrollTop = 0;
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showAlert(err.message);
        })
        .then(function () { btn.disabled = false; btn.textContent = label; });
    }

    /* ---------- printable registration form ----------
     * Same layout as the farmer profile (View window), without buttons or links.
     * A blank form (no farmer) prints empty writing lines instead of values. */
    function safeUrl(u) { return u && !/^\s*(javascript|data|vbscript):/i.test(u) ? u : ''; }
    var CROP_LABELS = { rice: 'Rice', vegetable: 'Vegetable', root_crops: 'Root Crops', banana: 'Banana', coconut: 'Coconut', fruit_bearing: 'Fruit Bearing' };

    function formHtml(f) {
      var blank = !f;
      f = f || {};
      var a = f.address || {};
      var s = f.spouse || {};
      var parcels = f.parcels && f.parcels.length ? f.parcels : (blank ? [{}, {}] : [{}]);   // a blank form has 2 parcels to fill in

      // one labelled value; an empty value is a dash on a filled form and a writing line on a blank one
      function item(label, value) {
        var has = value !== undefined && value !== null && value !== '';
        return '<div><dt>' + Util.esc(label) + '</dt><dd' + (has ? '' : (blank ? ' class="pf-line"' : '')) + '>' +
          (has ? Util.esc(value) : (blank ? '&nbsp;' : '&ndash;')) + '</dd></div>';
      }
      var statusText = function (st) { return st === 'Inactive' ? 'In-active' : (st || (blank ? '' : 'Active')); };
      var pic = safeUrl(f.photo_url);

      var html = '<div class="pf-header"><img class="pf-seal" src="assets/img/palo-seal.png" alt="">' +
        '<h1 class="pf-title">Palo Registry System for Basic Sectors in Agriculture (RSBSA)</h1></div>' +
        '<div class="profile-head">' +
        (pic ? '<img class="profile-photo" src="' + Util.esc(pic) + '" alt="">' : '<div class="profile-photo none">2 x 2<br>photo</div>') +
        '<div><h3 class="profile-name">' + (blank ? '&nbsp;' : Util.esc(fullName(f.surname, f.first_name, f.middle_name, f.suffix))) + '</h3>' +
        '<p class="muted">' + Util.esc(f.rsbsa_no || '') + (f.civil_status ? ' &middot; ' + Util.esc(f.civil_status) : '') + '</p></div></div>';

      html += '<h3 class="detail-title">Basic Information</h3><dl class="detail-grid">' +
        item('Registration Number', f.rsbsa_no) +
        item('Civil Status', f.civil_status) +
        item('Name (Surname, First Name, Middle Name, Etc.)', blank ? '' : fullName(f.surname, f.first_name, f.middle_name, f.suffix)) +
        item('Date of Birth', f.birth_date ? Util.date(f.birth_date) : '') +
        item('Valid ID', [f.valid_id_type, f.valid_id_no].filter(Boolean).join(' - ')) +
        item('Residential Address (Zone/Sitio, Brgy., Municipality, Province)', placeText(a)) +
        '</dl>';

      html += '<h3 class="detail-title">Family Background</h3><dl class="detail-grid">' +
        item('Spouse', blank ? '' : fullName(s.surname, s.first_name, s.middle_name)) +
        item('Mother Maiden Name (for married woman)', f.is_married_woman ? f.maiden_name : '') +
        (blank
          ? item('Name of Child 1', '') + item('Name of Child 2', '') + item('Name of Child 3', '')   // a blank form has 3 rows for children
          : item('Children', (f.children || []).join('; '))) +
        '</dl>';

      html += '<h3 class="detail-title">Record</h3><dl class="detail-grid">' +
        item('Date Updated', f.date_updated ? Util.date(f.date_updated) : '') +
        '</dl>';

      var total = 0;
      parcels.forEach(function (p) { if (!p.parcel_status || p.parcel_status === 'Active') total += Number(p.area_ha) || 0; });
      html += '<h3 class="detail-title">Parcels' + (blank ? '' : ' (' + parcels.length + ' &middot; ' + Util.ha(total) + ' ha total)') + '</h3>';

      parcels.forEach(function (p, i) {
        var crops = [];
        Object.keys(p.crop_areas || {}).forEach(function (k) {
          if (Number(p.crop_areas[k]) > 0) crops.push((CROP_LABELS[k] || k) + ': ' + Util.ha(p.crop_areas[k]) + ' ha');
        });
        var animals = (p.livestock || []).map(function (x) { return x.type + ': ' + Util.int(x.heads); });
        var has = function (v) { return v !== undefined && v !== null && v !== ''; };

        html += '<div class="detail-parcel"><h4>Parcel ' + (i + 1) + '</h4><dl class="detail-grid">' +
          item('Parcel Remark', blank ? '' : (p.parcel_status === 'Inactive' ? 'In-active (area not counted)' : p.parcel_status === 'Deceased' ? 'Deceased (area not counted)' : 'Active')) +
          item('Cadastral Lot No.', p.cadastral_lot_no) +
          item('Area (ha)', has(p.area_ha) ? Util.ha(p.area_ha) : '') +
          item('Area per Tax Declaration (ha)', has(p.tax_declared_area_ha) ? Util.ha(p.tax_declared_area_ha) : '') +
          item('Actual Cultivated Area (ha)', has(p.cultivated_area_ha) ? Util.ha(p.cultivated_area_ha) : '') +
          item('Farm Location (Zone/Sitio, Brgy., Municipality, Province)', placeText(p.location)) +
          item('Sectoral Commodity (Rice, Vegetable, Root Crops, Banana, Coconut, Livestock, Fishery)', p.commodity) +
          (blank || crops.length ? item('Area per Crop (Rice, Vegetable, Root Crops, Banana, Coconut, Fruit Bearing)', crops.join('; ')) : '') +
          (blank || animals.length ? item('No. of Heads (Cow, Carabao, Sheep, Goat, Swine, Poultry, Etc.)', animals.join('; ')) : '') +
          (blank || p.fish_size ? item('Size of Fish Cage / Fish Pond', p.fish_size) : '') +
          item('Farming Status (Owner, Tenant, Leasee, Actual Tiller, Farm Worker)', p.farming_role) +
          item('Name of Tax Declarant / Owner', p.tax_declarant) +
          item('Name of Association (if applicable)', p.association) +
          '</dl></div>';
      });

      return html;
    }

    function printForm(farmer) {
      Util.logEvent(farmer && farmer.id ? { event: 'print_form', farmer_id: farmer.id } : { event: 'print_form' });
      $('print-form').innerHTML = formHtml(farmer);
      document.body.classList.add('print-form-mode');
      window.print();
    }
    window.addEventListener('afterprint', function () { document.body.classList.remove('print-form-mode'); });

    /* ---------- farmer search ---------- */
    function renderPicks(rows) {
      var list = $('pick-list');
      list.hidden = false;
      if (!rows || !rows.length) { list.innerHTML = '<li class="pick-empty">No farmers found.</li>'; return; }
      list.innerHTML = rows.map(function (r) {
        return '<li><button type="button" data-id="' + Util.esc(r.id) + '" data-name="' + Util.esc(r.name) + '">' +
          Util.esc(r.name) + '<small>' + Util.esc(r.rsbsa_no) + (r.barangay ? ' · ' + Util.esc(r.barangay) : '') + '</small></button></li>';
      }).join('');
    }

    function search() {
      var term = $('r-search').value.trim();
      if (term.length < 2) { $('pick-list').hidden = true; return; }

      var mine = ++searchToken;
      Api.get(CONFIG.ENDPOINTS.FARMERS + '?' + qs({ q: term, sort: 'name', dir: 'asc', page: 1, per_page: 8 }))
        .then(function (res) { if (mine === searchToken) renderPicks((res.data || {}).rows); })
        .catch(function (err) {
          if (mine !== searchToken) return;
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showAlert(err.message);
        });
    }

    $('r-search').addEventListener('input', function () {
      selected = null;
      $('picked').hidden = true;
      $('print-farmer').disabled = true;
      clearTimeout(searchTimer);
      searchTimer = setTimeout(search, 350);
    });

    $('pick-list').addEventListener('click', function (e) {
      var b = e.target.closest('button[data-id]');
      if (!b) return;
      selected = { id: b.getAttribute('data-id'), name: b.getAttribute('data-name') };
      $('pick-list').hidden = true;
      $('r-search').value = selected.name;
      $('picked').textContent = 'Selected: ' + selected.name;
      $('picked').hidden = false;
      $('print-farmer').disabled = false;
    });

    $('print-farmer').addEventListener('click', function () {
      if (!selected) return;
      var btn = this;
      btn.disabled = true;
      alertBox.hidden = true;

      Api.get(CONFIG.ENDPOINTS.GET_FARMER + '?id=' + encodeURIComponent(selected.id))
        .then(function (res) { printForm((res.data || {}).farmer); })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showAlert(err.message);
        })
        .then(function () { btn.disabled = !selected; });
    });

    $('print-blank').addEventListener('click', function () { printForm(null); });

    /* ---------- downloads ---------- */
    Array.prototype.forEach.call(document.querySelectorAll('[data-export]'), function (btn) {
      btn.addEventListener('click', function () { runDownload(btn); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-preview]'), function (btn) {
      btn.addEventListener('click', function () { runPreview(btn); });
    });

    /* ---------- start ---------- */
    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('reports');

      // fill the barangay filter from the farmers list endpoint
      Api.get(CONFIG.ENDPOINTS.FARMERS + '?page=1&per_page=1')
        .then(function (res) {
          var list = (res.data || {}).barangays || [];
          $('r-barangay').innerHTML = '<option value="">All</option>' + list.map(function (b) {
            return '<option>' + Util.esc(b) + '</option>';
          }).join('');
        })
        .catch(function () { /* the filter stays on "All" */ });
    });
  };

  /* ---------- Settings (settings.php): change password, auto-logout, theme ----------
   * POST api/change_password.php (JSON) { current_password, new_password }
   *   -> { success, message }     422 { errors: { current_password | new_password } }, 429 after too many wrong attempts
   */
  Pages.settings = function () {
    function $(id) { return document.getElementById(id); }
    var form = $('pw-form');
    var alertBox = $('pw-alert');
    var submitBtn = $('pw-submit');
    var inputs = { current_password: $('pw-current'), new_password: $('pw-new'), confirm: $('pw-confirm') };

    function showAlert(msg, type) {
      alertBox.textContent = msg;
      alertBox.className = 'alert ' + (type || 'error');
      alertBox.hidden = false;
    }
    function setError(name, msg) {
      var field = inputs[name].closest('.field');
      field.classList.toggle('invalid', !!msg);
      field.querySelector('.field-error').textContent = msg || '';
    }
    function clearErrors() {
      Object.keys(inputs).forEach(function (n) { setError(n, ''); });
      alertBox.hidden = true;
    }
    function setLoading(on) {
      submitBtn.disabled = on;
      submitBtn.classList.toggle('loading', on);
      submitBtn.querySelector('.btn-text').textContent = on ? 'Saving...' : 'Change Password';
    }

    $('pw-show').addEventListener('change', function () {
      var type = this.checked ? 'text' : 'password';
      Object.keys(inputs).forEach(function (n) { inputs[n].type = type; });
    });
    Object.keys(inputs).forEach(function (n) {
      inputs[n].addEventListener('input', function () { setError(n, ''); });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearErrors();

      var current = inputs.current_password.value;
      var next = inputs.new_password.value;
      var confirm = inputs.confirm.value;
      var bad = null;
      function fail(name, msg) { setError(name, msg); if (!bad) bad = inputs[name]; }

      if (!current) fail('current_password', 'Enter your current password.');
      if (next.length < 8) fail('new_password', 'The new password must be at least 8 characters.');
      else if (next.length > 72) fail('new_password', 'The new password must be 72 characters or fewer.');
      else if (next === current) fail('new_password', 'The new password must be different from the current one.');
      if (next !== confirm) fail('confirm', 'The two new passwords do not match.');
      if (bad) { bad.focus(); return; }

      setLoading(true);
      Api.post(CONFIG.ENDPOINTS.CHANGE_PASSWORD, { current_password: current, new_password: next })
        .then(function (res) {
          form.reset();
          Object.keys(inputs).forEach(function (n) { inputs[n].type = 'password'; });
          showAlert((res.message || 'Password changed.') + ' Use the new password the next time you log in.', 'success');
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          var e = err.errors || {};
          if (e.current_password) setError('current_password', e.current_password);
          if (e.new_password) setError('new_password', e.new_password);
          showAlert(err.message);
          if (e.current_password) { inputs.current_password.value = ''; inputs.current_password.focus(); }
        })
        .then(function () { setLoading(false); });
    });

    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('settings');
      showUser();
    });

    /* ----- your name ----- */
    var nmForm = $('nm-form'), nmInput = $('nm-name'), nmAlert = $('nm-alert'), nmBtn = $('nm-submit');
    function nmError(msg) {
      var field = nmInput.closest('.field');
      field.classList.toggle('invalid', !!msg);
      field.querySelector('.field-error').textContent = msg || '';
    }
    function nmShow(msg, type) { nmAlert.textContent = msg; nmAlert.className = 'alert ' + (type || 'error'); nmAlert.hidden = false; }
    function showUser() {
      var u = Auth.getUser() || {};
      nmInput.value = u.name || '';
      $('nm-username').textContent = u.username || '-';
    }
    nmInput.addEventListener('input', function () { nmError(''); nmAlert.hidden = true; });
    nmForm.addEventListener('submit', function (e) {
      e.preventDefault();
      nmError(''); nmAlert.hidden = true;
      var name = nmInput.value.trim().replace(/\s+/g, ' ');
      if (!name) { nmError('Enter your name.'); nmInput.focus(); return; }
      if (name.length > 60) { nmError('The name must be 60 characters or fewer.'); nmInput.focus(); return; }
      nmBtn.disabled = true; nmBtn.classList.add('loading');
      Api.post(CONFIG.ENDPOINTS.UPDATE_PROFILE, { name: name })
        .then(function (res) {
          var user = (res.data && res.data.user) || {};
          cacheUser(user);
          nmInput.value = user.name || name;
          var side = $('user-name');
          if (side) side.textContent = user.name || name;
          nmShow(res.message || 'Your name was changed.', 'success');
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          if (err.errors && err.errors.name) nmError(err.errors.name);
          nmShow(err.message);
        })
        .then(function () { nmBtn.disabled = false; nmBtn.classList.remove('loading'); });
    });

    /* ----- auto-logout ----- */
    var alOn = $('al-enabled'), alMin = $('al-minutes'), alStatus = $('al-status');
    function showAutoLogout() {
      var s = Settings.get();
      alOn.checked = s.autoLogout;
      alMin.value = String(s.autoLogoutMinutes);
      alMin.disabled = !s.autoLogout;
      alStatus.textContent = s.autoLogout
        ? 'On: you will be logged out after ' + s.autoLogoutMinutes + ' minute' + (s.autoLogoutMinutes === 1 ? '' : 's') + ' without using the system.'
        : 'Off: you stay logged in until you log out.';
    }
    function logAutoLogout() { var s = Settings.get(); Util.logEvent({ event: 'auto_logout', enabled: s.autoLogout, minutes: s.autoLogoutMinutes }); }
    alOn.addEventListener('change', function () { Settings.set({ autoLogout: alOn.checked }); showAutoLogout(); logAutoLogout(); });
    alMin.addEventListener('change', function () { Settings.set({ autoLogoutMinutes: Number(alMin.value) }); showAutoLogout(); logAutoLogout(); });
    showAutoLogout();

    /* ----- theme ----- */
    var thStatus = $('th-status');
    function showTheme() {
      var t = Settings.get().theme;
      Array.prototype.forEach.call(document.getElementsByName('theme'), function (r) { r.checked = r.value === t; });
      thStatus.textContent = t === 'dark' ? 'Dark mode is on.' : 'Default theme is on.';
    }
    Array.prototype.forEach.call(document.getElementsByName('theme'), function (r) {
      r.addEventListener('change', function () { if (r.checked) { Settings.set({ theme: r.value }); showTheme(); Util.logEvent({ event: 'theme', value: r.value }); } });
    });
    showTheme();

    /* ----- backup & restore ----- */
    var bkAlert = $('bk-alert'), bkBody = $('bk-body'), bkNow = $('bk-now');
    function bkShow(msg, type) { bkAlert.textContent = msg; bkAlert.className = 'alert ' + (type || 'error'); bkAlert.hidden = false; }
    function bkFail(err) {
      if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
      bkShow(err.message);
    }
    function bkWhen(v) {
      var d = new Date(String(v).replace(' ', 'T'));
      return isNaN(d.getTime()) ? String(v || '') : d.toLocaleString('en-PH', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
    }
    var KIND_TEXT = { manual: 'Manual', auto: 'Automatic', 'before-restore': 'Safety copy (before a restore)' };

    function bkRender(data) {
      var settings = data.settings || {};
      $('bk-auto').checked = !!settings.auto;
      $('bk-freq').value = settings.frequency || 'daily';
      $('bk-keep').value = String(settings.keep || 14);
      $('bk-freq').disabled = $('bk-keep').disabled = !settings.auto;
      $('bk-folder').textContent = data.folder ? 'Backups are saved in: ' + data.folder : '';
      $('bk-limit').textContent = data.upload_limit_mb
        ? 'This server accepts uploads up to ' + data.upload_limit_mb + ' MB. For a bigger backup, copy the file into the folder above and restore it from the list.'
        : '';
      if (!data.can_backup) {
        bkShow(data.reason || 'Backup is not available.');
        bkNow.disabled = $('bk-save').disabled = $('bk-restore-file').disabled = true;
        bkBody.innerHTML = '<tr><td colspan="4" class="muted">Not available.</td></tr>';
        return;
      }
      var rows = data.backups || [];
      if (!rows.length) { bkBody.innerHTML = '<tr><td colspan="4" class="muted">No backups yet. Click Back Up Now.</td></tr>'; return; }
      bkBody.innerHTML = rows.map(function (b) {
        var n = Util.esc(b.name);
        return '<tr data-name="' + n + '">' +
          '<td>' + Util.esc(bkWhen(b.created_at)) + '</td>' +
          '<td><span class="bk-kind">' + Util.esc(KIND_TEXT[b.kind] || b.kind) + '</span></td>' +
          '<td>' + Util.esc(b.size_text) + '</td>' +
          '<td><div class="bk-actions">' +
            '<button type="button" class="btn-outline btn-sm" data-act="download">Download</button>' +
            '<button type="button" class="btn-outline btn-sm" data-act="restore">Restore</button>' +
            '<button type="button" class="btn-outline btn-sm" data-act="delete">Delete</button>' +
          '</div></td></tr>';
      }).join('');
    }
    function bkLoad() {
      return Api.get(CONFIG.ENDPOINTS.BACKUP_LIST).then(function (res) { bkRender(res.data || {}); }).catch(bkFail);
    }

    function bkDownload(name) {
      return Api.download(CONFIG.ENDPOINTS.BACKUP_DOWNLOAD + '?name=' + encodeURIComponent(name), name)
        .then(function (f) { Api.saveFile(f.blob, f.name); });
    }

    bkNow.addEventListener('click', function () {
      bkAlert.hidden = true;
      bkNow.disabled = true; bkNow.classList.add('loading'); bkNow.querySelector('.btn-text').textContent = 'Backing up...';
      Api.post(CONFIG.ENDPOINTS.BACKUP_CREATE, {}, 300000)
        .then(function (res) {
          var b = res.data.backup;
          return bkDownload(b.name).then(function () {
            bkShow('Backup created (' + b.size_text + ') and downloaded. A copy is also kept in the list below.', 'success');
          });
        })
        .then(bkLoad)
        .catch(bkFail)
        .then(function () { bkNow.disabled = false; bkNow.classList.remove('loading'); bkNow.querySelector('.btn-text').textContent = 'Back Up Now'; });
    });

    $('bk-auto').addEventListener('change', function () { $('bk-freq').disabled = $('bk-keep').disabled = !this.checked; });
    $('bk-save').addEventListener('click', function () {
      bkAlert.hidden = true;
      Api.post(CONFIG.ENDPOINTS.BACKUP_SETTINGS, { auto: $('bk-auto').checked, frequency: $('bk-freq').value, keep: Number($('bk-keep').value) })
        .then(function (res) { bkShow(res.message || 'Saved.', 'success'); return bkLoad(); })
        .catch(bkFail);
    });

    /** Asks for the password, then restores. source = { name } or { file }. */
    function bkRestore(source, label) {
      var dlg = document.createElement('dialog');
      dlg.className = 'modal modal-sm';
      dlg.setAttribute('aria-label', 'Restore backup');
      dlg.innerHTML =
        '<header class="modal-head"><h2>Restore this backup?</h2></header>' +
        '<div class="modal-body">' +
          '<p class="confirm-lead">' + Util.esc(label) + '</p>' +
          '<p>This <strong>replaces all current farmers, parcels, history, logs, accounts and uploaded files</strong> with the ones in the backup. A safety copy of the current data is saved first. You will be logged out afterwards.</p>' +
          '<label class="bk-pw"><span class="lbl">Enter your password to confirm</span><input type="password" id="bk-pw" autocomplete="current-password"></label>' +
          '<small class="field-error" id="bk-pw-error"></small>' +
        '</div>' +
        '<footer class="modal-foot">' +
          '<button type="button" class="btn-outline" data-r="0">Cancel</button>' +
          '<button type="button" class="btn-danger" data-r="1">Restore</button>' +
        '</footer>';
      document.body.appendChild(dlg);
      var pw = dlg.querySelector('#bk-pw'), err = dlg.querySelector('#bk-pw-error'), go = dlg.querySelector('[data-r="1"]'), busy = false;
      function close() { try { dlg.close(); } catch (e) {} dlg.remove(); }
      function run() {
        if (busy) return;
        if (!pw.value) { err.textContent = 'Enter your password.'; pw.focus(); return; }
        busy = true; go.disabled = true; go.textContent = 'Restoring...'; err.textContent = '';
        var fd = new FormData();
        fd.append('password', pw.value);
        if (source.file) fd.append('file', source.file); else fd.append('name', source.name);
        Api.request(CONFIG.ENDPOINTS.BACKUP_RESTORE, { method: 'POST', body: fd, timeout: 600000 })
          .then(function (res) {
            Flash.set((res.message || 'The backup was restored.') + ' A copy of the previous data was saved as ' + res.data.safety + '.');
            try { sessionStorage.removeItem(CONFIG.STORAGE_KEYS.USER); } catch (e) {}
            window.location.replace(CONFIG.LOGIN_URL);
          })
          .catch(function (e) {
            busy = false; go.disabled = false; go.textContent = 'Restore';
            if (e.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
            err.textContent = (e.errors && e.errors.password) || e.message;
            pw.focus();
          });
      }
      dlg.addEventListener('click', function (e) {
        var b = e.target.closest('[data-r]');
        if (b && b.getAttribute('data-r') === '1') run();
        else if ((b && !busy) || e.target === dlg && !busy) close();
      });
      pw.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); run(); } });
      dlg.addEventListener('cancel', function (e) { e.preventDefault(); if (!busy) close(); });
      dlg.showModal();
      pw.focus();
    }

    bkBody.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-act]');
      if (!btn) return;
      var tr = btn.closest('tr'), name = tr.getAttribute('data-name');
      bkAlert.hidden = true;
      if (btn.dataset.act === 'download') {
        bkDownload(name).catch(bkFail);
      } else if (btn.dataset.act === 'restore') {
        bkRestore({ name: name }, name);
      } else if (btn.dataset.act === 'delete') {
        Ui.confirm({ title: 'Delete this backup?', lines: [name, 'The backup file will be removed from this computer. This cannot be undone.'], confirmText: 'Delete', danger: true })
          .then(function (yes) {
            if (!yes) return;
            return Api.post(CONFIG.ENDPOINTS.BACKUP_DELETE, { name: name })
              .then(function (res) { bkShow(res.message || 'Backup deleted.', 'success'); return bkLoad(); })
              .catch(bkFail);
          });
      }
    });

    $('bk-restore-file').addEventListener('click', function () {
      var f = $('bk-file').files[0], er = $('bk-file-error');
      er.textContent = ''; bkAlert.hidden = true;
      if (!f) { er.textContent = 'Choose a backup file first.'; return; }
      if (!/\.zip$/i.test(f.name)) { er.textContent = 'The backup must be a .zip file.'; return; }
      bkRestore({ file: f }, 'File: ' + f.name);
    });

    Auth.requireLogin().then(function (ok) { if (ok) bkLoad(); });
  };

  /* ---------- Activity logs (activity.php) ----------
   * GET api/activity.php?q=&category=&user=&from=&to=&page=1&per_page=25
   *   -> { data: { rows: [ { created_at, username, name, category, action, details, ip } ], total, categories: [], users: [ { username, name } ] } }
   * Export: api/export.php?report=activity&format=xlsx|csv  (same filters)
   */
  Pages.activity = function () {
    var state = { page: 1, per_page: 25 };
    var token = 0, timer = null, filled = false;
    function $(id) { return document.getElementById(id); }
    var alertBox = $('act-alert');
    var body = $('act-body');

    function showAlert(msg, type) { alertBox.textContent = msg; alertBox.className = 'alert ' + (type || 'error'); alertBox.hidden = false; }

    function filters() {
      return {
        q: $('a-q').value.trim(), category: $('a-category').value, user: $('a-user').value,
        from: $('a-from').value, to: $('a-to').value
      };
    }
    function qs(o) {
      return Object.keys(o).filter(function (k) { return o[k] !== '' && o[k] !== null && o[k] !== undefined; })
        .map(function (k) { return k + '=' + encodeURIComponent(o[k]); }).join('&');
    }
    function stamp(v) {
      var d = new Date(String(v).replace(' ', 'T'));
      return isNaN(d.getTime()) ? String(v || '') : d.toLocaleString('en-PH', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', second: '2-digit' });
    }

    function fillOptions(d) {
      var cat = $('a-category'), usr = $('a-user');
      if (!filled) {
        (d.categories || []).forEach(function (c) { cat.insertAdjacentHTML('beforeend', '<option>' + Util.esc(c) + '</option>'); });
        filled = true;
      }
      var keep = usr.value;   // the user list grows as new people appear in the log
      usr.innerHTML = '<option value="">All</option>' + (d.users || []).map(function (u) {
        return '<option value="' + Util.esc(u.username) + '">' + Util.esc(u.name ? u.name + ' (' + u.username + ')' : u.username) + '</option>';
      }).join('');
      usr.value = keep;
    }

    function renderRows(rows) {
      if (!rows || !rows.length) { body.innerHTML = '<tr><td colspan="6" class="empty">No activity found.</td></tr>'; return; }
      body.innerHTML = rows.map(function (r) {
        var who = r.name || r.username || '(unknown)';
        return '<tr>' +
          '<td>' + Util.esc(stamp(r.created_at)) + '</td>' +
          '<td class="act-user">' + Util.esc(who) + (r.username && r.name ? '<small>' + Util.esc(r.username) + '</small>' : '') + '</td>' +
          '<td><span class="badge cat cat-' + Util.esc(r.category) + '">' + Util.esc(r.category) + '</span></td>' +
          '<td>' + Util.esc(r.action) + '</td>' +
          '<td class="act-details">' + Util.esc(r.details) + '</td>' +
          '<td>' + Util.esc(r.ip) + '</td></tr>';
      }).join('');
    }

    function renderPagination(total) {
      var nav = $('pagination');
      var pages = Math.max(1, Math.ceil(total / state.per_page));
      if (pages <= 1) { nav.innerHTML = ''; return; }
      var start = Math.max(1, state.page - 2), end = Math.min(pages, start + 4);
      start = Math.max(1, end - 4);
      var html = '<button type="button" data-page="' + (state.page - 1) + '"' + (state.page <= 1 ? ' disabled' : '') + '>Prev</button>';
      if (start > 1) html += '<button type="button" data-page="1">1</button>' + (start > 2 ? '<span class="muted">...</span>' : '');
      for (var p = start; p <= end; p++) {
        html += '<button type="button" data-page="' + p + '"' + (p === state.page ? ' class="current" aria-current="page"' : '') + '>' + p + '</button>';
      }
      if (end < pages) html += (end < pages - 1 ? '<span class="muted">...</span>' : '') + '<button type="button" data-page="' + pages + '">' + pages + '</button>';
      html += '<button type="button" data-page="' + (state.page + 1) + '"' + (state.page >= pages ? ' disabled' : '') + '>Next</button>';
      nav.innerHTML = html;
    }

    function rangeOk() {
      var f = filters(), err = $('a-range-error');
      err.textContent = '';
      if (f.from && f.to && f.from > f.to) { err.textContent = 'The end date must be on or after the start date.'; return false; }
      return true;
    }

    function load() {
      if (!rangeOk()) return;
      var mine = ++token;
      var q = filters(); q.page = state.page; q.per_page = state.per_page;
      Api.get(CONFIG.ENDPOINTS.ACTIVITY + '?' + qs(q))
        .then(function (res) {
          if (mine !== token) return;
          var d = res.data || {}, total = Number(d.total) || 0;
          var pages = Math.max(1, Math.ceil(total / state.per_page));
          if (state.page > pages) { state.page = pages; load(); return; }
          alertBox.hidden = true;
          fillOptions(d);
          renderRows(d.rows);
          renderPagination(total);
          $('result-info').textContent = total ? Util.int(total) + ' activit' + (total > 1 ? 'ies' : 'y') : 'No activity';
          var f = filters();
          var bits = [];
          if (f.category) bits.push('Category: ' + f.category);
          if (f.user) bits.push('User: ' + f.user);
          if (f.from) bits.push('From ' + f.from);
          if (f.to) bits.push('To ' + f.to);
          if (f.q) bits.push('Search: ' + f.q);
          $('print-meta').textContent = (bits.length ? bits.join(' | ') + ' · ' : 'All activity · ') + Util.int(total) + ' record(s) · Printed ' + new Date().toLocaleString('en-PH');
        })
        .catch(function (err) {
          if (mine !== token) return;
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          body.innerHTML = '<tr><td colspan="6" class="empty">Could not load the activity log.</td></tr>';
          showAlert(err.message);
        });
    }
    function reload() { state.page = 1; load(); }

    ['a-category', 'a-user', 'a-from', 'a-to'].forEach(function (id) { $(id).addEventListener('change', reload); });
    $('a-q').addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(reload, 350); });
    $('per-page').addEventListener('change', function () { state.per_page = Number(this.value); reload(); });
    $('a-clear').addEventListener('click', function () {
      ['a-q', 'a-category', 'a-user', 'a-from', 'a-to'].forEach(function (id) { $(id).value = ''; });
      reload();
    });
    $('pagination').addEventListener('click', function (e) {
      var b = e.target.closest('[data-page]');
      if (!b || b.disabled) return;
      state.page = Number(b.getAttribute('data-page'));
      load();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function exportFile(format, btn) {
      if (!rangeOk()) return;
      var text = btn.textContent;
      btn.disabled = true; btn.textContent = 'Preparing...';
      var q = filters(); q.report = 'activity'; q.format = format;
      Api.download(CONFIG.ENDPOINTS.EXPORT + '?' + qs(q), 'rsbsa-activity-log.' + format)
        .then(function (file) { Api.saveFile(file.blob, file.name); showAlert('Download started: ' + file.name, 'success'); })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showAlert(err.message);
        })
        .then(function () { btn.disabled = false; btn.textContent = text; });
    }
    $('export-xlsx').addEventListener('click', function () { exportFile('xlsx', this); });
    $('export-csv').addEventListener('click', function () { exportFile('csv', this); });
    $('print-btn').addEventListener('click', function () {
      Util.logEvent({ event: 'print_activity', detail: $('print-meta').textContent });
      window.print();
    });

    /* ----- clear logs: pick the entries, see how many, confirm with the password ----- */
    function openClear() {
      var dlg = document.createElement('dialog');
      dlg.className = 'modal clear-modal';
      dlg.setAttribute('aria-label', 'Clear activity logs');
      dlg.innerHTML =
        '<header class="modal-head"><h2>Clear Activity Logs</h2></header>' +
        '<div class="modal-body">' +
          '<p class="hint">Deleted log entries cannot be brought back. Use <strong>Export Excel</strong> or <strong>Export CSV</strong> first if you want a copy.</p>' +
          '<div class="clear-row">' +
            '<label><span class="lbl">Delete entries</span><select id="cl-mode">' +
              '<option value="within">made within the last</option><option value="older">older than</option></select></label>' +
            '<label><span class="lbl">Period</span><select id="cl-period">' +
              '<option value="24h">24 hours</option><option value="7d">7 days</option><option value="30d">1 month (30 days)</option>' +
              '<option value="1y">1 year</option><option value="all">All entries</option></select></label>' +
          '</div>' +
          '<p class="notice warn" id="cl-count">Counting...</p>' +
          '<label class="bk-pw"><span class="lbl">Enter your password to confirm</span><input type="password" id="cl-pw" autocomplete="current-password"></label>' +
          '<small class="field-error" id="cl-pw-error"></small>' +
        '</div>' +
        '<footer class="modal-foot"><button type="button" class="btn-outline" data-r="0">Cancel</button>' +
        '<button type="button" class="btn-danger" data-r="1" disabled>Delete Entries</button></footer>';
      document.body.appendChild(dlg);
      var mode = dlg.querySelector('#cl-mode'), per = dlg.querySelector('#cl-period'), cnt = dlg.querySelector('#cl-count'),
          pw = dlg.querySelector('#cl-pw'), err = dlg.querySelector('#cl-pw-error'), go = dlg.querySelector('[data-r="1"]'), busy = false, tok = 0, n = 0;
      function close() { try { dlg.close(); } catch (e) {} dlg.remove(); }
      function sel() { return { period: per.value, mode: mode.value }; }

      function count() {
        mode.disabled = per.value === 'all';
        var t = ++tok; go.disabled = true; cnt.textContent = 'Counting...';
        var body = sel(); body.preview = true;
        Api.post(CONFIG.ENDPOINTS.ACTIVITY_CLEAR, body).then(function (res) {
          if (t !== tok) return;
          n = (res.data || {}).count || 0;
          cnt.textContent = n === 0 ? 'No log entries match this selection.'
            : n + (n === 1 ? ' log entry' : ' log entries') + ' will be deleted (' + (res.data.description || '') + ').';
          go.disabled = n === 0;
        }).catch(function (e) {
          if (e.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          cnt.textContent = e.message;
        });
      }
      function run() {
        if (busy || go.disabled) return;
        if (!pw.value) { err.textContent = 'Enter your password.'; pw.focus(); return; }
        busy = true; go.disabled = true; go.textContent = 'Deleting...'; err.textContent = '';
        var body = sel(); body.password = pw.value;
        Api.post(CONFIG.ENDPOINTS.ACTIVITY_CLEAR, body).then(function (res) {
          close();
          showAlert(res.message || 'The log entries were deleted.', 'success');
          state.page = 1; load();
        }).catch(function (e) {
          busy = false; go.disabled = false; go.textContent = 'Delete Entries';
          if (e.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          err.textContent = (e.errors && e.errors.password) || e.message;
          pw.focus();
        });
      }
      mode.addEventListener('change', count);
      per.addEventListener('change', count);
      dlg.addEventListener('click', function (e) {
        var b = e.target.closest('[data-r]');
        if (b && b.getAttribute('data-r') === '1') run();
        else if ((b || e.target === dlg) && !busy) close();
      });
      pw.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); run(); } });
      dlg.addEventListener('cancel', function (e) { e.preventDefault(); if (!busy) close(); });
      dlg.showModal();
      count();
    }
    $('clear-btn').addEventListener('click', openClear);

    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('activity');
      load();
    });
  };

  /* ---------- Add new pages above this line ---------- */

  /* =====================================================================
   * 5. Boot - pick the page from <body data-page="...">
   * ===================================================================== */
  window.RSBSA = { CONFIG: CONFIG, Api: Api, Auth: Auth, Pages: Pages, Settings: Settings };

  document.addEventListener('DOMContentLoaded', function () {
    var page = document.body.getAttribute('data-page');
    if (page && typeof Pages[page] === 'function') Pages[page]();
  });
})();
