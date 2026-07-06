<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DDC Wifi Captive Portal Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600&family=Work+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/captive-admin.css">
</head>
<body>

<div class="shell">
  <header class="topbar">
    <div class="brand-mini">
      <!-- Swap the src below for your logo file (png/svg/jpg). If it fails
           to load, a "BG" placeholder mark is shown instead. -->
      <img src="../../assets/img/fposi-logo.png" alt="Business logo" class="logo" id="brandLogo"
           onerror="this.style.display='none'; document.getElementById('brandFallbackMark').style.display='flex'; document.getElementById('brandName').style.display='block';">
      <span class="mark" id="brandFallbackMark" style="display:none;">DC</span>
      <div>
        <div class="name" id="brandName">DDC Wifi Captive Portal Admin</div>
        <div class="sub">WifiTestMode vouchers</div>
      </div>
    </div>
  </header>

  <div class="layout">

    <!-- ---------- Create voucher form ---------- -->
    <section class="panel">
      <h2>Create vouchers</h2>
      <p class="panel-sub">Generates six-character codes for guest Wi‑Fi access.</p>

      <form id="voucherForm">
        <div class="field">
          <label for="vName">Voucher / Username</label>
          <input type="text" id="vName" placeholder="e.g. Front Desk Guest" required>
        </div>

        <div class="field">
          <label for="vCount">Number of vouchers to make</label>
          <input type="number" id="vCount" min="1" max="500" value="1" required>
        </div>

        <div class="field">
          <label for="vDuration">Duration / usage time</label>
          <div class="row-split">
            <input class="grow" type="number" id="vDuration" min="1" value="1" required>
            <select class="shrink" id="vDurationUnit">
              <option value="Minutes">Minutes</option>
              <option value="Hours" selected>Hours</option>
              <option value="Days">Days</option>
            </select>
          </div>
          <p class="hint">How long the voucher stays valid once activated.</p>
        </div>

        <div class="field">
          <label for="vUses">Number of times voucher can be used</label>
          <input type="number" id="vUses" min="0" value="1" required>
          <p class="hint">0 = unlimited uses.</p>
        </div>

        <div class="field">
          <label for="vDataLimit">Data limitation (MB)</label>
          <input type="number" id="vDataLimit" min="0" value="0">
          <p class="hint">0 = no limit.</p>
        </div>

        <div class="field">
          <label for="vDataCap">Data cap (MB)</label>
          <input type="number" id="vDataCap" min="0" value="0">
          <p class="hint">0 = no cap.</p>
        </div>

        <button type="submit" class="generate">
          <span>Generate voucher(s)</span>
          <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h12M11 5l5 5-5 5"/></svg>
        </button>

        <p class="form-msg" id="formMsg"></p>
      </form>
      <div class="topbar-right" id="clock" style="margin-top:25px;margin-bottom:30px;"></div>
      <p class="legal">Powered by DDC Wifi Team<br>Copyright © <span id="year"></span> DDC Data Processing OPC. <br> All rights reserved.</p>
    </section>

    <!-- ---------- Vouchers table ---------- -->
    <section class="panel table-panel">
      <div class="stats-row">
        <div class="stat"><div class="num" id="statTotal">0</div><div class="lbl">Total vouchers</div></div>
        <div class="stat"><div class="num" id="statActive">0</div><div class="lbl">Active</div></div>
        <div class="stat"><div class="num" id="statUsed">0</div><div class="lbl">Used up</div></div>
        <div class="stat"><div class="num" id="statRevoked">0</div><div class="lbl">Revoked</div></div>
      </div>

      <div class="table-toolbar">
        <input type="search" id="searchBox" placeholder="Search by code or voucher name…">
        <button class="btn-ghost" id="exportBtn">Export CSV</button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Code</th>
              <th>Voucher / Username</th>
              <th>Duration</th>
              <th style="min-width: 100px;">Uses</th>
              <th>Data limit</th>
              <th>Data cap</th>
              <th>Status</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="voucherBody">
            <tr class="empty-row"><td colspan="9">No vouchers yet — create one on the left to get started.</td></tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <div class="page-info">Showing <span class="mono" id="pageRangeLabel">0</span> of <span class="mono" id="pageTotalLabel">0</span></div>
        <div class="page-controls" id="pageControls"></div>
      </div>
    </section>

  </div>

</div>

<div class="toast" id="toast"></div>
<script src="../../assets/js/sole.js"></script>
<script src="../../assets/js/captive-admin/captive-admin.js"></script>
</body>
</html>