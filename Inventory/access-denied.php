<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>403 — Forbidden</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Space+Mono&display=swap');

    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg:         #f0f4f7;
      --surface:    #ffffff;
      --border:     #d0dce8;
      --text:       #1a2e45;
      --muted:      #6b8299;
      --accent:     #3aa8a8;
      --accent-dim: rgba(58, 168, 168, 0.10);
      --navy:       #1a2e45;
    }

    body {
      background-color: var(--bg);
      color: var(--text);
      font-family: 'Space Grotesk', system-ui, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      max-width: 480px;
      width: 100%;
      padding: 3rem 2.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 32px rgba(26, 46, 69, 0.08);
    }

    /* Teal top bar */
    .card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--navy), var(--accent));
      border-radius: 16px 16px 0 0;
    }

    /* Subtle teal glow */
    .card::after {
      content: '';
      position: absolute;
      top: 0; left: 50%;
      transform: translateX(-50%);
      width: 200px;
      height: 130px;
      background: radial-gradient(ellipse at top, rgba(58,168,168,0.10) 0%, transparent 70%);
      pointer-events: none;
    }

    .code-badge {
      display: inline-block;
      font-family: 'Space Mono', monospace;
      font-size: 0.72rem;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--accent);
      background: var(--accent-dim);
      border: 1px solid rgba(58, 168, 168, 0.25);
      border-radius: 4px;
      padding: 4px 10px;
      margin-bottom: 1.75rem;
    }

    .icon {
      width: 64px;
      height: 64px;
      margin: 0 auto 1.5rem;
      color: var(--accent);
    }

    h1 {
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      line-height: 1.1;
      color: var(--navy);
      margin-bottom: 0.75rem;
    }

    .subtitle {
      font-size: 0.95rem;
      font-weight: 300;
      color: var(--muted);
      line-height: 1.6;
      margin-bottom: 2.25rem;
    }

    .divider {
      height: 1px;
      background: var(--border);
      margin-bottom: 2rem;
    }

    .meta {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .meta-item {
      display: flex;
      flex-direction: column;
      gap: 2px;
      text-align: left;
    }

    .meta-label {
      font-family: 'Space Mono', monospace;
      font-size: 0.65rem;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--muted);
    }

    .meta-value {
      font-size: 0.82rem;
      font-weight: 500;
      color: var(--navy);
    }

    .btn {
      display: inline-block;
      text-decoration: none;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.875rem;
      font-weight: 500;
      padding: 10px 24px;
      border-radius: 8px;
      cursor: pointer;
      border: none;
      transition: background 0.15s, color 0.15s, border-color 0.15s;
    }

    .btn-primary {
      background: var(--accent);
      color: #fff;
    }
    .btn-primary:hover {
      background: #2e9090;
    }

    .btn-ghost {
      background: transparent;
      color: var(--muted);
      border: 1px solid var(--border);
      margin-left: 0.75rem;
    }
    .btn-ghost:hover {
      color: var(--navy);
      border-color: var(--navy);
    }

    /* DDC dots decoration — mirrors the logo's three dots */
    .ddc-dots {
      display: flex;
      justify-content: center;
      gap: 6px;
      margin-top: 2.5rem;
    }
    .ddc-dots span {
      border-radius: 50%;
      background: var(--accent);
      opacity: 0.5;
    }
    .ddc-dots span:nth-child(1) { width: 6px; height: 6px; }
    .ddc-dots span:nth-child(2) { width: 8px; height: 8px; opacity: 0.7; }
    .ddc-dots span:nth-child(3) { width: 10px; height: 10px; opacity: 1; }

    @media (max-width: 400px) {
      .card { padding: 2rem 1.5rem; }
      h1 { font-size: 1.6rem; }
      .meta { gap: 1.2rem; }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="code-badge">HTTP 403</div>

    <!-- Lock icon -->
    <svg class="icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <rect x="12" y="28" width="40" height="28" rx="6" stroke="currentColor" stroke-width="3" fill="rgba(58,168,168,0.08)"/>
      <path d="M21 28v-8a11 11 0 0 1 22 0v8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
      <circle cx="32" cy="42" r="3.5" fill="currentColor"/>
      <line x1="32" y1="45.5" x2="32" y2="51" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
    </svg>

    <h1>Access Denied</h1>
    <p class="subtitle">You don't have permission to view this page. Contact your administrator if you think this is a mistake.</p>

    <div class="divider"></div>

    <div class="meta">
      <div class="meta-item">
        <span class="meta-label">Status</span>
        <span class="meta-value">403 Forbidden</span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Time</span>
        <span class="meta-value" id="ts">—</span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Request ID</span>
        <span class="meta-value" id="rid">—</span>
      </div>
    </div>

    <a href="/" class="btn btn-primary">Go Home</a>
    <a href="mailto:freewifi.tech@datacapture2.com.ph" class="btn btn-ghost">Contact Support</a>

    <!-- Three dots echoing the DDC logo -->
    <div class="ddc-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

  <script>
    document.getElementById('ts').textContent =
      new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    document.getElementById('rid').textContent =
      Math.random().toString(36).slice(2, 10).toUpperCase();
  </script>
</body>
</html>