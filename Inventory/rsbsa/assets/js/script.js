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
      CHECK_LOT: 'check_lot.php',     // GET -> duplicate / remaining-area check (see Pages.register)
      GET_FARMER: 'get_farmer.php',   // GET ?id=  -> one farmer record for updating
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
      headers: { 'Accept': 'application/json' },
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
    request: request
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

    logout: function () {
      return Api.post(CONFIG.ENDPOINTS.LOGOUT)
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
        return ok
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

      // logout
      logoutBtn.addEventListener('click', function () {
        logoutBtn.disabled = true;
        Auth.logout();
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

    function renderCommodity(rows) {
      var box = document.getElementById('commodity-list');
      if (!rows || !rows.length) { box.innerHTML = '<p class="empty">No data yet.</p>'; return; }

      var max = 0;
      rows.forEach(function (r) { max = Math.max(max, Number(r.area_ha) || 0); });

      box.innerHTML = rows.map(function (r) {
        var pct = max > 0 ? Math.round((Number(r.area_ha) || 0) / max * 100) : 0;
        return '<div class="bar-row">' +
          '<div class="bar-row-head"><span>' + Util.esc(r.commodity) + '</span>' +
          '<span>' + Util.ha(r.area_ha) + ' ha &middot; ' + Util.int(r.farmers) + ' farmers</span></div>' +
          '<div class="bar-track"><div class="bar-fill" style="width:' + pct + '%"></div></div>' +
          '</div>';
      }).join('');
    }

    function renderBarangay(rows) {
      var body = document.getElementById('barangay-body');
      if (!rows || !rows.length) { body.innerHTML = '<tr><td colspan="3" class="empty">No data yet.</td></tr>'; return; }
      body.innerHTML = rows.map(function (r) {
        return '<tr><td>' + Util.esc(r.barangay) + '</td>' +
          '<td class="num">' + Util.int(r.farmers) + '</td>' +
          '<td class="num">' + Util.ha(r.area_ha) + '</td></tr>';
      }).join('');
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

    function load() {
      refreshBtn.disabled = true;
      alertBox.hidden = true;

      Api.get(CONFIG.ENDPOINTS.DASHBOARD)
        .then(function (res) {
          var d = res.data || {};
          renderTotals(d.totals);
          renderCommodity(d.by_commodity);
          renderBarangay(d.by_barangay);
          renderRecent(d.recent_updates);
          lastUpdated.textContent = 'Updated ' + new Date().toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' });
        })
        .catch(function (err) {
          if (err.status === 401) { window.location.replace(CONFIG.LOGIN_URL); return; }
          showError(err.message);
        })
        .then(function () { refreshBtn.disabled = false; });
    }

    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('dashboard');
      refreshBtn.addEventListener('click', load);
      load();
    });
  };

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
   *   tax_declaration_{i}     file for parcel i (only if a new file was chosen)
   *   supporting_docs_{i}[]   files for parcel i (only when the lot is a duplicate)
   *   -> { success, message, data: { id, rsbsa_no, flagged: bool } }
   *   Validation errors: HTTP 422 { success:false, message, errors: { "surname": "...", "parcels.0.area_ha": "..." } }
   *
   * payload = {
   *   id, rsbsa_no, surname, first_name, middle_name, suffix, birth_date, valid_id_type, valid_id_no,
   *   address: { zone_sitio, barangay, municipality, province },
   *   spouse: { surname, first_name, middle_name } | null, spouse_separated, is_married_woman, maiden_name,
   *   children: [ "name", ... ], association,
   *   parcels: [ { id, cadastral_lot_no, area_ha, tax_declared_area_ha, cultivated_area_ha,
   *                location: { zone_sitio, barangay, municipality, province },
   *                commodity, crop_areas: { rice, vegetable, root_crops, banana, coconut, fruit_bearing } | null,
   *                livestock: [ { type, heads } ] | null, fish_size | null,
   *                farming_role, tax_declarant, duplicate_flag, keep_existing_tax_file } ]
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
      CROP_KEYS.forEach(function (k) { setVal(card, 'crop_' + k, crops[k]); });
      (p.livestock || []).forEach(function (a) { addAnimalRow(card, a.type, a.heads); });
      if (p.tax_file_name) {
        card.dataset.existingFile = p.tax_file_name;
        var note = card.querySelector('[data-role="existing-file"]');
        note.textContent = 'Current file: ' + p.tax_file_name + '. Choose a new file only to replace it.';
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
      cards.forEach(function (c) {
        total += num(c, 'area_ha') || 0;
        cultivated += num(c, 'cultivated_area_ha') || 0;
      });
      $('sum-parcels').textContent = cards.length;
      $('sum-area').textContent = Util.ha(total);
      $('sum-cultivated').textContent = Util.ha(cultivated);
    }

    /* ---------- family toggles ---------- */
    function syncFamily() {
      $('maiden-group').hidden = !checked(form, 'is_married_woman');
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
      } else {
        setNotice(card, null);
      }
    }

    /* ---------- file checks ---------- */
    function checkFiles(input) {
      var files = Array.prototype.slice.call(input.files || []);
      for (var i = 0; i < files.length; i++) {
        var ext = files[i].name.split('.').pop().toLowerCase();
        if (ALLOWED_EXT.indexOf(ext) === -1) {
          input.value = '';
          setInvalid(input, 'Only PDF, JPG or PNG files are allowed.');
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

      var dobEl = fld(form, 'birth_date');
      if (!dobEl.value) {
        bad(dobEl, 'Date of birth is required.');
      } else {
        var dob = new Date(dobEl.value + 'T00:00:00');
        if (isNaN(dob.getTime()) || dob > new Date() || dob.getFullYear() < 1900) bad(dobEl, 'Enter a valid date of birth.');
      }

      // family
      var spouseAny = val(form, 'spouse_surname') || val(form, 'spouse_first_name') || val(form, 'spouse_middle_name');
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
      var spouseAny = val(form, 'spouse_surname') || val(form, 'spouse_first_name') || val(form, 'spouse_middle_name');
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
        spouse_separated: checked(form, 'spouse_separated'),
        is_married_woman: married,
        maiden_name: married ? val(form, 'maiden_name') : '',
        children: qa('[data-f="child_name"]', childrenList).map(function (el) { return el.value.trim(); }),
        association: val(form, 'association'),
        parcels: parcelCards().map(collectParcel)
      };

      var fd = new FormData();
      fd.append('payload', JSON.stringify(payload));
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
      } else if (act === 'remove-parcel') {
        if (parcelCards().length > 1 && window.confirm('Remove this parcel?')) {
          card.remove();
          renumber();
          updateTotals();
          dirty = true;
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
      document.title = 'Update Farmer | Palo RSBSA';
      saveBtn.querySelector('.btn-text').textContent = 'Save Changes';
      showAlert('Loading record...', 'success');

      Api.get(CONFIG.ENDPOINTS.GET_FARMER + '?id=' + encodeURIComponent(editId))
        .then(function (res) {
          var f = (res.data && res.data.farmer) || {};
          var a = f.address || {};
          var s = f.spouse || {};

          ['rsbsa_no', 'surname', 'first_name', 'middle_name', 'suffix', 'birth_date', 'valid_id_type', 'valid_id_no', 'maiden_name', 'association']
            .forEach(function (n) { setVal(form, n, f[n]); });
          setVal(form, 'addr_zone_sitio', a.zone_sitio);
          setVal(form, 'addr_barangay', a.barangay);
          setVal(form, 'addr_municipality', a.municipality);
          setVal(form, 'addr_province', a.province);
          setVal(form, 'spouse_surname', s.surname);
          setVal(form, 'spouse_first_name', s.first_name);
          setVal(form, 'spouse_middle_name', s.middle_name);
          fld(form, 'spouse_separated').checked = !!f.spouse_separated;
          fld(form, 'is_married_woman').checked = !!f.is_married_woman;
          syncFamily();

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
   * GET api/farmers.php?q=&commodity=&role=&barangay=&flagged=0|1&sort=&dir=asc|desc&page=1&per_page=25
   *   sort is one of: rsbsa_no, name, total_area_ha, date_updated
   *   q should match name, RSBSA no., cadastral lot no. and barangay (farm or residential)
   *   -> { success, data: {
   *          rows: [ { id, rsbsa_no, name, barangay, commodities: ["Rice"], parcels: 2,
   *                    total_area_ha: 4.85, date_updated: "YYYY-MM-DD HH:MM:SS", flagged: false } ],
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
        flagged: $('f-flagged').checked ? 1 : 0,
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
            (r.flagged ? '<span class="badge">Duplicate</span>' : '') + '</td>' +
          '<td>' + Util.esc(r.barangay) + '</td>' +
          '<td>' + tags + '</td>' +
          '<td class="num">' + Util.int(r.parcels) + '</td>' +
          '<td class="num">' + Util.ha(r.total_area_ha) + '</td>' +
          '<td>' + Util.esc(Util.date(r.date_updated)) + '</td>' +
          '<td class="actions">' +
            '<button type="button" class="link-btn" data-view="' + Util.esc(r.id) + '">View</button>' +
            '<a class="link-btn" href="register.php?id=' + id + '">Edit</a></td>' +
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

    function row(label, value) {
      return '<div><dt>' + Util.esc(label) + '</dt><dd>' + (value ? Util.esc(value) : '&ndash;') + '</dd></div>';
    }

    function placeText(a) {
      a = a || {};
      return [a.zone_sitio, a.barangay, a.municipality, a.province].filter(Boolean).join(', ');
    }

    function detailHtml(f) {
      var s = f.spouse || {};
      var html = '<h3 class="detail-title">Basic Information</h3><dl class="detail-grid">' +
        row('Registration Number', f.rsbsa_no) +
        row('Name', fullName(f.surname, f.first_name, f.middle_name, f.suffix)) +
        row('Date of Birth', Util.date(f.birth_date)) +
        row('Valid ID', [f.valid_id_type, f.valid_id_no].filter(Boolean).join(' - ')) +
        row('Residential Address', placeText(f.address)) +
        '</dl>';

      html += '<h3 class="detail-title">Family Background</h3><dl class="detail-grid">' +
        row('Spouse', fullName(s.surname, s.first_name, s.middle_name)) +
        row('Separated from spouse', f.spouse_separated ? 'Yes' : (f.spouse ? 'No' : '')) +
        row('Mother Maiden Name', f.is_married_woman ? f.maiden_name : '') +
        row('Children', (f.children || []).join('; ')) +
        '</dl>';

      html += '<h3 class="detail-title">Association</h3><dl class="detail-grid">' +
        row('Name of Association', f.association) +
        row('Date Updated', Util.date(f.date_updated)) +
        '</dl>';

      var total = 0;
      var parcels = f.parcels || [];
      parcels.forEach(function (p) { total += Number(p.area_ha) || 0; });
      html += '<h3 class="detail-title">Parcels (' + parcels.length + ' &middot; ' + Util.ha(total) + ' ha total)</h3>';

      parcels.forEach(function (p, i) {
        var crops = [];
        var labels = { rice: 'Rice', vegetable: 'Vegetable', root_crops: 'Root Crops', banana: 'Banana', coconut: 'Coconut', fruit_bearing: 'Fruit Bearing' };
        Object.keys(p.crop_areas || {}).forEach(function (k) {
          if (Number(p.crop_areas[k]) > 0) crops.push((labels[k] || k) + ': ' + Util.ha(p.crop_areas[k]) + ' ha');
        });
        var animals = (p.livestock || []).map(function (a) { return a.type + ': ' + Util.int(a.heads); });
        var url = safeUrl(p.tax_file_url);

        html += '<div class="detail-parcel"><h4>Parcel ' + (i + 1) +
          (p.duplicate_flag ? '<span class="badge">Duplicate</span>' : '') + '</h4><dl class="detail-grid">' +
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
          '</dl>' +
          (url ? '<p><a href="' + Util.esc(url) + '" target="_blank" rel="noopener">Open tax declaration</a></p>' : '') +
          '</div>';
      });

      return html;
    }

    function openView(id) {
      $('view-title').textContent = 'Farmer Record';
      $('view-edit').href = 'register.php?id=' + encodeURIComponent(id);
      viewBody.innerHTML = '<p class="muted">Loading...</p>';
      if (!dialog.open) dialog.showModal();

      Api.get(CONFIG.ENDPOINTS.GET_FARMER + '?id=' + encodeURIComponent(id))
        .then(function (res) {
          var f = (res.data && res.data.farmer) || {};
          $('view-title').textContent = fullName(f.surname, f.first_name, f.middle_name, f.suffix) || 'Farmer Record';
          viewBody.innerHTML = detailHtml(f);
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
    ['f-commodity', 'f-role', 'f-barangay', 'f-flagged'].forEach(function (id) {
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

    body.addEventListener('click', function (e) {
      var b = e.target.closest('[data-view]');
      if (b) openView(b.getAttribute('data-view'));
    });

    $('view-close').addEventListener('click', function () { dialog.close(); });
    $('view-close-2').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); }); // click on backdrop

    /* ---------- start ---------- */
    Auth.requireLogin().then(function (ok) {
      if (!ok) return;
      Shell.init('farmers');
      load();
    });
  };

  /* ---------- Add the next pages here, for example ----------
  Pages.duplicates = function () { ... Shell.init('duplicates'); ... };
  Pages.reports    = function () { ... Shell.init('reports'); ... };
  ------------------------------------------------------------ */

  /* =====================================================================
   * 5. Boot - pick the page from <body data-page="...">
   * ===================================================================== */
  window.RSBSA = { CONFIG: CONFIG, Api: Api, Auth: Auth, Pages: Pages };

  document.addEventListener('DOMContentLoaded', function () {
    var page = document.body.getAttribute('data-page');
    if (page && typeof Pages[page] === 'function') Pages[page]();
  });
})();