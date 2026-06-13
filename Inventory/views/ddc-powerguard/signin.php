<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DDC PowerGuard — Sign in</title>
<link rel="stylesheet" href="../../assets/css/powerguard.css">

</head>
<body>

<div class="login-shell">

  <!-- LEFT: signature panel -->
  <div class="brand-panel">
    <div class="brand-mark">
      <div class="bolt">
        <svg viewBox="0 0 24 24" fill="none" stroke="#1f2421" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/>
        </svg>
      </div>
      <div>
        <div class="brand-name">DDC POWERGUARD</div>
        <div class="brand-sub">incident management</div>
      </div>
    </div>

    <div class="brand-content">
      <div class="brand-eyebrow">Supervisor / Technicians</div>
      <div class="brand-headline">Log a fluctuation. Track every workstation. Close the loop.</div>
      <div class="brand-desc">
        Sign in to file incident tickets, follow technician assessments, sign off on completed repairs, and pull reports for your department.
      </div>
    </div>

    <div class="status-strip">
      <div class="status-row">
        <span class="status-dot dot-warn"></span>
        <span>Copyright © 2026 - Wifi Team | Ian</span>
      </div>
      <div class="status-row">
        <span class="status-dot dot-ok"></span>
        <span>Vibe coded with <a href="https://claude.ai/" target="blank" style="color: orangered; font-weight: bolder;">Claude.AI</a></span>
      </div>
    </div>
  </div>

  <!-- RIGHT: form panel -->
  <div class="form-panel">
    <div class="form-header">
      <div class="form-title">Sign in</div>
      <div class="form-subtitle">Enter your credentials to access your incident tickets and reports.</div>
    </div>

    <div class="banner" id="errorBanner">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <span>Username or password is incorrect. Check your details and try again.</span>
    </div>

    <form id="loginForm" novalidate>
      <div class="field">
        <label for="username">Username or Employee ID</label>
        <div class="input-wrap">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
          <input type="text" id="username" name="username" placeholder="e.g. r.villanueva or 123A" autocomplete="username" required>
        </div>
        <span class="field-error" id="usernameError">Enter your username to continue.</span>
      </div>

      <div class="field">
        <div class="row-between" style="margin-bottom:-2px">
          <label for="password" style="margin-bottom:0">Password</label>
        </div>
        <div class="input-wrap">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
          <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <span class="field-error" id="passwordError">Enter your password to continue.</span>
      </div>

      <div class="row-between">
        <label class="remember">
          <input type="checkbox" id="remember">
          Remember this device
        </label>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>

      <button type="submit" class="submit-btn">
        Sign in
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
        </svg>
      </button>
    </form>

    <div class="divider-row">new here</div>

    <div class="signup-row">
      Don't have an account? <a href="signup.php" id="signupLink">Register as supervisor</a>
    </div>
  </div>

</div>
<script src="../../assets/js/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/js/sole.js"></script>
<script src="../../assets/js/sole.swal.js"></script>
<script src="../../assets/js/powerguard/signin.js"></script>
</body>
</html>
