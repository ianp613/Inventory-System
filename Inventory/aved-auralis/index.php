<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AVED AURALIS &mdash; Eastern Visayas</title>
<style>
  :root{
    --bg: #f6f1e6;
    --panel: #ffffff;
    --panel-raised: #faf6ec;
    --ink: #2b2f3a;
    --muted: #767a86;
    --amber: #c68a1f;
    --teal: #3f7a4a;
    --line: #e8e0cd;
    --danger: #c0483f;
    --navy: #1c2760;
    --radius: 10px;
    --font-head: -apple-system, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    --font-mono: ui-monospace, "SF Mono", "JetBrains Mono", Consolas, "Liberation Mono", Menlo, monospace;
  }
  *{ box-sizing: border-box; }
  html,body{
    margin:0; padding:0; min-height:100%;
    background: var(--bg);
    color: var(--ink);
    font-family: var(--font-head);
  }
  body{
    background-color: var(--bg);
  }
  .wrap{
    max-width: 760px;
    margin: 0 auto;
    padding: 28px 20px 60px;
  }

  /* LNU branding */
  .motto-strip{
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 8px 0 14px;
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 1.5px;
    color: var(--amber);
    text-transform: uppercase;
  }
  .motto-dot{ color: var(--line); letter-spacing: 0; }
  .brand-group{
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .lnu-logo{
    width: 52px;
    height: 52px;
    flex: 0 0 auto;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,.4));
  }

  /* header */
  .station-head{
    display:flex;
    align-items: baseline;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    border-bottom: 1px solid var(--line);
    padding-bottom: 18px;
    margin-bottom: 22px;
  }
  .brand{
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.3px;
    color: var(--ink);
  }
  .brand span{ color: var(--amber); }
  .brand small{
    display:block;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--muted);
    font-weight: 400;
    letter-spacing: 0.5px;
    margin-top: 3px;
  }
  .controls{
    display:flex;
    gap: 8px;
    align-items:center;
    flex-wrap: wrap;
  }
  #searchForm{ display:flex; gap:6px; position: relative; }
  .search-field{ position: relative; }
  #cityInput{
    background: var(--panel);
    border: 1px solid var(--line);
    color: var(--ink);
    padding: 8px 10px;
    border-radius: var(--radius);
    font-family: var(--font-mono);
    font-size: 13px;
    width: 200px;
    outline: none;
  }
  #cityInput:focus{ border-color: var(--amber); }
  .suggestions{
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--panel-raised);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    z-index: 20;
    max-height: 260px;
    overflow-y: auto;
    list-style: none;
    margin: 0;
    padding: 4px;
    display: none;
  }
  .suggestions.open{ display: block; }
  .suggestions li{
    padding: 8px 9px;
    border-radius: 2px;
    cursor: pointer;
    font-family: var(--font-mono);
    font-size: 12.5px;
  }
  .suggestions li:hover, .suggestions li.active{
    background: var(--bg);
  }
  .suggestions li .muni{ color: var(--ink); }
  .suggestions li .region{ color: var(--muted); font-size: 11px; display:block; margin-top:2px; }
  .suggestions .empty{ color: var(--muted); cursor: default; }
  .suggestions .empty:hover{ background: none; }
  button{
    background: var(--panel);
    border: 1px solid var(--line);
    color: var(--ink);
    padding: 8px 12px;
    border-radius: var(--radius);
    font-family: var(--font-mono);
    font-size: 12px;
    cursor: pointer;
  }
  button:hover{ border-color: var(--muted); }
  button:active{ transform: translateY(1px); }
  #unitToggle{ color: var(--amber); border-color: var(--amber); }

  /* panels */
  .panel{
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 18px;
    box-shadow: 0 2px 14px rgba(28, 39, 96, 0.06);
  }
  .panel h2{
    margin: 0 0 14px;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    letter-spacing: 0.4px;
    font-family: var(--font-mono);
  }

  /* status / empty state */
  #status{
    color: var(--muted);
    font-family: var(--font-mono);
    font-size: 13px;
    line-height: 1.6;
  }
  #status.error{ color: var(--danger); }

  .hero-panel{
    background: transparent;
    border: none;
    box-shadow: none;
    padding: 0;
  }
  .hero-panel #status{ padding: 20px; background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: 0 2px 14px rgba(28,39,96,0.06); }
  .hero-card{
    position: relative;
    border-radius: 18px;
    padding: 24px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(28, 39, 96, 0.18);
    overflow: hidden;
    min-height: 190px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .hero-top{
    display: flex;
    align-items: flex-start;
    gap: 16px;
  }
  .hero-icon{ flex: 0 0 auto; margin-top: 2px; }
  .hero-icon svg{ display: block; }
  .hero-main{ flex: 1 1 auto; }
  .hero-temp{
    font-size: 48px;
    font-weight: 700;
    line-height: 1;
    font-family: var(--font-mono);
  }
  .hero-condition{
    font-size: 16px;
    font-weight: 600;
    opacity: 0.95;
    margin-top: 4px;
  }
  .hero-datetime{
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.6px;
    opacity: 0.8;
    margin-top: 10px;
    text-transform: uppercase;
  }
  .hero-bottom{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 18px;
    flex-wrap: wrap;
  }
  .hero-location{
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
  }
  .hero-chips{ display: flex; gap: 8px; flex-wrap: wrap; }
  .hero-chip{
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(2px);
    border-radius: 999px;
    padding: 5px 11px;
    font-family: var(--font-mono);
    font-size: 11px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* barograph */
  .barograph{
    display:flex;
    align-items:flex-end;
    gap: 5px;
    height: 110px;
    overflow-x: auto;
    padding-bottom: 4px;
  }
  .bar-col{
    flex: 0 0 auto;
    width: 26px;
    display:flex;
    flex-direction: column;
    align-items:center;
    gap: 6px;
    height: 100%;
    justify-content: flex-end;
  }
  .bar-temp{
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--muted);
  }
  .bar-stem{
    width: 6px;
    background: var(--amber);
    border-radius: 2px 2px 0 0;
    min-height: 2px;
  }
  .bar-stem.wet{ background: var(--teal); }
  .bar-hour{
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--muted);
  }

  /* ledger */
  table.ledger{
    width: 100%;
    border-collapse: collapse;
    font-family: var(--font-mono);
    font-size: 13px;
  }
  table.ledger th{
    text-align: left;
    font-weight: 400;
    color: var(--muted);
    font-size: 11px;
    padding: 0 8px 8px 0;
    border-bottom: 1px solid var(--line);
  }
  table.ledger td{
    padding: 9px 8px 9px 0;
    border-bottom: 1px solid var(--line);
    vertical-align: middle;
  }
  table.ledger tr:last-child td{ border-bottom: none; }
  .day-icon{ display:flex; align-items:center; gap:10px; }
  .day-icon-badge{
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
  }
  .day-icon svg{ flex: 0 0 auto; }
  .hi{ color: var(--ink); font-weight: 600; }
  .lo{ color: var(--muted); }
  .precip{ color: var(--teal); }

  /* advisories */
  .advisory-grid{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 14px;
  }
  .advisory-card{
    background: var(--panel-raised);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 14px 16px;
  }
  .advisory-label{
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--muted);
    margin-bottom: 8px;
  }
  .advisory-label svg{ flex: 0 0 auto; }
  .advisory-verdict{
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1.2;
  }
  .advisory-verdict.good{ color: var(--teal); }
  .advisory-verdict.warn{ color: var(--amber); }
  .advisory-verdict.bad{ color: var(--danger); }
  .advisory-verdict.neutral{ color: var(--ink); }
  .advisory-detail{
    font-family: var(--font-mono);
    font-size: 12px;
    color: var(--muted);
    line-height: 1.5;
  }
  .advisory-word{
    font-size: 30px;
    font-weight: 700;
    color: var(--amber);
    margin-bottom: 4px;
  }
  .chip-list{
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 2px;
  }
  .chip{
    font-family: var(--font-mono);
    font-size: 12px;
    background: var(--bg);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 4px 10px;
    color: var(--ink);
  }

  footer{
    margin-top: 20px;
    text-align: center;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--muted);
  }
  footer a{ color: var(--muted); }

  /* About modal */
  .about-overlay{
    position: fixed;
    inset: 0;
    background: rgba(20, 22, 35, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 100;
  }
  .about-overlay.open{ display: flex; }
  .about-modal{
    background: var(--bg);
    width: 100%;
    max-width: 560px;
    max-height: 85vh;
    border-radius: 14px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .about-modal-scroll{
    overflow-y: auto;
    padding-top: 44px;
  }
  .about-close{
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    background: rgba(28, 39, 96, 0.08);
    border: none;
    border-radius: 50%;
    font-size: 18px;
    line-height: 1;
    color: var(--muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }
  .about-close:hover{ color: var(--ink); background: rgba(28, 39, 96, 0.14); }
  .about-section{
    background: var(--panel);
    border-left: 4px solid var(--amber);
    border-radius: 8px;
    padding: 20px 22px;
    margin: 18px;
    box-shadow: 0 2px 10px rgba(28,39,96,0.05);
  }
  .about-section h3{
    font-family: Georgia, "Iowan Old Style", "Times New Roman", serif;
    color: var(--navy);
    font-size: 19px;
    margin: 0 0 10px;
  }
  .about-section p{
    color: var(--ink);
    font-size: 14px;
    line-height: 1.65;
    margin: 0;
  }
  .about-dev-card{ padding: 18px 22px; }
  .about-dev{
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .about-avatar{
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--navy);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    font-family: Georgia, serif;
    flex: 0 0 auto;
    overflow: hidden;
  }
  .about-avatar img{ width: 100%; height: 100%; object-fit: cover; }
  .about-dev-label{
    font-family: var(--font-mono);
    font-size: 10px;
    letter-spacing: 1px;
    color: var(--amber);
    text-transform: uppercase;
  }
  .about-dev-name{
    font-family: Georgia, serif;
    font-weight: 700;
    font-size: 16px;
    color: var(--navy);
    margin-top: 2px;
  }
  .about-dev-role{
    font-size: 12px;
    color: var(--muted);
    margin-top: 1px;
  }
  .about-footer{
    padding: 14px 22px;
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--muted);
    text-align: center;
    border-top: 1px solid var(--line);
    line-height: 1.6;
  }
  .about-tagline{
    padding: 12px 22px 22px;
    text-align: center;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--muted);
  }

  @media (prefers-reduced-motion: reduce){
    * { transition: none !important; }
  }

  @media (max-width: 480px){
    #cityInput{ width: 120px; }
    .hero-temp{ font-size: 38px; }
  }
</style>
</head>
<body>
<div class="wrap">

  <div class="motto-strip">
    <span>Integrity</span><span class="motto-dot">&middot;</span><span>Excellence</span><span class="motto-dot">&middot;</span><span>Service</span>
  </div>

  <div class="station-head">
    <div class="brand-group">
      <img class="lnu-logo" src="data:image/png;base64,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" alt="Leyte Normal University seal">
      <div class="brand">
        AVED AURALIS
        <small>Values Education Project &middot; Leyte Normal University</small>
        <small>Eastern Visayas, Philippines &middot; readings via open&#8209;meteo</small>
      </div>
    </div>
    <div class="controls">
      <form id="searchForm" autocomplete="off">
        <div class="search-field">
          <input id="cityInput" type="text" placeholder="Search a municipality or barangay" autocomplete="off">
          <ul class="suggestions" id="suggestions"></ul>
        </div>
        <button type="submit">Locate</button>
      </form>
      <button id="geoBtn" title="Use my current location">Near me</button>
      <button id="unitToggle" type="button">&deg;C</button>
      <button id="aboutBtn" type="button" title="About this project">About</button>
    </div>
  </div>

  <div class="panel hero-panel" id="currentPanel">
    <div id="status">Search a municipality or barangay in Eastern Visayas above &mdash; try "Palo" or "Tacloban" &mdash; or use "Near me".</div>
    <div class="hero-card" id="heroCard" style="display:none;">
      <div class="hero-top">
        <div class="hero-icon" id="heroIcon"></div>
        <div class="hero-main">
          <div class="hero-temp" id="heroTemp">&mdash;</div>
          <div class="hero-condition" id="heroCondition"></div>
          <div class="hero-datetime" id="heroDatetime"></div>
        </div>
      </div>
      <div class="hero-bottom">
        <div class="hero-location">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
          <span id="heroPlace">&mdash;</span>
        </div>
        <div class="hero-chips" id="heroChips"></div>
      </div>
    </div>
  </div>


  <div class="panel" id="hourlyPanel" style="display:none;">
    <h2>Next 24 hours</h2>
    <div class="barograph" id="barograph"></div>
  </div>

  <div class="panel" id="dailyPanel" style="display:none;">
    <h2>Seven-day outlook</h2>
    <table class="ledger">
      <thead>
        <tr><th>Day</th><th>Sky</th><th>High / Low</th><th>Rain</th><th>Wind</th></tr>
      </thead>
      <tbody id="ledgerBody"></tbody>
    </table>
  </div>

  <div class="panel" id="advisoryPanel" style="display:none;">
    <h2>Today's advisories</h2>
    <div class="advisory-grid" id="advisoryGrid"></div>
  </div>

  <footer>
    Data from <a href="https://open-meteo.com" target="_blank" rel="noopener">Open-Meteo</a>.
    Scoped to Eastern Visayas &mdash; barangay coverage depends on the underlying place index, so very small barangays may not appear; try the nearest municipality if yours isn't found.
  </footer>
</div>

<div class="about-overlay" id="aboutOverlay">
  <div class="about-modal">
    <button class="about-close" id="aboutClose" type="button" aria-label="Close">&times;</button>

    <div class="about-modal-scroll">
      <div class="about-section">
        <h3>About This Project</h3>
        <p>AVED AURALIS is a values-education weather app for Eastern Visayas. It pulls live forecasts and turns them into simple, everyday decisions &mdash; whether to hang the laundry, wash the car, walk the dog, or pack an umbrella &mdash; so that small daily choices can be made with prudence and care for others. It reflects the university's guiding values of Integrity, Excellence, and Service by grounding practical decisions in reliable, honestly-presented information.</p>
      </div>

      <div class="about-section about-dev-card">
        <div class="about-dev">
          <div class="about-avatar" id="aboutAvatar">LA</div>
          <div>
            <div class="about-dev-label">Developed by</div>
            <div class="about-dev-name">Liezel A. Dumdum</div>
            <div class="about-dev-role">BSED Values Education Student</div>
          </div>
        </div>
      </div>

      <div class="about-section">
        <h3>Submitted To</h3>
        <p>This project was developed and submitted as a course requirement for VED 110 — Information Technology and Human Development, under the guidance of Ms. Karlvie Nadine Capisinio, College of Education, Leyte Normal University, Tacloban City.</p>
      </div>

      <div class="about-footer">
        Copyright &copy; 2026 Liezel A. Dumdum. All rights reserved. Weather data is drawn from the public Open-Meteo API; this project's design, code, and compilation are original work created for academic submission.
      </div>
      <div class="about-tagline">AVED AURALIS &middot; a student project for VED 110 — Information Technology and Human Development</div>
    </div>
  </div>
</div>

<script>
(function(){
  const state = {
    unit: 'C', // 'C' or 'F'
    lastData: null,
    lastPlace: null,
  };

  const els = {
    form: document.getElementById('searchForm'),
    input: document.getElementById('cityInput'),
    suggestions: document.getElementById('suggestions'),
    geoBtn: document.getElementById('geoBtn'),
    unitToggle: document.getElementById('unitToggle'),
    status: document.getElementById('status'),
    heroCard: document.getElementById('heroCard'),
    heroIcon: document.getElementById('heroIcon'),
    heroTemp: document.getElementById('heroTemp'),
    heroCondition: document.getElementById('heroCondition'),
    heroDatetime: document.getElementById('heroDatetime'),
    heroPlace: document.getElementById('heroPlace'),
    heroChips: document.getElementById('heroChips'),
    hourlyPanel: document.getElementById('hourlyPanel'),
    barograph: document.getElementById('barograph'),
    dailyPanel: document.getElementById('dailyPanel'),
    ledgerBody: document.getElementById('ledgerBody'),
    advisoryPanel: document.getElementById('advisoryPanel'),
    advisoryGrid: document.getElementById('advisoryGrid'),
    aboutBtn: document.getElementById('aboutBtn'),
    aboutOverlay: document.getElementById('aboutOverlay'),
    aboutClose: document.getElementById('aboutClose'),
  };

  // ---- weather code -> label + icon ----
  function weatherInfo(code){
    const map = {
      0:  ['Clear sky', 'sun'],
      1:  ['Mostly clear', 'sun-cloud'],
      2:  ['Partly cloudy', 'sun-cloud'],
      3:  ['Overcast', 'cloud'],
      45: ['Fog', 'fog'],
      48: ['Fog', 'fog'],
      51: ['Light drizzle', 'drizzle'],
      53: ['Drizzle', 'drizzle'],
      55: ['Dense drizzle', 'drizzle'],
      56: ['Freezing drizzle', 'drizzle'],
      57: ['Freezing drizzle', 'drizzle'],
      61: ['Light rain', 'rain'],
      63: ['Rain', 'rain'],
      65: ['Heavy rain', 'rain'],
      66: ['Freezing rain', 'rain'],
      67: ['Freezing rain', 'rain'],
      71: ['Light snow', 'snow'],
      73: ['Snow', 'snow'],
      75: ['Heavy snow', 'snow'],
      77: ['Snow grains', 'snow'],
      80: ['Rain showers', 'rain'],
      81: ['Rain showers', 'rain'],
      82: ['Violent showers', 'rain'],
      85: ['Snow showers', 'snow'],
      86: ['Snow showers', 'snow'],
      95: ['Thunderstorm', 'storm'],
      96: ['Thunderstorm, hail', 'storm'],
      99: ['Thunderstorm, hail', 'storm'],
    };
    return map[code] || ['Unknown', 'cloud'];
  }

  function icon(kind, color){
    color = color || 'currentColor';
    const s = 'stroke="' + color + '" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"';
    switch(kind){
      case 'sun':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" ${s}/><g ${s}><line x1="12" y1="1.5" x2="12" y2="4.5"/><line x1="12" y1="19.5" x2="12" y2="22.5"/><line x1="1.5" y1="12" x2="4.5" y2="12"/><line x1="19.5" y1="12" x2="22.5" y2="12"/><line x1="4.4" y1="4.4" x2="6.5" y2="6.5"/><line x1="17.5" y1="17.5" x2="19.6" y2="19.6"/><line x1="4.4" y1="19.6" x2="6.5" y2="17.5"/><line x1="17.5" y1="6.5" x2="19.6" y2="4.4"/></g></svg>`;
      case 'sun-cloud':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><circle cx="8" cy="9" r="4" ${s}/><path d="M4 18h13a3.5 3.5 0 0 0 0-7 5 5 0 0 0-9.5-1.6" ${s}/></svg>`;
      case 'cloud':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M6 18h12a3.5 3.5 0 0 0 0-7 5.5 5.5 0 0 0-10.6-1.5A4 4 0 0 0 6 18Z" ${s}/></svg>`;
      case 'fog':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M6 11h12a3.5 3.5 0 0 0 0-7 5.5 5.5 0 0 0-10.6-1A4 4 0 0 0 6 11Z" ${s}/><line x1="3" y1="16" x2="21" y2="16" ${s}/><line x1="5" y1="20" x2="19" y2="20" ${s}/></svg>`;
      case 'drizzle':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M6 12h12a3.5 3.5 0 0 0 0-7 5.5 5.5 0 0 0-10.6-1A4 4 0 0 0 6 12Z" ${s}/><g stroke="${color==='currentColor'?'#3f6ea3':color}" stroke-width="1.6" stroke-linecap="round"><line x1="9" y1="17" x2="8" y2="20"/><line x1="13" y1="17" x2="12" y2="20"/><line x1="17" y1="17" x2="16" y2="20"/></g></svg>`;
      case 'rain':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M6 11h12a3.5 3.5 0 0 0 0-7 5.5 5.5 0 0 0-10.6-1A4 4 0 0 0 6 11Z" ${s}/><g stroke="${color==='currentColor'?'#3f6ea3':color}" stroke-width="1.8" stroke-linecap="round"><line x1="8" y1="16" x2="6.5" y2="21"/><line x1="12.5" y1="16" x2="11" y2="21"/><line x1="17" y1="16" x2="15.5" y2="21"/></g></svg>`;
      case 'snow':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M6 11h12a3.5 3.5 0 0 0 0-7 5.5 5.5 0 0 0-10.6-1A4 4 0 0 0 6 11Z" ${s}/><g stroke="${color==='currentColor'?'#4a5568':color}" stroke-width="1.6" stroke-linecap="round"><line x1="9" y1="16" x2="9" y2="21"/><line x1="6.7" y1="17.3" x2="11.3" y2="19.7"/><line x1="11.3" y1="17.3" x2="6.7" y2="19.7"/><line x1="17" y1="16" x2="17" y2="21"/><line x1="14.7" y1="17.3" x2="19.3" y2="19.7"/><line x1="19.3" y1="17.3" x2="14.7" y2="19.7"/></g></svg>`;
      case 'storm':
        return `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M6 11h11a3.5 3.5 0 0 0 0-7 5.5 5.5 0 0 0-10.6-1A4 4 0 0 0 6 11Z" ${s}/><path d="M13 15l-3 5h3l-2 4" stroke="${color==='currentColor'?'#d4af37':color}" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
      default:
        return `<svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" ${s}/></svg>`;
    }
  }

  // ---- unit conversion ----
  function toDisplayTemp(c){
    if (state.unit === 'F') return Math.round(c * 9/5 + 32);
    return Math.round(c);
  }
  function unitSuffix(){ return state.unit === 'F' ? '\u00b0F' : '\u00b0C'; }

  // ---- hero card (illustrated sky icon + gradient) ----
  function compassDir(deg){
    const dirs = ['N','NE','E','SE','S','SW','W','NW'];
    return dirs[Math.round(((deg % 360) + 360) % 360 / 45) % 8];
  }

  function heroKind(code, isDay){
    const base = weatherInfo(code)[1];
    if (!isDay){
      if (base === 'sun') return 'moon';
      if (base === 'sun-cloud') return 'cloud-moon';
    }
    return base;
  }

  function heroIconSVG(kind){
    const glow = {
      sun: '#ffd166', 'sun-cloud': '#e7c26a', moon: '#d6ddff', 'cloud-moon': '#c9d2f2',
      cloud: '#b9c4d6', fog: '#c7cedb', drizzle: '#8fb6d6', rain: '#7fa8c9',
      snow: '#dfe8f5', storm: '#e0a55c',
    }[kind] || '#cfd8ff';
    let inner;
    switch(kind){
      case 'sun':
        inner = `<circle cx="32" cy="32" r="18" fill="${glow}"/>`; break;
      case 'sun-cloud':
        inner = `<circle cx="26" cy="26" r="13" fill="${glow}"/><ellipse cx="36" cy="40" rx="15" ry="11" fill="#e9edf5"/>`; break;
      case 'moon':
        inner = `<path d="M40 12a19 19 0 1 0 9 34 15 15 0 0 1-9-34Z" fill="${glow}"/>`; break;
      case 'cloud-moon':
        inner = `<path d="M42 10a14 14 0 1 0 6 25 11 11 0 0 1-6-25Z" fill="${glow}"/><ellipse cx="24" cy="42" rx="13" ry="9" fill="#c9d2ea"/>`; break;
      case 'cloud': case 'fog':
        inner = `<ellipse cx="26" cy="36" rx="14" ry="11" fill="${glow}"/><ellipse cx="41" cy="32" rx="11" ry="9" fill="#e9edf5"/>`; break;
      case 'drizzle': case 'rain':
        inner = `<ellipse cx="26" cy="30" rx="14" ry="10" fill="${glow}"/><ellipse cx="40" cy="27" rx="10" ry="8" fill="#e9edf5"/>
          <g stroke="#bfe0ff" stroke-width="3" stroke-linecap="round"><line x1="24" y1="46" x2="21" y2="54"/><line x1="34" y1="46" x2="31" y2="54"/><line x1="44" y1="46" x2="41" y2="54"/></g>`;
        break;
      case 'snow':
        inner = `<ellipse cx="26" cy="30" rx="14" ry="10" fill="${glow}"/><ellipse cx="40" cy="27" rx="10" ry="8" fill="#e9edf5"/>
          <g fill="#ffffff"><circle cx="24" cy="50" r="2.2"/><circle cx="34" cy="52" r="2.2"/><circle cx="44" cy="50" r="2.2"/></g>`;
        break;
      case 'storm':
        inner = `<ellipse cx="26" cy="28" rx="14" ry="10" fill="${glow}"/><ellipse cx="40" cy="25" rx="10" ry="8" fill="#e9edf5"/>
          <path d="M34 40l-7 12h7l-4 9" fill="none" stroke="#ffd166" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>`;
        break;
      default:
        inner = `<circle cx="32" cy="32" r="16" fill="${glow}"/>`;
    }
    return `<svg width="60" height="60" viewBox="0 0 64 64" style="filter: drop-shadow(0 0 14px ${glow}99);">${inner}</svg>`;
  }

  function heroGradient(kind, isDay){
    if (!isDay) return 'linear-gradient(160deg, #1b2245, #3a3f74)';
    const grads = {
      sun: 'linear-gradient(160deg, #ff9d5c, #ffc773)',
      'sun-cloud': 'linear-gradient(160deg, #6f9bd1, #a7c6e8)',
      cloud: 'linear-gradient(160deg, #7c8aa0, #a9b4c4)',
      fog: 'linear-gradient(160deg, #8791a3, #b7bfcc)',
      drizzle: 'linear-gradient(160deg, #4c6c86, #7ba0bd)',
      rain: 'linear-gradient(160deg, #3c5a73, #6c8fa8)',
      snow: 'linear-gradient(160deg, #7c93ad, #c7d6e6)',
      storm: 'linear-gradient(160deg, #2c2f4a, #55507a)',
    };
    return grads[kind] || 'linear-gradient(160deg, #5b6b8c, #8fa2c0)';
  }

  // ---- advisories (IF/ELSE decision logic) ----
  function fmtHour(h){
    const period = h < 12 ? 'AM' : 'PM';
    let hh = h % 12; if (hh === 0) hh = 12;
    return hh + period;
  }

  function groupHours(hours){
    if (!hours.length) return null;
    const ranges = [];
    let start = hours[0], prev = hours[0];
    for (let i = 1; i <= hours.length; i++){
      const h = hours[i];
      if (h === prev + 1){ prev = h; continue; }
      ranges.push(start === prev ? fmtHour(start) : `${fmtHour(start)}\u2013${fmtHour(prev)}`);
      if (h === undefined) break;
      start = h; prev = h;
    }
    return ranges.join(', ');
  }

  function computeOneWord(tempC, code, windKmh){
    const kind = weatherInfo(code)[1];
    if (kind === 'storm') return 'Stormy';
    if (kind === 'rain') return tempC >= 27 ? 'Muggy' : 'Dismal';
    if (kind === 'drizzle') return 'Dreary';
    if (kind === 'snow') return 'Frigid';
    if (kind === 'fog') return 'Hazy';
    if (tempC >= 33) return 'Scorching';
    if (tempC >= 29) return 'Sultry';
    if (tempC >= 25) return windKmh >= 20 ? 'Breezy' : 'Balmy';
    if (tempC >= 20) return 'Mild';
    return 'Crisp';
  }

  function computePacking(daily){
    const items = [];
    const maxPrecip = Math.max(...daily.precipitation_probability_max.slice(0, 7));
    const maxTemp = Math.max(...daily.temperature_2m_max.slice(0, 7));
    const minTemp = Math.min(...daily.temperature_2m_min.slice(0, 7));
    const maxWind = Math.max(...daily.windspeed_10m_max.slice(0, 7));
    if (maxPrecip >= 40) items.push('Umbrella');
    if (maxTemp >= 30) items.push('Sunscreen');
    if (minTemp <= 23) items.push('Light jacket');
    if (maxWind >= 35) items.push('Windbreaker');
    if (maxPrecip < 20 && maxTemp >= 30) items.push('Sunglasses');
    if (!items.length) items.push('Nothing extra needed');
    return items;
  }

  function computeLaundry(hourly, startIdx){
    const precip = hourly.precipitation_probability.slice(startIdx, startIdx + 4);
    const humidity = hourly.relative_humidity_2m.slice(startIdx, startIdx + 4);
    const wind = hourly.windspeed_10m.slice(startIdx, startIdx + 4);
    if (!precip.length) return { verdict: 'Unknown', tone: 'neutral', detail: 'Not enough hourly data' };
    const maxPrecip = Math.max(...precip);
    const avgHumidity = Math.round(humidity.reduce((a, b) => a + b, 0) / humidity.length);
    const avgWind = wind.reduce((a, b) => a + b, 0) / wind.length;
    if (maxPrecip >= 30){
      return { verdict: 'No', tone: 'bad', detail: `Rain chance hits ${maxPrecip}% in the next 4 hours` };
    }
    if (avgHumidity >= 85){
      return { verdict: 'No', tone: 'bad', detail: `Humidity is averaging ${avgHumidity}% over the next 4h \u2014 it won't dry` };
    }
    if (avgHumidity >= 75 && avgWind < 8){
      return { verdict: 'Maybe', tone: 'warn', detail: `${avgHumidity}% avg humidity (next 4h) and little wind \u2014 drying will be slow` };
    }
    return { verdict: 'Yes', tone: 'good', detail: `Low rain risk, ${avgHumidity}% avg humidity (next 4h)` };
  }

  function computeCarWash(daily){
    const next3 = daily.precipitation_probability_max.slice(0, 3);
    const maxP = Math.max(...next3);
    if (maxP >= 50) return { verdict: 'Skip it', tone: 'bad', detail: `Rain likely (${maxP}%) within 72 hours` };
    if (maxP >= 25) return { verdict: 'Your call', tone: 'warn', detail: `${maxP}% rain chance within 72 hours` };
    return { verdict: 'Go for it', tone: 'good', detail: 'Dry stretch for the next 72 hours' };
  }

  function computePetWalk(hourly, startIdx){
    const goodHours = [];
    let hotHours = 0;
    const limit = Math.min(startIdx + 24, hourly.time.length);
    for (let i = startIdx; i < limit; i++){
      const t = hourly.temperature_2m[i];
      const p = hourly.precipitation_probability[i];
      const hr = new Date(hourly.time[i]).getHours();
      if (t >= 32) hotHours++;
      if (t >= 21 && t <= 30 && p < 30) goodHours.push(hr);
    }
    const windowStr = groupHours(goodHours);
    return {
      verdict: windowStr || 'Stick to early morning',
      tone: windowStr ? 'good' : 'warn',
      detail: hotHours > 0
        ? `Pavement may be too hot for paws around midday (${hotHours}h above 32\u00b0C)`
        : 'No pavement-heat concerns expected today',
    };
  }

  function computeCoffee(tempC){
    if (tempC <= 22) return { drink: 'Hot latte', detail: `${Math.round(tempC)}\u00b0C \u2014 something warm hits the spot` };
    if (tempC < 29) return { drink: 'Hot brew or iced coffee', detail: `${Math.round(tempC)}\u00b0C \u2014 take your pick` };
    return { drink: 'Iced Americano', detail: `${Math.round(tempC)}\u00b0C \u2014 time to cool off` };
  }

  function advisoryIcon(kind){
    const s = 'stroke="#d4af37" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"';
    switch(kind){
      case 'word':
        return `<svg width="18" height="18" viewBox="0 0 24 24"><path d="M4 5h16v11H9l-4 4v-4H4Z" ${s}/><line x1="7.5" y1="9" x2="16.5" y2="9" ${s}/><line x1="7.5" y1="12.5" x2="13" y2="12.5" ${s}/></svg>`;
      case 'laundry':
        return `<svg width="18" height="18" viewBox="0 0 24 24"><path d="M12 4c-1 1.6-2.5 2-2.5 3.5A2.5 2.5 0 0 0 12 10a2.5 2.5 0 0 1 2.5 2.5" ${s}/><path d="M2 12l10-6 10 6" ${s}/><path d="M4 12v7h16v-7" ${s}/><line x1="2" y1="19" x2="22" y2="19" ${s}/></svg>`;
      case 'car':
        return `<svg width="18" height="18" viewBox="0 0 24 24"><path d="M4 16v-3.5L6 8h12l2 4.5V16" ${s}/><path d="M4 16h16" ${s}/><circle cx="7.5" cy="17.5" r="1.4" ${s}/><circle cx="16.5" cy="17.5" r="1.4" ${s}/><line x1="3" y1="6" x2="5.5" y2="8" ${s}/><line x1="21" y1="6" x2="18.5" y2="8" ${s}/></svg>`;
      case 'coffee':
        return `<svg width="18" height="18" viewBox="0 0 24 24"><path d="M5 9h11v6a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4Z" ${s}/><path d="M16 10.5h1.5a2.25 2.25 0 0 1 0 4.5H16" ${s}/><path d="M8 4c-.6.9-1.4 1.1-1.4 2a1.4 1.4 0 0 0 1.4 1.4" ${s}/><path d="M12 4c-.6.9-1.4 1.1-1.4 2a1.4 1.4 0 0 0 1.4 1.4" ${s}/></svg>`;
      case 'paw':
        return `<svg width="18" height="18" viewBox="0 0 24 24"><ellipse cx="12" cy="16" rx="4.5" ry="3.6" ${s}/><circle cx="6" cy="10" r="1.8" ${s}/><circle cx="10" cy="6.5" r="1.8" ${s}/><circle cx="14" cy="6.5" r="1.8" ${s}/><circle cx="18" cy="10" r="1.8" ${s}/></svg>`;
      case 'bag':
        return `<svg width="18" height="18" viewBox="0 0 24 24"><rect x="4" y="8" width="16" height="12" rx="1.5" ${s}/><path d="M9 8V6a3 3 0 0 1 6 0v2" ${s}/><line x1="4" y1="13" x2="20" y2="13" ${s}/></svg>`;
      default:
        return '';
    }
  }

  function renderAdvisories(data, curTempC, curCode, curWindKmh, startIdx){
    const word = computeOneWord(curTempC, curCode, curWindKmh);
    const packing = computePacking(data.daily);
    const laundry = computeLaundry(data.hourly, startIdx);
    const carWash = computeCarWash(data.daily);
    const petWalk = computePetWalk(data.hourly, startIdx);
    const coffee = computeCoffee(curTempC);

    els.advisoryGrid.innerHTML = `
      <div class="advisory-card">
        <div class="advisory-label">${advisoryIcon('word')}<span>One-word weather</span></div>
        <div class="advisory-word">${word}</div>
      </div>
      <div class="advisory-card">
        <div class="advisory-label">${advisoryIcon('laundry')}<span>Can I hang my laundry?</span></div>
        <div class="advisory-verdict ${laundry.tone}">${laundry.verdict}</div>
        <div class="advisory-detail">${laundry.detail}</div>
      </div>
      <div class="advisory-card">
        <div class="advisory-label">${advisoryIcon('car')}<span>Car wash advisor</span></div>
        <div class="advisory-verdict ${carWash.tone}">${carWash.verdict}</div>
        <div class="advisory-detail">${carWash.detail}</div>
      </div>
      <div class="advisory-card">
        <div class="advisory-label">${advisoryIcon('coffee')}<span>Coffee choice predictor</span></div>
        <div class="advisory-verdict neutral">${coffee.drink}</div>
        <div class="advisory-detail">${coffee.detail}</div>
      </div>
      <div class="advisory-card">
        <div class="advisory-label">${advisoryIcon('paw')}<span>Pet walking planner</span></div>
        <div class="advisory-verdict ${petWalk.tone}">${petWalk.verdict}</div>
        <div class="advisory-detail">${petWalk.detail}</div>
      </div>
      <div class="advisory-card">
        <div class="advisory-label">${advisoryIcon('bag')}<span>Packing list (7 days)</span></div>
        <div class="chip-list">${packing.map(i => `<span class="chip">${i}</span>`).join('')}</div>
      </div>
    `;
    els.advisoryPanel.style.display = 'block';
  }

  // ---- rendering ----
  function setStatus(msg, isError){
    els.status.textContent = msg;
    els.status.className = isError ? 'error' : '';
    els.status.style.display = msg ? 'block' : 'none';
  }

  function render(){
    const data = state.lastData;
    const place = state.lastPlace;
    if (!data) return;

    setStatus('');
    els.heroCard.style.display = 'flex';
    els.hourlyPanel.style.display = 'block';
    els.dailyPanel.style.display = 'block';

    const cur = data.current_weather;
    const info = weatherInfo(cur.weathercode);
    const isDay = cur.is_day !== 0;
    const kind = heroKind(cur.weathercode, isDay);

    els.heroCard.style.background = heroGradient(kind, isDay);
    els.heroIcon.innerHTML = heroIconSVG(kind);
    els.heroTemp.textContent = toDisplayTemp(cur.temperature) + unitSuffix();
    els.heroCondition.textContent = info[0];
    els.heroPlace.textContent = place.name + ' \u00b7 ' + subtitleFor(place);

    const now = new Date(cur.time);
    const dayName = new Intl.DateTimeFormat(undefined, { weekday: 'long' }).format(now).toUpperCase();
    const dateStr = new Intl.DateTimeFormat(undefined, { month: 'short', day: 'numeric' }).format(now).toUpperCase();
    const timeStr = new Intl.DateTimeFormat(undefined, { hour: '2-digit', minute: '2-digit', hour12: false }).format(now);
    els.heroDatetime.textContent = `${dayName}  |  ${dateStr}  |  ${timeStr}`;

    // feels-like + humidity: match current hour in hourly arrays
    const hIdx = data.hourly.time.indexOf(cur.time);
    let feelsLike = null, humidity = null;
    if (hIdx !== -1){
      feelsLike = data.hourly.apparent_temperature[hIdx];
      humidity = data.hourly.relative_humidity_2m[hIdx];
    }
    const chips = [];
    if (humidity !== null) chips.push(`${Math.round(humidity)}% humidity now`);
    chips.push(`${compassDir(cur.winddirection)} wind ${Math.round(cur.windspeed)}`);
    if (feelsLike !== null) chips.push(`Feels ${toDisplayTemp(feelsLike)}${unitSuffix()}`);
    els.heroChips.innerHTML = chips.map(c => `<span class="hero-chip">${c}</span>`).join('');

    // hourly barograph: next 24 hours from current index
    const startIdx = hIdx !== -1 ? hIdx : 0;
    const hTemps = data.hourly.temperature_2m.slice(startIdx, startIdx+24);
    const hPrecip = data.hourly.precipitation_probability.slice(startIdx, startIdx+24);
    const hTime = data.hourly.time.slice(startIdx, startIdx+24);
    const tMin = Math.min(...hTemps), tMax = Math.max(...hTemps);
    const range = Math.max(1, tMax - tMin);
    els.barograph.innerHTML = hTemps.map((t, i) => {
      const heightPct = 20 + ((t - tMin)/range) * 70;
      const wet = hPrecip[i] >= 40;
      const hour = new Date(hTime[i]);
      const label = i % 3 === 0 ? hour.getHours() + 'h' : '';
      return `<div class="bar-col">
        <div class="bar-temp">${toDisplayTemp(t)}&deg;</div>
        <div class="bar-stem ${wet ? 'wet' : ''}" style="height:${heightPct}%"></div>
        <div class="bar-hour">${label}</div>
      </div>`;
    }).join('');

    // daily ledger
    const d = data.daily;
    const dayFmt = new Intl.DateTimeFormat(undefined, { weekday: 'short' });
    const badgeColorFor = (kind) => ({
      sun: '#ffe3ad', 'sun-cloud': '#dce8f5', cloud: '#e3e7ee', fog: '#e6e9ef',
      drizzle: '#dcebf7', rain: '#d3e6f5', snow: '#eaf1fa', storm: '#ded9ee',
    }[kind] || '#e9edf5');
    els.ledgerBody.innerHTML = d.time.map((date, i) => {
      const info = weatherInfo(d.weathercode[i]);
      const dayLabel = i === 0 ? 'Today' : dayFmt.format(new Date(date));
      return `<tr>
        <td>${dayLabel}</td>
        <td><div class="day-icon"><span class="day-icon-badge" style="background:${badgeColorFor(info[1])}">${icon(info[1])}</span><span>${info[0]}</span></div></td>
        <td><span class="hi">${toDisplayTemp(d.temperature_2m_max[i])}&deg;</span> / <span class="lo">${toDisplayTemp(d.temperature_2m_min[i])}&deg;</span></td>
        <td class="precip">${d.precipitation_probability_max[i]}%</td>
        <td>${Math.round(d.windspeed_10m_max[i])} km/h</td>
      </tr>`;
    }).join('');

    renderAdvisories(data, cur.temperature, cur.weathercode, cur.windspeed, startIdx);
  }

  // ---- fetching ----
  async function fetchForecast(lat, lon, place){
    setStatus('Checking the sky\u2026');
    els.heroCard.style.display = 'none';
    els.hourlyPanel.style.display = 'none';
    els.dailyPanel.style.display = 'none';
    els.advisoryPanel.style.display = 'none';
    try {
      const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
        `&current_weather=true` +
        `&hourly=temperature_2m,apparent_temperature,precipitation_probability,weathercode,relative_humidity_2m,windspeed_10m` +
        `&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_probability_max,windspeed_10m_max` +
        `&timezone=auto`;
      const res = await fetch(url);
      if (!res.ok) throw new Error('Forecast request failed');
      const data = await res.json();
      state.lastData = data;
      state.lastPlace = place;
      render();
    } catch (err) {
      console.error(err);
      setStatus('Could not read the forecast. Check your connection and try again.', true);
    }
  }

  const EASTERN_VISAYAS = 'eastern visayas';

  function subtitleFor(r){
    const parts = [r.admin4, r.admin3, r.admin2].filter(p => p && p !== r.name);
    return parts.slice(0, 2).join(', ') || 'Eastern Visayas';
  }

  async function geocode(name, maxResults){
    // Fetch a broad set nationally, then narrow to Eastern Visayas client-side,
    // since the geocoding index has no direct region filter.
    const url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(name)}` +
      `&count=20&language=en&format=json&countryCode=PH`;
    const res = await fetch(url);
    if (!res.ok) throw new Error('Geocoding request failed');
    const data = await res.json();
    const filtered = (data.results || []).filter(r =>
      r.country_code === 'PH' && r.admin1 && r.admin1.toLowerCase().includes(EASTERN_VISAYAS)
    );
    return filtered.slice(0, maxResults);
  }

  async function searchCity(name){
    setStatus('Locating ' + name + '\u2026');
    try {
      const results = await geocode(name, 1);
      if (!results.length){
        setStatus(`No municipality or barangay named "${name}" found in Eastern Visayas. Try a different spelling.`, true);
        return;
      }
      fetchForecast(results[0].latitude, results[0].longitude, results[0]);
    } catch (err) {
      console.error(err);
      setStatus('Could not reach the location index. Try again shortly.', true);
    }
  }

  // ---- autocomplete ----
  let debounceTimer = null;
  let activeSuggestions = [];

  function closeSuggestions(){
    els.suggestions.classList.remove('open');
    els.suggestions.innerHTML = '';
    activeSuggestions = [];
  }

  function renderSuggestions(results){
    if (!results.length){
      els.suggestions.innerHTML = '<li class="empty">No matching municipality or barangay in Eastern Visayas</li>';
      els.suggestions.classList.add('open');
      return;
    }
    activeSuggestions = results;
    els.suggestions.innerHTML = results.map((r, i) => {
      const region = subtitleFor(r);
      return `<li data-idx="${i}"><span class="muni">${r.name}</span><span class="region">${region}</span></li>`;
    }).join('');
    els.suggestions.classList.add('open');
  }

  els.input.addEventListener('input', function(){
    const v = els.input.value.trim();
    clearTimeout(debounceTimer);
    if (v.length < 2){ closeSuggestions(); return; }
    debounceTimer = setTimeout(async function(){
      try {
        const results = await geocode(v, 8);
        renderSuggestions(results);
      } catch (err){
        console.error(err);
        closeSuggestions();
      }
    }, 300);
  });

  els.suggestions.addEventListener('click', function(e){
    const li = e.target.closest('li[data-idx]');
    if (!li) return;
    const place = activeSuggestions[Number(li.dataset.idx)];
    if (!place) return;
    els.input.value = place.name;
    closeSuggestions();
    fetchForecast(place.latitude, place.longitude, place);
  });

  document.addEventListener('click', function(e){
    if (!e.target.closest('.search-field')) closeSuggestions();
  });

  async function reverseName(lat, lon){
    try {
      const url = `https://geocoding-api.open-meteo.com/v1/search?name=&count=1`;
      // Open-Meteo geocoding has no reverse endpoint; label generically.
      return { name: 'Your location', admin1: '', country: '' };
    } catch(e){
      return { name: 'Your location', admin1: '', country: '' };
    }
  }

  // ---- events ----
  els.form.addEventListener('submit', function(e){
    e.preventDefault();
    closeSuggestions();
    const v = els.input.value.trim();
    if (v) searchCity(v);
  });

  // approximate bounding box for the Eastern Visayas region (Leyte, Samar
  // provinces and Biliran)
  function withinEasternVisayas(lat, lon){
    return lat >= 9.3 && lat <= 12.8 && lon >= 123.9 && lon <= 126.3;
  }

  function useMyLocation(isAutoLoad){
    if (!navigator.geolocation){
      if (!isAutoLoad) setStatus('Geolocation is not available in this browser.', true);
      return;
    }
    setStatus(isAutoLoad ? 'Finding your location\u2026' : 'Requesting your location\u2026');
    navigator.geolocation.getCurrentPosition(async function(pos){
      const { latitude, longitude } = pos.coords;
      if (!withinEasternVisayas(latitude, longitude)){
        setStatus('Your location looks like it\u2019s outside Eastern Visayas. This station only reads Eastern Visayas municipalities and barangays \u2014 try searching one instead.', true);
        return;
      }
      const place = await reverseName(latitude, longitude);
      fetchForecast(latitude, longitude, place);
    }, function(){
      // On auto-load, a denied/unavailable prompt should fail quietly back to the default empty state.
      setStatus(isAutoLoad
        ? 'Search a municipality or barangay in Eastern Visayas above \u2014 try "Palo" or "Tacloban" \u2014 or use "Near me".'
        : 'Location permission was denied. Try searching a municipality or barangay instead.', !isAutoLoad);
    });
  }

  els.geoBtn.addEventListener('click', function(){ useMyLocation(false); });

  // auto-fetch the visitor's location on first load
  useMyLocation(true);

  els.unitToggle.addEventListener('click', function(){
    state.unit = state.unit === 'C' ? 'F' : 'C';
    els.unitToggle.textContent = state.unit === 'C' ? '\u00b0C' : '\u00b0F';
    render();
  });

  // ---- about modal ----
  els.aboutBtn.addEventListener('click', function(){
    els.aboutOverlay.classList.add('open');
  });
  els.aboutClose.addEventListener('click', function(){
    els.aboutOverlay.classList.remove('open');
  });
  els.aboutOverlay.addEventListener('click', function(e){
    if (e.target === els.aboutOverlay) els.aboutOverlay.classList.remove('open');
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') els.aboutOverlay.classList.remove('open');
  });

})();
</script>
</body>
</html>