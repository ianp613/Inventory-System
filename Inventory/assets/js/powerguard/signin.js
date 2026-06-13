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
            localStorage.setItem("userid",res.user.id)
            localStorage.setItem("login","true")
            localStorage.setItem("login_title",res.title)
            localStorage.setItem("login_type",res.type)
            localStorage.setItem("login_message",res.message)
            if(res.user.privileges == "supervisor"){
                window.location.replace("supervisor.php")
            }
            if(res.user.privileges == "technician"){

            }
            if(res.user.privileges == "administrator"){

            }
        }
        ss.toast(res.title,res.type,res.message)
    })
    // console.log('Signing in', { username: username.value, remember: document.getElementById('remember').checked });
  });