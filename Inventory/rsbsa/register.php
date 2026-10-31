<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Farmer | Palo RSBSA</title>
  <link rel="icon" type="image/png" href="assets/img/palo-seal.png">
  <link rel="stylesheet" href="assets/css/style.css?v=<?= (int) @filemtime(__DIR__ . '/assets/css/style.css') ?>">
  <script>try{var s=JSON.parse(localStorage.getItem("rsbsa_settings")||"{}");if(s.theme==="dark")document.documentElement.setAttribute("data-theme","dark")}catch(e){}</script>
</head>
<body class="app-page" data-page="register">

  <div class="app">

    <!-- ===== Sidebar ===== -->
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
      <div class="sidebar-brand">
        <img class="brand-seal" src="assets/img/palo-seal.png" alt="Municipality of Palo seal" width="40" height="40">
        <div>
          <strong>Palo RSBSA</strong>
          <small>Administrator</small>
        </div>
      </div>

      <nav class="nav">
        <a href="dashboard.php" data-nav="dashboard"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg><span>Dashboard</span></a>
        <a href="farmers.php" data-nav="farmers"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Farmers</span></a>
        <a href="register.php" data-nav="register"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M16 11h6"/></svg><span>Register Farmer</span></a>
        <a href="duplicates.php" data-nav="duplicates"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21V4"/><path d="M4 4h12l-2 4 2 4H4"/></svg><span>Duplicate Flags</span></a>
        <a href="reports.php" data-nav="reports"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/><path d="M8 17v-3M12 17v-6M16 17v-4"/></svg><span>Reports &amp; Export</span></a>
        <a href="activity.php" data-nav="activity"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><span>Activity Logs</span></a>
        <a href="settings.php" data-nav="settings"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/></svg><span>Settings</span></a>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user" id="user-name">Administrator</div>
        <button type="button" class="btn-ghost" id="logout-btn"><svg class="nav-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg><span>Log Out</span></button>
      </div>
    </aside>
    <div class="scrim" id="scrim" hidden></div>

    <!-- ===== Main ===== -->
    <div class="main">
      <header class="topbar">
        <button type="button" class="menu-btn" id="menu-btn" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 id="page-title">Register Farmer</h1>
        <div class="topbar-actions">
          <span class="muted">Palo Registry System for Basic Sectors in Agriculture</span>
        </div>
      </header>

      <form id="reg-form" class="reg-form" novalidate autocomplete="off">
        <main class="content">
          <div id="reg-alert" class="alert" role="alert" hidden></div>

          <!-- ================= 1. BASIC INFORMATION ================= -->
          <section class="panel" aria-labelledby="sec-basic">
            <div class="section-head">
              <h2 id="sec-basic">Basic Information</h2>
              <div class="char-tools" title="Names with special characters (Ñ, II)">
                <span class="muted">Special characters:</span>
                <button type="button" class="chip" data-insert="Ñ">Ñ</button>
                <button type="button" class="chip" data-insert="ñ">ñ</button>
              </div>
            </div>

            <div class="form-grid cols-3">
              <div class="field">
                <label><span class="lbl">Registration Number (RSBSA Number)</span>
                  <input type="text" data-f="rsbsa_no" maxlength="40" placeholder="Leave blank to auto-generate"></label>
                <small class="field-error"></small>
              </div>
            </div>

            <h3 class="sub-title">Name of Registered Farmer</h3>
            <div class="form-grid cols-4">
              <div class="field">
                <label><span class="lbl">Surname <b class="req">*</b></span>
                  <input type="text" class="name-input" data-f="surname" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">First Name <b class="req">*</b></span>
                  <input type="text" class="name-input" data-f="first_name" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Middle Name</span>
                  <input type="text" class="name-input" data-f="middle_name" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Suffix (Jr., Sr., II ...)</span>
                  <input type="text" class="name-input" data-f="suffix" maxlength="10" list="suffix-list"></label>
                <small class="field-error"></small>
              </div>
            </div>

            <div class="form-grid cols-3">
              <div class="field">
                <label><span class="lbl">Date of Birth <b class="req">*</b></span>
                  <input type="date" data-f="birth_date"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Valid ID <b class="req">*</b></span>
                  <input type="text" data-f="valid_id_type" maxlength="60" list="id-list" placeholder="Type or pick an ID"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">ID Number <b class="req">*</b></span>
                  <input type="text" data-f="valid_id_no" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
            </div>

            <div class="form-grid cols-3">
              <div class="field">
                <label><span class="lbl">Civil Status <b class="req">*</b></span>
                  <select data-f="civil_status">
                    <option value="">Select...</option>
                    <option>Single</option>
                    <option>Married</option>
                    <option>Widowed</option>
                    <option>Separated</option>
                    <option>Annulled</option>
                  </select></label>
                <small class="field-error"></small>
              </div>
            </div>

            <h3 class="sub-title">Photo and Valid ID</h3>
            <div class="id-uploads">
              <div class="photo-box" id="photo-box" aria-live="polite">
                <img id="photo-preview" alt="2x2 picture preview" hidden>
                <span id="photo-empty">2 x 2<br>photo</span>
              </div>
              <div class="form-grid cols-2 id-fields">
                <div class="field">
                  <label><span class="lbl">2 x 2 Picture <b class="req" id="photo-req">*</b></span>
                    <input type="file" data-f="photo" accept=".jpg,.jpeg,.png"></label>
                  <small class="hint" data-role="existing-photo" hidden></small>
                  <small class="hint">JPG or PNG, up to 5 MB.</small>
                  <small class="field-error"></small>
                </div>
                <div class="field">
                  <label><span class="lbl">Valid ID (scan or photo) <b class="req" id="idfile-req">*</b></span>
                    <input type="file" data-f="valid_id_file" accept=".pdf,.jpg,.jpeg,.png"></label>
                  <small class="hint" data-role="existing-idfile" hidden></small>
                  <small class="hint">PDF, JPG or PNG, up to 5 MB.</small>
                  <small class="field-error"></small>
                </div>
              </div>
            </div>

            <h3 class="sub-title">Residential Address</h3>
            <div class="form-grid cols-4">
              <div class="field">
                <label><span class="lbl">Zone / Sitio</span>
                  <input type="text" data-f="addr_zone_sitio" maxlength="80"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Barangay <b class="req">*</b></span>
                  <input type="text" data-f="addr_barangay" maxlength="80"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Municipality <b class="req">*</b></span>
                  <input type="text" data-f="addr_municipality" maxlength="80"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Province <b class="req">*</b></span>
                  <input type="text" data-f="addr_province" maxlength="80"></label>
                <small class="field-error"></small>
              </div>
            </div>
          </section>

          <!-- ================= 2. FAMILY BACKGROUND ================= -->
          <section class="panel" aria-labelledby="sec-family">
            <div class="section-head"><h2 id="sec-family">Family Background</h2></div>

            <div id="spouse-group">
            <h3 class="sub-title">Name of Spouse</h3>
            <div class="form-grid cols-3">
              <div class="field">
                <label><span class="lbl">Surname</span>
                  <input type="text" class="name-input" data-f="spouse_surname" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">First Name</span>
                  <input type="text" class="name-input" data-f="spouse_first_name" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
              <div class="field">
                <label><span class="lbl">Middle Name</span>
                  <input type="text" class="name-input" data-f="spouse_middle_name" maxlength="60"></label>
                <small class="field-error"></small>
              </div>
            </div>

            </div>
            <p class="hint" id="spouse-hint" hidden>Spouse duplicate rules do not apply when the marriage has ended (Widowed, Separated, Annulled).</p>

            <div class="check-row">
              <label class="checkbox"><input type="checkbox" data-f="is_married_woman"><span>Registrant is a married woman</span></label>
            </div>

            <div class="form-grid cols-3" id="maiden-group" hidden>
              <div class="field">
                <label><span class="lbl">Mother Maiden Name (for married woman) <b class="req">*</b></span>
                  <input type="text" class="name-input" data-f="maiden_name" maxlength="120"></label>
                <small class="field-error"></small>
              </div>
            </div>

            <h3 class="sub-title">Name of Children</h3>
            <div id="children-list" class="stack"></div>
            <button type="button" class="btn-outline btn-sm" data-act="add-child">+ Add Child</button>
          </section>

          <!-- ================= 3. PARCELS ================= -->
          <section class="panel" aria-labelledby="sec-parcels">
            <div class="section-head">
              <h2 id="sec-parcels">Parcel Particulars</h2>
              <button type="button" class="btn-outline btn-sm" data-act="add-parcel">+ Add Parcel</button>
            </div>
            <p class="hint">Add one block per parcel. The total area for the farmer is computed from all parcels.</p>
            <div id="parcels-list" class="stack"></div>
          </section>

          <!-- ================= 4. RECORD ================= -->
          <section class="panel" aria-labelledby="sec-record">
            <div class="section-head"><h2 id="sec-record">Record</h2></div>
            <div class="form-grid cols-3">
              <div class="field">
                <label><span class="lbl">Date Updated</span>
                  <input type="text" id="date-updated" readonly tabindex="-1"></label>
                <small class="hint">Set automatically when saved.</small>
              </div>
            </div>
          </section>
        </main>

        <!-- Sticky save bar -->
        <div class="savebar">
          <div class="savebar-info">
            <span><strong id="sum-parcels">0</strong> parcel(s)</span>
            <span>Total area: <strong id="sum-area">0.00</strong> ha</span>
            <span>Cultivated: <strong id="sum-cultivated">0.00</strong> ha</span>
          </div>
          <div class="savebar-actions">
            <button type="button" class="btn-danger" id="delete-btn" hidden>Delete Farmer</button>
            <button type="button" class="btn-outline" id="cancel-btn">Cancel</button>
            <button type="submit" class="btn-primary btn-inline" id="save-btn">
              <span class="btn-text">Save Record</span>
              <span class="spinner" aria-hidden="true"></span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Suggestions -->
  <datalist id="suffix-list">
    <option value="Jr."></option><option value="Sr."></option>
    <option value="II"></option><option value="III"></option><option value="IV"></option>
  </datalist>
  <datalist id="id-list">
    <option value="PhilSys National ID"></option>
    <option value="Driver's License"></option>
    <option value="Voter's ID"></option>
    <option value="Postal ID"></option>
    <option value="UMID"></option>
    <option value="SSS ID"></option>
    <option value="Passport"></option>
    <option value="PRC ID"></option>
    <option value="Senior Citizen ID"></option>
  </datalist>

  <!-- ============ Template: child row ============ -->
  <template id="tpl-child">
    <div class="inline-row child-row">
      <div class="field grow">
        <label><span class="lbl sr-only">Name of child</span>
          <input type="text" class="name-input" data-f="child_name" maxlength="120" placeholder="Surname, First Name, Middle Name"></label>
        <small class="field-error"></small>
      </div>
      <button type="button" class="link-danger" data-act="remove-child">Remove</button>
    </div>
  </template>

  <!-- ============ Template: parcel ============ -->
  <template id="tpl-parcel">
    <section class="parcel-card">
      <header class="parcel-head">
        <h3>Parcel <span class="parcel-no">1</span></h3>
        <span class="parcel-actions">
          <button type="button" class="btn-outline btn-sm" data-act="parcel-history" hidden>History</button>
          <button type="button" class="link-danger" data-act="remove-parcel">Remove Parcel</button>
        </span>
      </header>

      <div class="form-grid cols-4">
        <div class="field">
          <label><span class="lbl">Cadastral Lot No. <b class="req">*</b></span>
            <input type="text" data-f="cadastral_lot_no" maxlength="40"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Area in Hectares <b class="req">*</b></span>
            <input type="number" data-f="area_ha" min="0" step="0.0001" inputmode="decimal"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Area per Tax Declaration (ha) <b class="req">*</b></span>
            <input type="number" data-f="tax_declared_area_ha" min="0" step="0.0001" inputmode="decimal"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Actual Cultivated Area (ha) <b class="req">*</b></span>
            <input type="number" data-f="cultivated_area_ha" min="0" step="0.0001" inputmode="decimal"></label>
          <small class="field-error"></small>
        </div>
      </div>

      <div class="notice" data-role="lot-notice" hidden></div>

      <div class="sub-row">
        <h4 class="sub-title">Farm Location</h4>
        <button type="button" class="link-btn" data-act="copy-address">Same as residential address</button>
      </div>
      <div class="form-grid cols-4">
        <div class="field">
          <label><span class="lbl">Zone / Sitio</span>
            <input type="text" data-f="loc_zone_sitio" maxlength="80"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Barangay <b class="req">*</b></span>
            <input type="text" data-f="loc_barangay" maxlength="80"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Municipality <b class="req">*</b></span>
            <input type="text" data-f="loc_municipality" maxlength="80"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Province <b class="req">*</b></span>
            <input type="text" data-f="loc_province" maxlength="80"></label>
          <small class="field-error"></small>
        </div>
      </div>

      <h4 class="sub-title">Sectoral Commodity</h4>
      <div class="form-grid cols-4">
        <div class="field">
          <label><span class="lbl">Commodity <b class="req">*</b></span>
            <select data-f="commodity">
              <option value="">Select...</option>
              <option>Rice</option>
              <option>Vegetable</option>
              <option>Root Crops</option>
              <option>Banana</option>
              <option>Coconut</option>
              <option>Livestock</option>
              <option>Fishery</option>
            </select></label>
          <small class="field-error"></small>
        </div>
      </div>

      <!-- Crops: total area in hectares per crop -->
      <div data-group="crop" hidden>
        <h4 class="sub-title">Total Area in Hectares per Crop</h4>
        <div class="form-grid cols-6">
          <div class="field"><label><span class="lbl">Rice</span><input type="number" data-f="crop_rice" min="0" step="0.0001" inputmode="decimal"></label><small class="field-error"></small></div>
          <div class="field"><label><span class="lbl">Vegetable</span><input type="number" data-f="crop_vegetable" min="0" step="0.0001" inputmode="decimal"></label><small class="field-error"></small></div>
          <div class="field"><label><span class="lbl">Root Crops</span><input type="number" data-f="crop_root_crops" min="0" step="0.0001" inputmode="decimal"></label><small class="field-error"></small></div>
          <div class="field"><label><span class="lbl">Banana</span><input type="number" data-f="crop_banana" min="0" step="0.0001" inputmode="decimal"></label><small class="field-error"></small></div>
          <div class="field"><label><span class="lbl">Coconut</span><input type="number" data-f="crop_coconut" min="0" step="0.0001" inputmode="decimal"></label><small class="field-error"></small></div>
          <div class="field"><label><span class="lbl">Fruit Bearing</span><input type="number" data-f="crop_fruit_bearing" min="0" step="0.0001" inputmode="decimal"></label><small class="field-error"></small></div>
        </div>
      </div>

      <!-- Livestock: number of heads -->
      <div data-group="livestock" hidden>
        <h4 class="sub-title">No. of Heads (Cow, Carabao, Sheep, Goat, Swine, Poultry, etc.)</h4>
        <div class="stack" data-list="animals"></div>
        <button type="button" class="btn-outline btn-sm" data-act="add-animal">+ Add Animal</button>
        <small class="field-error" data-err-group="livestock"></small>
      </div>

      <!-- Fishery: size of fish cage / pond -->
      <div data-group="fishery" hidden>
        <h4 class="sub-title">Fishery</h4>
        <div class="form-grid cols-3">
          <div class="field">
            <label><span class="lbl">Size of Fish Cage / Fish Pond <b class="req">*</b></span>
              <input type="text" data-f="fish_size" maxlength="60" placeholder="e.g. 10 x 10 m"></label>
            <small class="field-error"></small>
          </div>
        </div>
      </div>

      <h4 class="sub-title">Farming Status</h4>
      <div class="form-grid cols-4">
        <div class="field">
          <label><span class="lbl">Status <b class="req">*</b></span>
            <select data-f="farming_role">
              <option value="">Select...</option>
              <option>Owner</option>
              <option>Tenant</option>
              <option>Leasee</option>
              <option>Actual Tiller</option>
              <option>Farm Worker</option>
            </select></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Name of Tax Declarant / Owner <b class="req">*</b></span>
            <input type="text" class="name-input" data-f="tax_declarant" maxlength="120"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Name of Association (if applicable)</span>
            <input type="text" data-f="association" maxlength="120"></label>
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label><span class="lbl">Attached Tax Declaration <b class="req">*</b></span>
            <input type="file" data-f="tax_file" accept=".pdf,.jpg,.jpeg,.png"></label>
          <small class="hint" data-role="existing-file" hidden></small>
          <small class="field-error"></small>
        </div>
      </div>

      <div class="form-grid cols-3">
        <div class="field">
          <label><span class="lbl">Parcel Remark</span>
            <select data-f="parcel_status">
              <option value="Active" selected>Active</option>
              <option value="Inactive">In-active</option>
              <option value="Deceased">Deceased</option>
            </select></label>
          <small class="hint" data-role="status-hint">Active parcels are counted in the farmer's total area.</small>
          <small class="field-error"></small>
        </div>
      </div>

      <!-- Shown only when the lot is already registered to another farmer -->
      <div data-group="support" hidden>
        <div class="form-grid cols-3">
          <div class="field">
            <label><span class="lbl">Supporting Documents <b class="req">*</b></span>
              <input type="file" data-f="support_docs" accept=".pdf,.jpg,.jpeg,.png" multiple></label>
            <small class="hint">Required because this lot and tax declaration are already registered. The record will be flagged as a duplication.</small>
            <small class="field-error"></small>
          </div>
        </div>
      </div>
    </section>
  </template>

  <script src="assets/js/script.js?v=<?= (int) @filemtime(__DIR__ . '/assets/js/script.js') ?>"></script>
</body>
</html>
