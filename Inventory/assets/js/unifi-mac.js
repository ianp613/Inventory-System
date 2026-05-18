var register_form_btn     = document.getElementById("register_form_btn")
var delete_form_btn       = document.getElementById("delete_form_btn")
var password_form_btn     = document.getElementById("password_form_btn")

var register_form         = document.getElementById("register_form")
var delete_form           = document.getElementById("delete_form")
var password_form         = document.getElementById("password_form")

var mac_ssid              = document.getElementById("mac_ssid")
var mac_address           = document.getElementById("mac_address")
var mac_address_          = document.getElementById("mac_address_")
var mac_name              = document.getElementById("mac_name")
var mac_device            = document.getElementById("mac_device")
var mac_project           = document.getElementById("mac_project")
var mac_location          = document.getElementById("mac_location")
var mac_remarks           = document.getElementById("mac_remarks")
var mac_message           = document.getElementById("mac_message")
var mac_register_by       = document.getElementById("mac_register_by")

var clear_btn             = document.getElementById("clear_btn")
var register_mac          = document.getElementById("register_mac")
var loading_mac           = document.getElementById("loading_mac")

var delete_mac_ssid       = document.getElementById("delete_mac_ssid")
var delete_mac_address    = document.getElementById("delete_mac_address")

var delete_clear_btn      = document.getElementById("delete_clear_btn")
var delete_mac            = document.getElementById("delete_mac")
var delete_loading_mac    = document.getElementById("delete_loading_mac")

var password_mac_ssid     = document.getElementById("password_mac_ssid")
var password_mac          = document.getElementById("password_mac")
var password_loading_mac  = document.getElementById("password_loading_mac")

var um_login              = document.getElementById("um_login")
var um_login_card         = document.getElementById("um_login_card")
var um_login_userid       = document.getElementById("um_login_userid")
var um_login_password     = document.getElementById("um_login_password")
var um_login_btn          = document.getElementById("um_login_btn")


var theme               = document.getElementById("theme")
var Building            = []
var default_theme       = "dark"

async function GetWifi(params) {
  await sole.get("../controllers/unifi-mac/get-wifi.php").then(res => {
    res.wifis.forEach(wifi => {
      var opt                   = document.createElement("option")
      opt.value                 = wifi.id
      opt.innerText             = wifi.name
      mac_ssid.appendChild(opt)

      var opt_                   = document.createElement("option")
      opt_.value                 = wifi.id
      opt_.innerText             = wifi.name
      delete_mac_ssid.appendChild(opt_)

      var opt__                   = document.createElement("option")
      opt__.value                 = wifi.id
      opt__.innerText             = wifi.name
      password_mac_ssid.appendChild(opt__)
    });
    localStorage.setItem("unifi_mac_gid",res.g_id)
  })
}

register_form_btn.addEventListener("click", e => {
  register_form_btn.classList.remove("btn-secondary")
  register_form_btn.classList.add("btn-light")

  delete_form_btn.classList.remove("btn-light")
  delete_form_btn.classList.add("btn-secondary")

  password_form_btn.classList.remove("btn-light")
  password_form_btn.classList.add("btn-secondary")

  register_form.hidden  = false
  delete_form.hidden    = true
  password_form.hidden  = true
  mac_message.innerHTML = ""
})

delete_form_btn.addEventListener("click", e => {
  delete_form_btn.classList.remove("btn-secondary")
  delete_form_btn.classList.add("btn-light")

  register_form_btn.classList.remove("btn-light")
  register_form_btn.classList.add("btn-secondary")
  
  password_form_btn.classList.remove("btn-light")
  password_form_btn.classList.add("btn-secondary")

  register_form.hidden  = true
  delete_form.hidden    = false
  password_form.hidden  = true
  mac_message.innerHTML = ""
})

password_form_btn.addEventListener("click", e => {
  password_form_btn.classList.remove("btn-secondary")
  password_form_btn.classList.add("btn-light")

  register_form_btn.classList.remove("btn-light")
  register_form_btn.classList.add("btn-secondary")

  delete_form_btn.classList.remove("btn-light")
  delete_form_btn.classList.add("btn-secondary")

  register_form.hidden  = true
  delete_form.hidden    = true
  password_form.hidden  = false
  mac_message.innerHTML = ""
})



mac_address.addEventListener("input",function(){
    fixMAC(this)
})

delete_mac_address.addEventListener("input",function(){
    fixMAC(this)
})

function fixMAC(inp){
  inp.value = inp.value.replace(/[^a-zA-Z0-9:]/g, "")
  if(inp.value){
      var str       = inp.value.replace(/:/g, "")
      str           = str.match(/.{1,2}/g)
      inp.value    = str.join(":").toLowerCase()
  }
}

clear_btn.addEventListener("click", e => {
  mac_address.value       = ""
  mac_ssid.value          = ""
  mac_name.value          = ""
  mac_device.value        = ""
  mac_project.value       = ""
  mac_location.value      = ""
  mac_remarks.value       = ""
  mac_project.innerHTML   = ""
  var opt_project         = document.createElement("option")
  opt_project.value       = ""
  opt_project.innerText   = "-- Select Project / Office --"
  opt_project.disabled    = true
  opt_project.selected    = true
  mac_project.appendChild(opt_project)
})

delete_clear_btn.addEventListener("click", e => {
  delete_mac_address.value  = ""
  delete_mac_ssid.value     = ""
})

register_mac.addEventListener("click", e => {
  if(!mac_address.value){
    if(sessionStorage.getItem("last_mac_address") !== null){
      sessionStorage.setItem("last_mac_address",mac_address.value)  
    }else{
      sessionStorage.setItem("last_mac_address",sessionStorage.getItem("last_mac_address") + "+++" + mac_address.value) 
    }
    
    alert("Please input MAC address.")
    return
  }
  if(!mac_ssid.value){
    alert("Please select Wifi SSID.")
    return
  }
  if(!mac_name.value){
    alert("Please input name.")
    return
  }
  if(!mac_device.value){
    alert("Please select device.")
    return
  }
  if(!mac_project.value){
    alert("Please input project.")
    return
  }
  if(!mac_project.value){
    alert("Please input location.")
    return
  }
  if(!mac_register_by.value){
    alert("Please select registered by.")
    return
  }

  register_mac.hidden   = true
  loading_mac.hidden    = false
  sole.post("../controllers/unifi-mac/register-mac.php",{
    mac_address       : mac_address.value,
    mac_ssid          : mac_ssid.value,
    mac_name          : mac_name.value,
    mac_device        : mac_device.value,
    mac_project       : mac_project.value,
    mac_location      : mac_location.value,
    mac_remarks       : mac_remarks.value,
    mac_register_by   : mac_register_by.value,
    g_id              : localStorage.getItem("unifi_mac_gid")
  }).then(res => {
    displayMessage(res)
    register_mac.hidden     = false
    loading_mac.hidden      = true
    clear_btn.click()
  })
  if(sessionStorage.getItem("last_mac_address") !== null){
    mac_address_.innerHTML = ""
    var mac_ = sessionStorage.getItem("last_mac_address").split("+++")
    mac_.forEach(mac => {
      mac_address_.insertAdjacentHTML("beforeend",
        `<option>${mac}</option>`
      )
    })
  }
})

delete_mac.addEventListener("click", e => {
  if(!delete_mac_address.value){
    alert("Please input MAC address.")
    return
  }
  if(!delete_mac_ssid.value){
    alert("Please select Wifi SSID.")
    return
  }
  delete_mac.hidden           = true
  delete_loading_mac.hidden   = false
  sole.post("../controllers/unifi-mac/delete-mac.php",{
    delete_mac_address    : delete_mac_address.value,
    delete_mac_ssid       : delete_mac_ssid.value,
    mac_register_by      : mac_register_by.value,
    g_id                  : localStorage.getItem("unifi_mac_gid")
  }).then(res => {
    displayMessage(res)
    delete_mac.hidden           = false
    delete_loading_mac.hidden   = true
    delete_clear_btn.click()
  })
})

password_mac.addEventListener("click", e => {
  if(!password_mac_ssid.value){
    alert("Please select Wifi SSID.")
    return
  }

  password_mac.hidden           = true
  password_loading_mac.hidden   = false
  sole.post("../controllers/unifi-mac/password-mac.php",{
    password_mac_ssid       : password_mac_ssid.value,
  }).then(res => {
    displayMessage(res)
    password_mac.hidden           = false
    password_loading_mac.hidden   = true
    password_mac_ssid.value       = ""
  })
})

um_login_btn.addEventListener("click", e => {
  if(!um_login_userid.value || !um_login_password.value){
    bs5.toast("warning","<span class=\"text-dark\">Please input User ID and Password.<span>")
    return
  }
  sole.post("../controllers/unifi-mac/login.php", {
    userid : um_login_userid.value,
    password : um_login_password.value
  }).then(res => {
    if(res.status){
      um_login_card.hidden = true
      um_login.classList.remove("um-login")
      mac_register_by.value = res.user[0]["name"]
      bs5.toast(res.type,res.message + res.user[0]["name"])
    }else{
      bs5.toast(res.type,res.message)
    }
  })
})

setTheme()
loadTheme()

theme.addEventListener("change", e => {
  localStorage.setItem("unifi_mac_theme",theme.value)
  setTheme()
  loadTheme()
})

function displayMessage(res){
  mac_message.innerHTML = ""
    if(res.site.length){
      for (let i = 0; i < res.site.length; i++) {
        mac_message.insertAdjacentHTML("afterbegin", 
          `<div class="alert-`+res.status[i]+` p-3 pb-2 rounded-3 mb-2">`+
              `<div class="d-flex justify-content-between">`+
                  `<h6 class="f-13"><span class="fa fa-wifi"></span> `+res.site[i]+`</h6>`+
                  `<span onclick="this.parentNode.parentNode.remove()" class="fa fa-remove"></span>`+
              `</div>`+
              `<h6 class="ms-4 f-i f-13 unifi-message">`+res.message[i]+`</h6>`+
          `</div>`
        )
      }
    }
}

function setTheme(){
  if(localStorage.getItem("unifi_mac_theme") === null){
    localStorage.setItem("unifi_mac_theme",default_theme)
    theme.value = default_theme
  }else{
    theme.value = localStorage.getItem("unifi_mac_theme")
    if(localStorage.getItem("unifi_mac_theme") == "dark"){
      if(!document.body.classList.contains("dark")){
        document.body.classList.remove("light")
        document.body.classList.add("dark")
      }
    }
    if(localStorage.getItem("unifi_mac_theme") == "light"){
      if(!document.body.classList.contains("light")){
        document.body.classList.remove("dark")
        document.body.classList.add("light")
      }
    }
  }
}


function loadTheme(){
  if(document.body.classList.contains("dark")){
    document.body.classList.add("bg-dark")
    document.body.classList.add("text-light")
    document.getElementsByTagName("h5")[0].classList.add("text-light")
    document.getElementsByTagName("img")[0].style = "border: solid 2px white; margin-top: -23px;"
    document.getElementsByTagName("textarea")[0].classList.add("text-light")
    document.getElementsByTagName("textarea")[0].classList.add("bg-dark")
    var inps = document.getElementsByTagName("input")
    for (let i = 0; i < inps.length; i++) {
      inps[i].classList.add("bg-dark")
      inps[i].classList.add("text-light")
    }

    var sels = document.getElementsByTagName("select")
    for (let i = 0; i < sels.length; i++) {
      sels[i].classList.add("bg-dark")
      sels[i].classList.add("text-light")
    }

    var btns = document.getElementsByTagName("button")
    for (let i = 0; i < btns.length; i++) {
      btns[i].style = "border: solid 2px white;"
      btns[i].style = "border: solid 2px white;"
    }
  }else{
    document.body.classList.remove("bg-dark")
    document.body.classList.remove("text-light")
    document.getElementsByTagName("h5")[0].classList.remove("text-light")
    document.getElementsByTagName("img")[0].style = "margin-top: -23px;"
    document.getElementsByTagName("textarea")[0].classList.remove("text-light")
    document.getElementsByTagName("textarea")[0].classList.remove("bg-dark")
    var inps = document.getElementsByTagName("input")
    for (let i = 0; i < inps.length; i++) {
      inps[i].classList.remove("bg-dark")
      inps[i].classList.remove("text-light")
    }

    var sels = document.getElementsByTagName("select")
    for (let i = 0; i < sels.length; i++) {
      sels[i].classList.remove("bg-dark")
      sels[i].classList.remove("text-light")
    }

    var btns = document.getElementsByTagName("button")
    for (let i = 0; i < btns.length; i++) {
      btns[i].removeAttribute("style")
      btns[i].removeAttribute("style")
    }
  }
}

GetWifi()
GetLocations()
GetUsers()

function GetUsers(){
  sole.get("../controllers/unifi-mac/get-users.php").then(res => {
    res.forEach(user => {
      // if(user["name"].toLowerCase() != "administrator"){
        // if(user["username"].toLowerCase() != "703f_administrator"){
          var opt                 = document.createElement("option")
          opt.value               = user["name"]
          opt.innerText           = user["name"]
          mac_register_by.appendChild(opt)
        // }
      // }
    })
  })
}

function GetLocations(){
  sole.get("../controllers/equipments/get_equipment_location_preset.php").then(res => {
    res.Building.forEach(bldg => {
      Building.push([Object.keys(bldg)[0],bldg])
    })
    mac_location.innerHTML    = ""
    var opt_building          = document.createElement("option")
    opt_building.value        = ""
    opt_building.innerText    = "-- Select Site / Location --"
    opt_building.disabled     = true
    opt_building.selected     = true
    mac_location.appendChild(opt_building)
    Building.forEach(bldg_ => {
      var opt                 = document.createElement("option")
      opt.value               = bldg_[0]
      opt.innerText           = bldg_[0]
      mac_location.appendChild(opt)
    });
  })

  mac_location.addEventListener("change", e => {
    mac_project.innerHTML     = ""
    var opt_project           = document.createElement("option")
    opt_project.value         = ""
    opt_project.innerText     = "-- Select Project / Office --"
    opt_project.disabled      = true
    opt_project.selected      = true
    mac_project.appendChild(opt_project)
    Building.forEach(bldg_ => {
      if(bldg_[0] == mac_location.value){
        bldg_[1][bldg_[0]].Project.forEach(project => {
          var opt             = document.createElement("option")
          opt.value           = project
          opt.innerText       = project
          mac_project.appendChild(opt)
        })
      }
    });
  })
}

function splash(message, seconds) {
  // Create splash element
  const splashScreen                = document.createElement("div");
  const bs5_spinner                 = "<div class=\"spinner-border text-dark ht-70 wd-70 me-5\" role=\"status\"></div>"
  splashScreen.innerHTML            = bs5_spinner
  splashScreen.id                   = "splash";
  splashScreen.style.position       = "fixed";
  splashScreen.style.top            = "0";
  splashScreen.style.left           = "0";
  splashScreen.style.width          = "100%";
  splashScreen.style.height         = "100%";
  splashScreen.style.background     = "white";
  splashScreen.style.display        = "flex";
  splashScreen.style.alignItems     = "center";
  splashScreen.style.justifyContent = "center";
  splashScreen.style.fontSize       = "24px";
  splashScreen.style.opacity        = "1";
  splashScreen.style.transition     = "opacity 0.5s ease-out";
  splashScreen.style.zIndex         = "9999";

  if (message) {
    splashScreen.innerHTML          = message;
  }

  document.body.appendChild(splashScreen);

  // Remove after fade
  setTimeout(() => {
    splashScreen.style.opacity      = "0";
    setTimeout(() => {
      splashScreen.remove();
    }, 500); // Matches fade transition duration
  }, seconds);
}

document.addEventListener("contextmenu", e => {
  e.preventDefault()
})


splash(null, 200)