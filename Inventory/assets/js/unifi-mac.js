var mac_ssid      = document.getElementById("mac_ssid")
var mac_address   = document.getElementById("mac_address")
var mac_name      = document.getElementById("mac_name")
var mac_device    = document.getElementById("mac_device")
var mac_project   = document.getElementById("mac_project")
var mac_location  = document.getElementById("mac_location")
var mac_remarks   = document.getElementById("mac_remarks")
var mac_message   = document.getElementById("mac_message")

var clear_btn     = document.getElementById("clear_btn")
var register_mac  = document.getElementById("register_mac")
var loading_mac   = document.getElementById("loading_mac")

async function GetWifi(params) {
  await sole.get("../controllers/unifi-mac/get-wifi.php").then(res => {
    res.wifis.forEach(wifi => {
      var opt                   = document.createElement("option")
      opt.value                 = wifi.id
      opt.innerText             = wifi.name
      mac_ssid.appendChild(opt)
    });
    localStorage.setItem("unifi_mac_gid",res.g_id)
  })
}

mac_address.addEventListener("input",function(){
    this.value = this.value.replace(/[^a-zA-Z0-9:]/g, "")
    if(this.value){
        var str       = this.value.replace(/:/g, "")
        str           = str.match(/.{1,2}/g)
        this.value    = str.join(":").toLowerCase()
    }
})

clear_btn.addEventListener("click", e => {
  mac_address.value   = ""
  mac_ssid.value      = ""
  mac_name.value      = ""
  mac_device.value    = ""
  mac_project.value   = ""
  mac_location.value  = ""
  mac_remarks.value   = ""
})

register_mac.addEventListener("click", e => {
  if(!mac_address.value){
    alert("Please input mac address.")
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




  register_mac.hidden   = true
  loading_mac.hidden      = false
  sole.post("../controllers/unifi-mac/register-mac.php",{
    mac_address   : mac_address.value,
    mac_ssid      : mac_ssid.value,
    mac_name      : mac_name.value,
    mac_device    : mac_device.value,
    mac_project   : mac_project.value,
    mac_location  : mac_location.value,
    mac_remarks   : mac_remarks.value,
    g_id          : localStorage.getItem("unifi_mac_gid")
  }).then(res => {
    mac_message.innerHTML = ""
    if(res.site.length){
      for (let i = 0; i < res.site.length; i++) {
        mac_message.insertAdjacentHTML("afterbegin", 
          `<div class="alert-`+res.status[i]+` p-3 pb-2 rounded-3 mb-2">`+
              `<div class="d-flex justify-content-between">`+
                  `<h6 class="f-13"><span class="fa fa-wifi"></span> `+res.site[i]+`</h6>`+
                  `<span onclick="this.parentNode.parentNode.remove()" class="fa fa-remove"></span>`+
              `</div>`+
              `<h6 class="ms-4 f-i f-13">`+res.message[i]+`</h6>`+
          `</div>`
        )
      }
    }
    register_mac.hidden   = false
    loading_mac.hidden      = true
    clear_btn.click()
  })

})

GetWifi()













































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