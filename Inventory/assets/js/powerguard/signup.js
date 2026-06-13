(function(){
  const panes = {
    info: document.getElementById('pgPaneInfo'),
    account: document.getElementById('pgPaneAccount'),
    review: document.getElementById('pgPaneReview'),
    success: document.getElementById('pgPaneSuccess')
  };
  const navSteps = document.querySelectorAll('#pgSignupStepsNav .pg-signup-step');

  function showPane(name){
    Object.values(panes).forEach(p => p.classList.remove('pg-pane-active'));
    panes[name].classList.add('pg-pane-active');

    const order = ['info','account','review'];
    const idx = order.indexOf(name);
    navSteps.forEach((step, i) => {
      step.classList.remove('pg-active','pg-done');
      if(idx === -1) { step.classList.add('pg-done'); return; }
      if(i < idx) step.classList.add('pg-done');
      else if(i === idx) step.classList.add('pg-active');
    });
  }

  function clearErr(input, errEl){
    input.classList.remove('pg-has-error');
    errEl.classList.remove('pg-show');
  }
  function setErr(input, errEl){
    input.classList.add('pg-has-error');
    errEl.classList.add('pg-show');
  }

  // live clear on input
  ['pgFname','pgLname','pgJobtitle','pgEmail','pgPhone','pgEmpid'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('input', () => clearErr(el, document.getElementById(id+'Error')));
  });
  ['pgUname','pgPw2'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('input', () => clearErr(el, document.getElementById(id+'Error')));
  });

  // password toggles
  function wireToggle(btnId, inputId){
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    btn.addEventListener('click', () => {
      const isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';
      btn.setAttribute('aria-label', isPw ? 'Hide password' : 'Show password');
      btn.innerHTML = isPw
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>';
    });
  }
  wireToggle('pgTogglePw1','pgPw');
  wireToggle('pgTogglePw2','pgPw2');

  // password strength
  document.getElementById('pgPw').addEventListener('input', function(){
    const v = this.value;
    const fill = document.getElementById('pgPwFill');
    const label = document.getElementById('pgPwLabel');
    let score = 0;
    if(v.length >= 8) score++;
    if(/[0-9]/.test(v)) score++;
    if(/[^a-zA-Z0-9]/.test(v)) score++;
    if(v.length >= 12) score++;
    const pct = [0,33,60,80,100][score];
    const colors = ['#c0473b','#e8954a','#e8954a','#5fae8f','#5fae8f'];
    const labels = ['At least 8 characters, one number, one symbol','Weak','Fair','Strong','Very strong'];
    fill.style.width = pct + '%';
    fill.style.background = colors[score];
    label.textContent = labels[score];
    clearErr(this, document.getElementById('pgPwError'));
  });

  // Step 1 -> 2
  document.getElementById('pgFormInfo').addEventListener('submit', function(e){
    e.preventDefault();
    let ok = true;
    const required = ['pgFname','pgLname','pgJobtitle','pgEmail','pgPhone','pgEmpid'];
    required.forEach(id => {
      const el = document.getElementById(id);
      if(!el.value.trim()){ setErr(el, document.getElementById(id+'Error')); ok = false; }
    });
    const email = document.getElementById('pgEmail');
    if(email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())){
      setErr(email, document.getElementById('pgEmailError'));
      ok = false;
    }
    if(!ok) return;
    showPane('account');
  });

  // Step 2 back
  document.getElementById('pgBackToInfo').addEventListener('click', () => showPane('info'));

  // Step 2 -> 3
  document.getElementById('pgFormAccount').addEventListener('submit', function(e){
    e.preventDefault();
    let ok = true;
    const uname = document.getElementById('pgUname');
    const pw = document.getElementById('pgPw');
    const pw2 = document.getElementById('pgPw2');
    const terms = document.getElementById('pgTerms');
    const termsErr = document.getElementById('pgTermsError');

    if(!uname.value.trim()){ setErr(uname, document.getElementById('pgUnameError')); ok = false; }
    if(pw.value.length < 8){ setErr(pw, document.getElementById('pgPwError')); ok = false; }
    if(pw2.value !== pw.value || !pw2.value){ setErr(pw2, document.getElementById('pgPw2Error')); ok = false; }
    if(!terms.checked){ termsErr.classList.add('pg-show'); ok = false; } else { termsErr.classList.remove('pg-show'); }

    if(!ok) return;

    // populate review
    document.getElementById('pgRvName').textContent = document.getElementById('pgFname').value + ' ' + document.getElementById('pgLname').value;
    document.getElementById('pgRvJobtitle').textContent = document.getElementById('pgJobtitle').value;
    document.getElementById('pgRvEmail').textContent = document.getElementById('pgEmail').value;
    document.getElementById('pgRvPhone').textContent = document.getElementById('pgPhone').value || '—';
    document.getElementById('pgRvEmpid').textContent = document.getElementById('pgEmpid').value;
    document.getElementById('pgRvUname').textContent = uname.value;

    showPane('review');
  });

  // Step 3 back
  document.getElementById('pgBackToAccount').addEventListener('click', () => showPane('account'));

  // Submit registration
  document.getElementById('pgSubmitAll').addEventListener('click', function(){
    sole.post("../../controllers/powerguard/signup.php",{
      fname : document.getElementById('pgFname').value,
      lname : document.getElementById('pgLname').value,
      job_tittle : document.getElementById('pgJobtitle').value,
      email : document.getElementById('pgEmail').value,
      phone : document.getElementById('pgPhone').value || '—',
      employee_id : document.getElementById('pgEmpid').value,
      username : document.getElementById('pgUname').value,
      password : document.getElementById('pgPw').value
    }).then(res => {
      if(res.status){
            document.getElementById('pgSuccessEmail').textContent = document.getElementById('pgEmail').value;
            showPane('success');
      }else{
        ss.toast(res.title,res.type,res.message)
      }
    })

  });

  // login links
  document.getElementById('pgGoLogin2').addEventListener('click', e => {
    window.location.replace("signin.php")
  });
})();