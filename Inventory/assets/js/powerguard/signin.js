  const form = document.getElementById('loginForm');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const usernameError = document.getElementById('usernameError');
  const passwordError = document.getElementById('passwordError');
  const errorBanner = document.getElementById('errorBanner');
  const togglePw = document.getElementById('togglePw');

  togglePw.addEventListener('click', () => {
    const isPw = password.type === 'password';
    password.type = isPw ? 'text' : 'password';
    togglePw.setAttribute('aria-label', isPw ? 'Hide password' : 'Show password');
    togglePw.innerHTML = isPw
      ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
      : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>';
  });

  function clearError(input, errEl){
    input.classList.remove('has-error');
    errEl.classList.remove('show');
  }

  username.addEventListener('input', () => clearError(username, usernameError));
  password.addEventListener('input', () => clearError(password, passwordError));

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    errorBanner.classList.remove('show');
    let valid = true;

    if(!username.value.trim()){
      username.classList.add('has-error');
      usernameError.classList.add('show');
      valid = false;
    } else {
      clearError(username, usernameError);
    }

    if(!password.value){
      password.classList.add('has-error');
      passwordError.classList.add('show');
      valid = false;
    } else {
      clearError(password, passwordError);
    }

    if(!valid) return;

    sole.post("../../controllers/powerguard/signin.php", {
        username : username.value,
        password : password.value
    }).then(res => {
        if(res.status){
            username.value = ""
            password.value = ""
            document.getElementById('remember').checked = false
            localStorage.setItem("login_title",res.title)
            localStorage.setItem("login_type",res.type)
            localStorage.setItem("login_message",res.message)
            if(res.user.privileges == "supervisor"){
              localStorage.setItem("pgs_avatar",res.user.fname[0]+res.user.lname[0].toUpperCase())
              localStorage.setItem("pgs_name",res.user.fname[0]+". "+res.user.lname)
              localStorage.setItem("pgs_role","Supervisor · ")

              localStorage.setItem("userid_sup",res.user.id)
              localStorage.setItem("login_sup",true)
              localStorage.removeItem("pgs-active")
              window.location.replace("supervisor.php")
            }
            if(res.user.privileges == "technician"){
              localStorage.setItem("pgt_avatar",res.user.fname[0]+res.user.lname[0].toUpperCase())
              localStorage.setItem("pgt_name",res.user.fname[0]+". "+res.user.lname)
              localStorage.setItem("pgt_role",`${res.user.job_title} · IT Department`)

              localStorage.setItem("userid_tech",res.user.id)
              localStorage.setItem("login_tech",true)
              localStorage.removeItem("pgt-active")
              window.location.replace("technician.php")
            }
            if(res.user.privileges == "administrator"){
              localStorage.setItem("pga_avatar",res.user.fname[0]+res.user.lname[0].toUpperCase())
              localStorage.setItem("pga_name",res.user.fname[0]+". "+res.user.lname)
              localStorage.setItem("pga_role","Administrator · Full system access")

              localStorage.setItem("userid_admin",res.user.id)
              localStorage.setItem("login_admin",true)
              localStorage.removeItem("pga-active")
              window.location.replace("administrator.php")
            }
        }
        ss.toast(res.title,res.type,res.message,null,"#082b49")
    })
    // console.log('Signing in', { username: username.value, remember: document.getElementById('remember').checked });
  });

  sole.get("https://ddc-artisan.ddns.net:9552/controllers/api/test.php").then(res => {
    console.log(res)
  })