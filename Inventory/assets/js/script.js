let unclose = {
    backdrop: 'static',
    keyboard: false
}
const audio = new Audio("../assets/sound/ph.mp3");
const click = new Audio("../assets/sound/click.wav");
var switch_sound = document.getElementById("switch_sound")
var switch_sound_check = document.getElementById("switch_sound_check")

var theme_id = null;
var sound = null;
var theme = null;

if(document.getElementById("sidebar")){
    let inactivityTime = 60000 * 5; // 5 minutes
    let timeout;

    function inactive_logout() {
        if(document.getElementById("sidebar")){
            localStorage.setItem("inactivity",true)
            window.location.replace("../index.php?inactivity=true");    
        }else{
            resetTimer()
        }
    }

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(inactive_logout, inactivityTime);
    }

    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('keypress', resetTimer);
    document.addEventListener('mousedown', resetTimer);
    document.addEventListener('touchstart', resetTimer);

    // Start the timer initially
    resetTimer();

    sole.get("../../controllers/settings.php").then(res => settings(res))

    const logout_modal = new bootstrap.Modal(document.getElementById('logout_modal'),unclose);
    const operate_as_modal = new bootstrap.Modal(document.getElementById('operate_as'),unclose);
    const settings_modal = new bootstrap.Modal(document.getElementById('settings_modal'),unclose);
    const confirm_export_modal = new bootstrap.Modal(document.getElementById('confirm_export_modal'),unclose);
    
    const account_edit_modal = new bootstrap.Modal(document.getElementById('account_edit'),unclose);
    var account = document.getElementById("account");
    var account_email = document.getElementById("account_email");
    var account_new_password = document.getElementById("account_new_password");
    var account_confirm_password = document.getElementById("account_confirm_password");
    var account_cancel_btn = document.getElementById("account_cancel_btn");
    var account_submit_btn = document.getElementById("account_submit_btn");
    var logout_modal_modal = document.getElementById('logout_modal');
    var confirm_logout = document.getElementById("confirm_logout");

    var passkey = document.getElementById("passkey");
    var btn_generate_passkey = document.getElementById("btn_generate_passkey");
    var passkey_field = document.getElementById("passkey_field");

    var operate_as = document.getElementById("operate_as");
    var group_list = document.getElementById("group_list");
    var operate_as_btn = document.getElementById("operate_as_btn");

    sole.get("../../controllers/administrator/get_operate_as.php")
    .then(res => {
        if(res.operate_as){
            exit_group_btn.removeAttribute("hidden")
        }
    })

    operate_as.addEventListener('shown.bs.modal', function () {
        sole.get("../../controllers/administrator/get_group.php")
        .then(res => {
            group_list.innerHTML = "<option selected disabled value=\"\">-- Select Group --</option>"
            res.groups.forEach(group => {
                var op = document.createElement("option")
                op.innerText = group["group_name"]
                op.value = group["id"]
                group_list.appendChild(op)
            });
        })
    })

    operate_as_btn.addEventListener("click",function(){
        if(group_list.value){
            sole.post("../../controllers/administrator/operate_as.php",{
                gid : group_list.value
            }).then(res => {
                if(res.status){
                    setTimeout(() => {
                        localStorage.clear();
                        location.reload();
                    }, 2000);
                    bs5.toast(res.type,res.message,res.size)
                }else{
                    bs5.toast(res.type,res.message,res.size)
                }
            })
        }else{
            bs5.toast("warning","Please select group.")
        }
    })
    
    exit_group_btn.addEventListener("click",function(){
        sole.post("../../controllers/administrator/operate_as_remove.php",{
            exit : true
        }).then(res => {
            if(res.status){
                setTimeout(() => {
                    localStorage.clear();
                    location.reload();
                }, 2000);
                bs5.toast(res.type,res.message,res.size)
            }else{
                bs5.toast(res.type,res.message,res.size)
            }
        })
    })

    sole.get("../controllers/validate_auth.php").then(res => {
        validateAuth(res);
    })

    function validateAuth(res){
        if(res.status){
            localStorage.setItem("userid",res.user[0]["id"])
            localStorage.setItem("email",res.user[0]["email"])
            localStorage.setItem("yourname",res.user[0]["name"])
            localStorage.setItem("username",res.user[0]["username"])
            localStorage.setItem("password",res.user[0]["password"])
            localStorage.setItem("privileges",res.user[0]["privileges"])
            localStorage.setItem("passkey",res.user[0]["passkey"])
            localStorage.setItem("g_member",res.g_member)
            if(document.getElementById("dashboard")){
                !localStorage.getItem("email") ? alert("Your account doesn’t have an email address associated with it. Please add one to enhance your account’s security.") : null
            }
            document.getElementById("userDropdown").innerHTML = "<div class=\"d-flex gray-2\" style=\"margin-top: 10px;\"><span class=\"fa fa-user-circle-o me-2 mt-2 f-20\"></span> <div>" + res.user[0]["name"] + "<br><p class=\"f-10\" style=\"margin-top: -4px;\"> Account: " + (res.user[0]["privileges"] == "Assistant Technician" ? "Technician" : res.user[0]["privileges"]) + "</p></div></div>"

            if(res.user[0]["privileges"] == "Administrator"){
                document.querySelectorAll('.g_menu').forEach(element => {
                    element.removeAttribute("hidden")
                });
                document.querySelectorAll('.g_account').forEach(element => {
                    element.removeAttribute("hidden")
                });
                document.querySelectorAll('.g_op').forEach(element => {
                    element.removeAttribute("hidden")
                });
            }else{
                if(res.group){
                    if(res.group[0]["type"] == "IT"){
                        document.querySelectorAll('.g_menu').forEach(element => {
                            element.removeAttribute("hidden")
                        });
                    }else{
                        document.querySelectorAll('.g_menu').forEach(element => {
                            element.remove()
                        }); 
                    }
                }

                if(res.user[0]["privileges"] == "Supervisor"){
                    document.querySelectorAll('.g_account').forEach(element => {
                        element.removeAttribute("hidden")
                    });
                }else{
                    document.querySelectorAll('.g_account').forEach(element => {
                        element.remove()
                    });
                }

                document.querySelectorAll('.g_op').forEach(element => {
                    element.remove()
                });
                
            }

            if(localStorage.getItem("password") == "12345"){
                alert("Your account password is set to the default setting. For security reasons, please update your password.")
                account_email.value = localStorage.getItem("email") != "-" ? localStorage.getItem("email") : "";
                passkey_field.hidden = true
                account_cancel_btn.hidden = true
                account_edit_modal.show()
            }
        }else{
            window.location.replace("../index.php");
        }
    }
    

    document.getElementById("settings").addEventListener("click",function(){
        settings_modal.show()
    })

    document.getElementById("switch").addEventListener("click",function(){
        console.log("switch")
    })

    document.getElementById("logout").addEventListener("click",function(){
        sound ? audio.play() : null
        logout_modal.show()
    })

    // document.addEventListener('keydown', e => {
    //     console.log(e.ctrlKey)
    //     if (e.ctrlKey && e.key.toLowerCase() === 'l') {
    //         sound ? audio.play() : null
    //         logout_modal.show()
    //     }
    // })

    const pressedKeys = {};
    const LONG_PRESS_DURATION = 600; // milliseconds

    // function handleKeyDown(event) {
    //     const key = event.key;

    //     // Prevent repeated keydown events from resetting the timer
    //     if (pressedKeys[key]) return; 

    //     // Record the key as pressed and set the timer
    //     pressedKeys[key] = setTimeout(() => {
    //         // console.log(`Long press detected for key: ${key}`);
    //         // Perform long press action here
    //         if(key == "l" || key == "L"){
    //             sound ? audio.play() : null
    //             logout_modal.show()
    //         }
    //     }, LONG_PRESS_DURATION);
    // }

    function handleKeyUp(event) {
        const key = event.key;
        
        // Clear the timeout when the key is released
        clearTimeout(pressedKeys[key]);
        delete pressedKeys[key];
        
        // Optional: add logic here for a short press if needed
        // Example: if a long press didn't happen, a short press did
        // (This requires additional tracking to distinguish)
    }

    // Add event listeners to the document
    // document.addEventListener('keydown', handleKeyDown);
    document.addEventListener('keyup', handleKeyUp);

    confirm_logout.addEventListener("click",function(){
        window.location.replace("../index.php");
    })

    logout_modal_modal.addEventListener('shown.bs.modal', function () {
        confirm_logout.focus()
    })

    var export_password = document.getElementById("export_password");

    document.getElementById("export_db").addEventListener("click",function(){
        settings_modal.hide()
        confirm_export_modal.show()
    })

     // CONFIRM PASSWORD FOCUS
    document.getElementById('confirm_export_modal').addEventListener('shown.bs.modal', function () {
        export_password.focus()
    })

    document.getElementById("cancel_export").addEventListener("click",function(){
        settings_modal.show()
        confirm_export_modal.hide()
        export_password.value = ""
    })

    document.addEventListener("keypress",function(e){
        if(document.activeElement.classList.contains('export_password') && e.key == "Enter"){
            authenticate()
        }
    })

    document.getElementById("confirm_export").addEventListener("click",function(){
        authenticate()
    })

    function authenticate(){
        if(localStorage.getItem("username")){
            if(export_password.value){
                sole.post("../controllers/pass_check.php",{
                    username: localStorage.getItem("username"),
                    password: export_password.value
                })
                .then(res => exportAssist(res))
            }else{
                alert("PLEASE INPUT ACCOUNT PASSWORD")
            }
        }else{
            bs5.toast("warning","Something went wrong, try again.")
        }
    }

    function exportAssist(res){
        if(res.status){
            sole.get("../controllers/db_export.php")
            .then(res => exportAssistDownload(res))
        }else{
            alert(res.message)
        }
    }

    function exportAssistDownload(res){
        if(res){
            const filePath = res;
            const filename = filePath.split('/').pop();

            fetch(res)
            .then(response => response.blob()) // Convert response to a Blob
            .then(blob => {
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = filename; // Set the filename
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
            .catch(error => bs5.toast("error", "Export Failed: " + error));

            settings_modal.show()
            confirm_export_modal.hide()
            export_password.value = ""

            setTimeout(() => {
                sole.post("../../controllers/clear_temp.php").then(res => console.log(res));
            }, 5000);
        }else{
            alert("EXPORT ERROR")
        }
    }

    const sidebar = document.getElementById('sidebar');
    let collapsed = false;

    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        collapsed = !collapsed;
    }

    // Add a toggle button (optional)
    const toggleButton = document.createElement('button');
    toggleButton.innerHTML = '<i class="fa fa-bars"></i>';
    toggleButton.classList.add('btn', 'btn-outline-light', 'mb-3');
    toggleButton.style = "width: 50px;"
    toggleButton.addEventListener('click', toggleSidebar);

    // Insert the toggle button at the top of the sidebar
    //sidebar.insertBefore(toggleButton, sidebar.firstChild);
    const toggleButtonContainer = document.createElement('div');
    toggleButtonContainer.id = 'toggleButton';
    toggleButtonContainer.style.marginBottom = '10px';
    toggleButtonContainer.style.textAlign = 'center';
    toggleButtonContainer.style.width = '100%';
    toggleButtonContainer.style.borderRadius = '0';
    toggleButtonContainer.style.marginTop = 'auto';

    toggleButtonContainer.appendChild(toggleButton);
    sidebar.appendChild(toggleButtonContainer);

    // Initial check for screen size
    function checkScreenSize() {
        if (window.innerWidth <= 768 && !collapsed) {
            toggleSidebar();
            if(localStorage.getItem("privileges") == "Administrator" && document.getElementById("logs")){
                document.getElementById("select_log").style.position = ""
                document.getElementById("select_log_container").classList.add("justify-content-center")
            }
        } else if (window.innerWidth > 768 && collapsed) {
            toggleSidebar();
            if(localStorage.getItem("privileges") == "Administrator" && document.getElementById("logs")){
                document.getElementById("select_log").style.position = "absolute"
                document.getElementById("select_log_container").classList.remove("justify-content-center")
            }
        }
    }

    // Call checkScreenSize on load and resize
    window.addEventListener('load', checkScreenSize);
    window.addEventListener('resize', checkScreenSize);


    account.addEventListener("click",function(){
        passkey_field.hidden = false
        account_email.value = localStorage.getItem("email") != "-" ? localStorage.getItem("email") : "";
        passkey.value = localStorage.getItem("passkey");
        account_edit_modal.show()
    })

    btn_generate_passkey.addEventListener("click",function(){
        sole.post("../../controllers/generate_key.php",{
            id : localStorage.getItem("userid")
        }).then(res => {
            passkey.value = res.key
        })
    })
    
    account_submit_btn.addEventListener("click",function(){
        var bol = false;
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(account_email.value){
            var bol = regex.test(account_email.value)
            if(!bol){
                alert("Please input a valid email.")
            }
        }
        bol = account_new_password.value
            ? account_confirm_password.value
                ? account_new_password.value !== account_confirm_password.value
                ? (alert("Password did not match."), false)
                : true
                : (alert("Please confirm new password."), false)
            : (alert("Please enter a new password."), false);

        if(bol){
            sole.post("../../controllers/update_account.php",{
                id: localStorage.getItem("userid"),
                email: account_email.value,
                password: account_new_password.value
            }).then(res => {
                bs5.toast(res.type,res.message,res.size)
                if(res.status){
                    localStorage.setItem("password",account_new_password.value)
                    localStorage.setItem("email",account_email.value)
                    account_email.value = ""
                    account_new_password.value = ""
                    account_confirm_password.value = ""
                    account_cancel_btn.hidden = false
                }
            })
        }
    })
}


if(document.getElementsByClassName("ps-field")[0]){
    document.getElementsByClassName("ps-field")[0].addEventListener("click",function(e){
        if(e.target.tagName == "SPAN"){
            if(e.target.classList.contains("fa-eye-slash")){
                document.getElementById("password").type = "text"
                e.target.classList.remove("fa-eye-slash")
                e.target.classList.add("fa-eye")
            }else{
                document.getElementById("password").type = "password"
                e.target.classList.remove("fa-eye")
                e.target.classList.add("fa-eye-slash")
            }
        }
    })    
}

// PUBLIC

document.addEventListener('contextmenu', event => {
    event.preventDefault();
});

const elements = document.querySelectorAll("input[type='text'], input[type='password'], textarea");
elements.forEach((element) => {
  element.addEventListener("input", function() {
    this.value = this.value.replace(/[']/g, "");
  });
});

const originalAddEventListener = EventTarget.prototype.addEventListener;

// Override the method to include the click sound
EventTarget.prototype.addEventListener = function(type, listener, options) {
    if (type === "click") {
        // Wrap the original listener to also play the sound
        const newListener = function(event) {
            sound ? click.play() : null;
            listener.call(this, event);
        };
        originalAddEventListener.call(this, type, newListener, options);
    } else {
        originalAddEventListener.call(this, type, listener, options);
    }
};


if(document.getElementById("sidebar")){
    document.getElementById("settings_modal").addEventListener('shown.bs.modal', function () {
        sole.get("../../controllers/settings.php").then(res => settings(res))
    })

    function settings(res){
        theme_id = res["id"]
        sound = res["sound"] == "1" ? true : false
        theme = res["theme"] == "1" ? true : false

        if(sound){
            switch_sound_check.setAttribute("checked","true")
        }else{
            switch_sound_check.getAttribute("checked") ? switch_sound_check.removeAttribute("checked") : null
        }
    }
    let isRunning = false;
    switch_sound.addEventListener("click",function (e){
        if (isRunning) return;
        isRunning = true;
        sole.post("../../controllers/settings_set.php", {
            id: theme_id,
            type: "sound"
        }).then(res => {
            settings(res);
            isRunning = false;
        });
    })
}


function splash(message, seconds) {
  // Create splash element
  const splashScreen = document.createElement("div");
  const bs5_spinner = "<div class=\"spinner-border text-dark ht-70 wd-70 me-5\" role=\"status\"></div>"
  splashScreen.innerHTML = bs5_spinner
  splashScreen.id = "splash";
  splashScreen.style.position = "fixed";
  splashScreen.style.top = "0";
  splashScreen.style.left = "0";
  splashScreen.style.width = "100%";
  splashScreen.style.height = "100%";
  splashScreen.style.background = "white";
  splashScreen.style.display = "flex";
  splashScreen.style.alignItems = "center";
  splashScreen.style.justifyContent = "center";
  splashScreen.style.fontSize = "24px";
  splashScreen.style.opacity = "1";
  splashScreen.style.transition = "opacity 0.5s ease-out";
  splashScreen.style.zIndex = "9999";

  if (message) {
    splashScreen.innerHTML = message;
  }

  document.body.appendChild(splashScreen);

  // Remove after fade
  setTimeout(() => {
    splashScreen.style.opacity = "0";
    setTimeout(() => {
      splashScreen.remove();
    }, 500); // Matches fade transition duration
  }, seconds);
}
splash(null, 200)
