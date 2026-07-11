var register_form_btn     = document.getElementById("register_form_btn")
var delete_form_btn       = document.getElementById("delete_form_btn")
var password_form_btn     = document.getElementById("password_form_btn")
var voucher_form_btn     = document.getElementById("voucher_form_btn")

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

var voucher_clear         = document.getElementById("voucher_clear")
var voucher_get           = document.getElementById("voucher_get")
var voucher_get_loading   = document.getElementById("voucher_get_loading")

var voucher_site            = document.getElementById("voucher_site")
var voucher_name          = document.getElementById("voucher_name")
var voucher_device        = document.getElementById("voucher_device")
var voucher_project       = document.getElementById("voucher_project")
var voucher_location      = document.getElementById("voucher_location")

var voucher_last_code     = document.getElementById("voucher_last_code")
var voucher_code          = document.getElementById("voucher_code")
var voucher_duration      = document.getElementById("voucher_duration")
var voucher_down          = document.getElementById("voucher_down")
var voucher_up            = document.getElementById("voucher_up")
var voucher_note          = document.getElementById("voucher_note")
var voucher_status        = document.getElementById("voucher_status")
var voucher_qouta         = document.getElementById("voucher_qouta")
var voucher_used          = document.getElementById("voucher_used")

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

  voucher_form_btn.classList.remove("btn-light")
  voucher_form_btn.classList.add("btn-secondary")

  register_form.hidden  = false
  delete_form.hidden    = true
  password_form.hidden  = true
  voucher_form.hidden   = true
  mac_message.innerHTML = ""
})

delete_form_btn.addEventListener("click", e => {
  delete_form_btn.classList.remove("btn-secondary")
  delete_form_btn.classList.add("btn-light")

  register_form_btn.classList.remove("btn-light")
  register_form_btn.classList.add("btn-secondary")
  
  password_form_btn.classList.remove("btn-light")
  password_form_btn.classList.add("btn-secondary")

  voucher_form_btn.classList.remove("btn-light")
  voucher_form_btn.classList.add("btn-secondary")

  register_form.hidden  = true
  delete_form.hidden    = false
  password_form.hidden  = true
  voucher_form.hidden   = true
  mac_message.innerHTML = ""
})

password_form_btn.addEventListener("click", e => {
  password_form_btn.classList.remove("btn-secondary")
  password_form_btn.classList.add("btn-light")

  register_form_btn.classList.remove("btn-light")
  register_form_btn.classList.add("btn-secondary")

  delete_form_btn.classList.remove("btn-light")
  delete_form_btn.classList.add("btn-secondary")

  voucher_form_btn.classList.remove("btn-light")
  voucher_form_btn.classList.add("btn-secondary")

  register_form.hidden  = true
  delete_form.hidden    = true
  password_form.hidden  = false
  voucher_form.hidden   = true
  mac_message.innerHTML = ""
})

voucher_form_btn.addEventListener("click", e => {
  password_form_btn.classList.add("btn-secondary")
  password_form_btn.classList.remove("btn-light")

  register_form_btn.classList.remove("btn-light")
  register_form_btn.classList.add("btn-secondary")

  delete_form_btn.classList.remove("btn-light")
  delete_form_btn.classList.add("btn-secondary")

  voucher_form_btn.classList.add("btn-light")
  voucher_form_btn.classList.remove("btn-secondary")

  register_form.hidden  = true
  delete_form.hidden    = true
  password_form.hidden  = true
  voucher_form.hidden   = false
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

voucher_clear.addEventListener("click", e => {
  voucher_site.value          = ""
  voucher_name.value          = ""
  voucher_device.value        = ""
  voucher_project.value       = ""
  voucher_location.value      = ""
  voucher_project.innerHTML   = ""

  voucher_code.innerText      = ""
  voucher_duration.innerText  = ""
  voucher_note.innerText     = ""
  voucher_status.innerText    = ""
  voucher_qouta.innerText     = ""
  voucher_used.innerText      = ""
  var opt_project             = document.createElement("option")
  opt_project.value           = ""
  opt_project.innerText       = "-- Select Project / Office --"
  opt_project.disabled        = true
  opt_project.selected        = true
  voucher_project.appendChild(opt_project)
})

delete_clear_btn.addEventListener("click", e => {
  delete_mac_address.value  = ""
  delete_mac_ssid.value     = ""
})

register_mac.addEventListener("click", e => {
  if(!mac_address.value){
    alert("Please input MAC address.")
    return
  }else{
    if(sessionStorage.getItem("last_mac_address") !== null){
      sessionStorage.setItem("last_mac_address",sessionStorage.getItem("last_mac_address") + "+++" + mac_address.value) 
    }else{
      sessionStorage.setItem("last_mac_address",mac_address.value)
    }
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
    alert("Please select project.")
    return
  }
  if(!mac_project.value){
    alert("Please select location.")
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
    loadLastMAC()
    clear_btn.click()
  })
})

if(localStorage.getItem("voucher_last_code") !== null){
  voucher_last_code.innerText = localStorage.getItem("voucher_last_code")
}

voucher_get.addEventListener("click", e => {
  if(!voucher_site.value){
    alert("Please select wifi network.")
    return    
  }
  if(!voucher_name.value){
    alert("Please input name.")
    return
  }
  if(!voucher_device.value){
    alert("Please select device.")
    return
  }
  if(!voucher_project.value){
    alert("Please select project.")
    return
  }
  if(!voucher_location.value){
    alert("Please select location.")
    return
  }

  voucher_get.hidden            = true
  voucher_get_loading.hidden    = false
  voucher_clear.hidden          = true
  sole.post("../controllers/unifi-mac/get-code.php",{
    voucher_site          : voucher_site.value,
    voucher_name          : voucher_name.value,
    voucher_device        : voucher_device.value,
    voucher_project       : voucher_project.value,
    voucher_location      : voucher_location.value,
    name                  : mac_register_by.value
  }).then(res => {
    displayMessage_(res)

    if (res.status === 'success' && res.vouchers.length > 0) {
        const randomVoucher = res.vouchers[Math.floor(Math.random() * res.vouchers.length)];
        voucher_code.innerText            = formatVoucherCode(randomVoucher.code)
        voucher_duration.innerText        = formatVoucherDuration(randomVoucher.duration)
        voucher_down.innerText            = formatVoucherSpeed(randomVoucher.down)
        voucher_up.innerText              = formatVoucherSpeed(randomVoucher.up)
        voucher_note.innerText            = randomVoucher.note
        voucher_status.innerText          = randomVoucher.status
        voucher_qouta.innerText           = randomVoucher.quota
        voucher_used.innerText            = randomVoucher.used
    }
    voucher_clear.hidden          = false
    voucher_get.hidden            = false
    voucher_get_loading.hidden    = true
  })
})

function formatVoucherCode(code) {
  const str = String(code);
  const res = str.slice(0, 5) + '-' + str.slice(5, 10);
  localStorage.setItem("voucher_last_code","LAST CODE: " + res);
  voucher_last_code.innerText = localStorage.getItem("voucher_last_code")
  return res;
}

function formatVoucherSpeed(kbps) {
    if (kbps === 0) {
        return "Unlimited";
    }

    const mbps = kbps / 1000;

    // Trim trailing .0 (e.g. 25.0 -> "25 Mbps", but keep 12.5 -> "12.5 Mbps")
    const formatted = mbps % 1 === 0 ? mbps.toFixed(0) : mbps.toFixed(1);

    return `${formatted} Mbps`;
}

function formatVoucherDuration(duration) {
  const MIN_PER_HOUR = 60;
  const MIN_PER_DAY = 60 * 24;

  const days = Math.floor(duration / MIN_PER_DAY);
  const remainderAfterDays = duration % MIN_PER_DAY;

  const hours = Math.floor(remainderAfterDays / MIN_PER_HOUR);
  const mins = remainderAfterDays % MIN_PER_HOUR;

  const parts = [];

  if (days > 0) {
      parts.push(days + (days === 1 ? " day" : " days"));
  }
  if (hours > 0) {
      parts.push(hours + (hours === 1 ? " hr" : " hrs"));
  }
  if (mins > 0) {
      parts.push(mins + (mins === 1 ? " min" : " mins"));
  }

  return parts.length > 0 ? parts.join(" ") : "0 min";
}

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
function displayMessage_(res){
  mac_message.innerHTML = ""
    mac_message.insertAdjacentHTML("afterbegin", 
      `<div class="alert-`+res.status+` p-3 pb-2 rounded-3 mb-2">`+
          `<div class="d-flex justify-content-between">`+
              `<h6 class="f-13"><span class="fa fa-wifi"></span> `+res.controller+`</h6>`+
              `<span onclick="this.parentNode.parentNode.remove()" class="fa fa-remove"></span>`+
          `</div>`+
          `<h6 class="ms-4 f-i f-13 unifi-message">`+res.message+`</h6>`+
      `</div>`
    )

}
loadLastMAC()

function loadLastMAC(){
  if(sessionStorage.getItem("last_mac_address") !== null){
    mac_address_.innerHTML = ""
    var mac_ = sessionStorage.getItem("last_mac_address").split("+++")
    mac_.forEach(mac => {
      mac_address_.insertAdjacentHTML("beforeend",
        `<option>${mac}</option>`
      )
    })
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

    voucher_location.innerHTML    = ""
    var opt_building          = document.createElement("option")
    opt_building.value        = ""
    opt_building.innerText    = "-- Select Site / Location --"
    opt_building.disabled     = true
    opt_building.selected     = true
    voucher_location.appendChild(opt_building)

    Building.forEach(bldg_ => {
      var opt                 = document.createElement("option")
      opt.value               = bldg_[0]
      opt.innerText           = bldg_[0]
      mac_location.appendChild(opt)

      var opt                 = document.createElement("option")
      opt.value               = bldg_[0]
      opt.innerText           = bldg_[0]
      voucher_location.appendChild(opt)
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

  voucher_location.addEventListener("change", e => {
    voucher_project.innerHTML     = ""
    var opt_project           = document.createElement("option")
    opt_project.value         = ""
    opt_project.innerText     = "-- Select Project / Office --"
    opt_project.disabled      = true
    opt_project.selected      = true
    voucher_project.appendChild(opt_project)
    Building.forEach(bldg_ => {
      if(bldg_[0] == voucher_location.value){
        bldg_[1][bldg_[0]].Project.forEach(project => {
          var opt             = document.createElement("option")
          opt.value           = project
          opt.innerText       = project
          voucher_project.appendChild(opt)
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

um_login_userid.value = "703F"
um_login_password.value = "311660"
um_login_btn.click()