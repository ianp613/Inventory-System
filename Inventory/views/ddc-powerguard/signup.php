<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DDC PowerGuard — Register</title>
<link rel="stylesheet" href="../../assets/css/powerguard.css">

</head>
<body class="pg-signup-body">

<div class="pg-signup-shell">

  <!-- LEFT: signature panel -->
  <div class="pg-signup-brand">
    <div class="pg-signup-mark">
      <div class="pg-bolt">
        <svg viewBox="0 0 24 24" fill="none" stroke="#1f2421" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/>
        </svg>
      </div>
      <div>
        <div class="pg-signup-name">DDC POWERGUARD</div>
        <div class="pg-signup-sub">incident management</div>
      </div>
    </div>

    <div class="pg-signup-content">
      <div class="pg-signup-eyebrow">Supervisor</div>
      <div class="pg-signup-headline">Register your account</div>
      <div class="pg-signup-desc">
        Set up access to file incident tickets, follow technician work, sign off on repairs, and pull reports for your department.
      </div>

      <div class="pg-signup-steps" id="pgSignupStepsNav">
        <div class="pg-signup-step pg-active" data-step="1">
          <div class="pg-signup-step-marker">1</div>
          <div class="pg-signup-step-label">Personal information</div>
        </div>
        <div class="pg-signup-step" data-step="2">
          <div class="pg-signup-step-marker">2</div>
          <div class="pg-signup-step-label">Account credentials</div>
        </div>
        <div class="pg-signup-step" data-step="3">
          <div class="pg-signup-step-marker">3</div>
          <div class="pg-signup-step-label">Review &amp; submit</div>
        </div>
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
  <div class="pg-signup-formpanel">

    <!-- STEP 1: Personal info -->
    <div class="pg-signup-pane pg-pane-active" id="pgPaneInfo">
      <div class="pg-signup-progress">
        <div class="pg-signup-progress-bar"><div class="pg-signup-progress-fill" style="width:33%"></div></div>
        <div class="pg-signup-progress-text">Step 1 of 3</div>
      </div>

      <div class="pg-signup-header">
        <div class="pg-signup-title">Personal information</div>
        <div class="pg-signup-subtitle">This form is for supervisors and operations managers — both use the same account type.</div>
      </div>

      <form class="pg-signup-form" id="pgFormInfo" novalidate>
        <div class="pg-signup-row">
          <div class="pg-signup-field">
            <label for="pgFname">First name</label>
            <div class="pg-signup-inputwrap">
              <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" id="pgFname" placeholder="e.g. Ramon">
            </div>
            <span class="pg-signup-error" id="pgFnameError">Enter your first name.</span>
          </div>
          <div class="pg-signup-field">
            <label for="pgLname">Last name</label>
            <div class="pg-signup-inputwrap">
              <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" id="pgLname" placeholder="e.g. Villanueva">
            </div>
            <span class="pg-signup-error" id="pgLnameError">Enter your last name.</span>
          </div>
        </div>

        <div class="pg-signup-field">
          <label for="pgJobtitle">Job title</label>
          <div class="pg-signup-inputwrap">
            <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-3V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v2H4a1 1 0 0 0-1 1v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1z"/><path d="M3 13v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5"/></svg>
            <input type="text" id="pgJobtitle" placeholder="e.g. Supervisor, Operations Manager">
          </div>
          <span class="pg-signup-hint">Enter your title exactly as it appears on your company record.</span>
          <span class="pg-signup-error" id="pgJobtitleError">Enter your job title.</span>
        </div>

        <div class="pg-signup-field">
          <label for="pgEmail">Work email address</label>
          <div class="pg-signup-inputwrap">
            <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" style="display:none"/><path d="M22 6 12 13 2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
            <input type="email" id="pgEmail" placeholder="you@company.com">
          </div>
          <span class="pg-signup-error" id="pgEmailError">Enter a valid work email address.</span>
        </div>

        <div class="pg-signup-row">
          <div class="pg-signup-field">
            <label for="pgPhone">Mobile number</label>
            <div class="pg-signup-inputwrap">
              <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.36 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <input type="tel" id="pgPhone" placeholder="+63 9XX XXX XXXX">
            </div>
            <span class="pg-signup-error" id="pgPhoneError">Enter your mobile number.</span>
          </div>
          <div class="pg-signup-field">
            <label for="pgEmpid">Employee ID</label>
            <div class="pg-signup-inputwrap">
              <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M15 8h3M15 12h3M7 16h10"/></svg>
              <input type="text" id="pgEmpid" placeholder="e.g. EMP-2024-0042">
            </div>
            <span class="pg-signup-error" id="pgEmpidError">Enter your employee ID.</span>
          </div>
        </div>

        <div class="pg-signup-actions">
          <button type="submit" class="pg-signup-btn pg-signup-btn-primary">
            Next
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
          </button>
        </div>
      </form>

      <div class="pg-signup-loginlink">Already have an account? <a href="signin.php">Sign in</a></div>
    </div>

    <!-- STEP 2: Account credentials -->
    <div class="pg-signup-pane" id="pgPaneAccount">
      <div class="pg-signup-progress">
        <div class="pg-signup-progress-bar"><div class="pg-signup-progress-fill" style="width:66%"></div></div>
        <div class="pg-signup-progress-text">Step 2 of 3</div>
      </div>

      <div class="pg-signup-header">
        <div class="pg-signup-title">Account credentials</div>
        <div class="pg-signup-subtitle">Choose a username and password to sign in once your account is approved.</div>
      </div>

      <form class="pg-signup-form" id="pgFormAccount" novalidate>
        <div class="pg-signup-field">
          <label for="pgUname">Username</label>
          <div class="pg-signup-inputwrap">
            <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="pgUname" placeholder="e.g. r.villanueva">
          </div>
          <span class="pg-signup-hint">Lowercase letters and dots only, no spaces.</span>
          <span class="pg-signup-error" id="pgUnameError">Enter a username.</span>
        </div>

        <div class="pg-signup-field">
          <label for="pgPw">Password</label>
          <div class="pg-signup-inputwrap">
            <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="pgPw" placeholder="Create a strong password">
            <button type="button" class="pg-signup-toggle-pw" id="pgTogglePw1" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="pg-signup-pwbar"><div class="pg-signup-pwfill" id="pgPwFill"></div></div>
          <span class="pg-signup-pwlabel" id="pgPwLabel">At least 8 characters, one number, one symbol</span>
          <span class="pg-signup-error" id="pgPwError">Password must be at least 8 characters.</span>
        </div>

        <div class="pg-signup-field">
          <label for="pgPw2">Confirm password</label>
          <div class="pg-signup-inputwrap">
            <svg class="pg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="pgPw2" placeholder="Re-enter your password">
            <button type="button" class="pg-signup-toggle-pw" id="pgTogglePw2" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <span class="pg-signup-error" id="pgPw2Error">Passwords do not match.</span>
        </div>

        <div class="pg-signup-terms">
          <input type="checkbox" id="pgTerms">
          <label for="pgTerms">I agree to the <a href="#">terms of use</a> and understand that my account is subject to administrator approval before activation.</label>
        </div>
        <span class="pg-signup-error" id="pgTermsError">You must agree to the terms of use to continue.</span>

        <div class="pg-signup-actions">
          <button type="button" class="pg-signup-btn pg-signup-btn-ghost" id="pgBackToInfo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            Back
          </button>
          <button type="submit" class="pg-signup-btn pg-signup-btn-primary">
            Next
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
          </button>
        </div>
      </form>
    </div>

    <!-- STEP 3: Review -->
    <div class="pg-signup-pane" id="pgPaneReview">
      <div class="pg-signup-progress">
        <div class="pg-signup-progress-bar"><div class="pg-signup-progress-fill" style="width:100%"></div></div>
        <div class="pg-signup-progress-text">Step 3 of 3</div>
      </div>

      <div class="pg-signup-header">
        <div class="pg-signup-title">Review your details</div>
        <div class="pg-signup-subtitle">Check everything below before sending your registration for approval.</div>
      </div>

      <div class="pg-signup-review-block">
        <div class="pg-signup-review-heading">Personal information</div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Full name</div><div class="pg-signup-review-value" id="pgRvName">—</div></div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Job title</div><div class="pg-signup-review-value" id="pgRvJobtitle">—</div></div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Work email</div><div class="pg-signup-review-value" id="pgRvEmail">—</div></div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Mobile</div><div class="pg-signup-review-value" id="pgRvPhone">—</div></div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Employee ID</div><div class="pg-signup-review-value" id="pgRvEmpid">—</div></div>
      </div>

      <div class="pg-signup-review-block">
        <div class="pg-signup-review-heading">Account</div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Username</div><div class="pg-signup-review-value" id="pgRvUname">—</div></div>
        <div class="pg-signup-review-row"><div class="pg-signup-review-label">Password</div><div class="pg-signup-review-value">••••••••</div></div>
      </div>

      <div class="pg-signup-banner">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>After submitting, your account will be pending administrator approval. You'll be notified by email once it's activated.</span>
      </div>

      <div class="pg-signup-actions" style="margin-top:14px">
        <button type="button" class="pg-signup-btn pg-signup-btn-ghost" id="pgBackToAccount">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
          Back
        </button>
        <button type="button" class="pg-signup-btn pg-signup-btn-primary" id="pgSubmitAll">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
          Submit registration
        </button>
      </div>
    </div>

    <!-- SUCCESS -->
    <div class="pg-signup-pane" id="pgPaneSuccess">
      <div class="pg-signup-success">
        <div class="pg-signup-success-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <div class="pg-signup-success-title">Registration submitted</div>
        <div class="pg-signup-success-desc">
          Your request has been sent to the system administrator. You'll receive a confirmation at
          <span class="pg-signup-success-email" id="pgSuccessEmail"></span> once your account is activated.
        </div>

        <div class="pg-signup-nextsteps">
          <div class="pg-signup-nextsteps-title">What happens next</div>
          <div class="pg-signup-nextstep-row"><div class="pg-signup-nextstep-num">1</div><span>Admin verifies your employee ID and details</span></div>
          <div class="pg-signup-nextstep-row"><div class="pg-signup-nextstep-num">2</div><span>You receive an activation email (within 1 business day)</span></div>
          <div class="pg-signup-nextstep-row"><div class="pg-signup-nextstep-num">3</div><span>Log in and start submitting incident tickets</span></div>
        </div>

        <div class="pg-signup-actions">
          <button type="button" class="pg-signup-btn pg-signup-btn-primary" id="pgGoLogin2">
            Go to login
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="../../assets/js/sweetalert2/sweetalert2.all.min.js"></script>
<script src="../../assets/js/sole.js"></script>
<script src="../../assets/js/sole.swal.js"></script>
<script src="../../assets/js/powerguard/signup.js"></script>
</body>
</html>