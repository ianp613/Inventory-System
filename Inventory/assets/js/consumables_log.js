var link = "";
var g_name_display = document.getElementById("g_name_display");
var g_search = document.getElementById("g_search");
var g_id = null;
var cid = null;
var remaining_stock = null;


var log_consumables_code = document.getElementById("log_consumables_code")
var log_consumables_stock = document.getElementById("log_consumables_stock")
var log_consumables_description = document.getElementById("log_consumables_description")

var requested_quantity = document.getElementById("requested_quantity");
var date_today = document.getElementById("date_today");
var time_today = document.getElementById("time_today");
var remarks = document.getElementById("remarks");
var user_id = document.getElementById("user_id");
var passkey = document.getElementById("passkey");

var cancel_btn = document.getElementById("cancel_btn");
var submit_btn = document.getElementById("submit_btn");

var log_consumable_badge_danger = document.getElementById("log_consumable_badge_danger")
var log_consumable_badge_warning = document.getElementById("log_consumable_badge_warning")
var log_consumable_badge_success = document.getElementById("log_consumable_badge_success")

loadPage()
setdatetime()

function loadPage() {
    link = window.location.href.split("glog=")
    if(link.length){
        sole.post("../../controllers/consumables/consumables_log/get_link_data.php",{
            link: link[1]
        }).then(res => {
            if(res.status){
                g_name_display.innerHTML = "<span class=\"fa fa-users wd-30\"></span> " + res.g_name
                g_name_display.classList.add("me-2")
                g_id = res.g_id
            }else{
                bs5.toast("warning", "<code> The link provided is invalid. Please contact your supervisor to obtain a valid link. Redirecting to home page <span class='fa fa-spin fa-spinner'></span></code>","lg",false,false)
                locationRedirect()
            }
        })    
    }else{
        locationRedirect()
    }
}

g_search.addEventListener("input", e => {
    search()
})

submit_btn.addEventListener("click", e => {
    if(!cid){
        alert("Please select an item first.")
        return
    }
    if(!requested_quantity.value){
        alert("Please input a valid quantity.")
        return
    }
    if(!remarks.value){
        alert("Please input remarks.")
        return
    }
    if(!user_id.value || !passkey.value){
        alert("Please input User ID and Passkey.")
        return
    }
    sole.post("../../controllers/consumables/consumables_log/save_request.php",{
        gid : g_id,
        cid : cid,
        date_today : date_today.value,
        time_today : time_today.value,
        remarks : remarks.value,
        requested_quantity : requested_quantity.value,
        user_id : user_id.value,
        passkey : passkey.value
    }).then(res => {
        if(res.status){
            bs5.toast("success",res.message)
            cancel_btn.click()
        }else{
            alert(res.message)
        }
    })

})

cancel_btn.addEventListener("click", e => {
    g_search.value = ""
    search()
    setdatetime()
    requested_quantity.value = 0
    remarks.value = ""
    user_id.value = ""
    passkey.value = ""
})

requested_quantity.addEventListener("input",function(){
    if(/^0+\d/.test(requested_quantity.value)) {
        requested_quantity.value = requested_quantity.value.replace(/^0+(?=\d)/,    '');
    }
    if(requested_quantity.value < 0){
        requested_quantity.value = 0
    }
    if(!requested_quantity.value){
        requested_quantity.value = 0
    }
})

function search(){
    sole.post("../../controllers/consumables/consumables_log/search_consumable.php",{
        search: g_search.value,
        g_id: g_id,
        link: link[1]
    }).then(res => {
        if(res.status){
            if(res.data.length && g_search.value){
                log_consumables_code.innerText = res.data[0].code
                log_consumables_description.innerText = res.data[0].description
                log_consumables_stock.innerText = res.data[0].stock

                remaining_stock = res.data[0].stock
                cid = res.data[0].id

                if(parseFloat(res.data[0].stock) <= parseFloat(res.data[0].restock_point) && parseFloat(res.data[0].stock) != 0){
                    log_consumable_badge_warning.hidden = false
                    log_consumable_badge_danger.hidden = true
                    log_consumable_badge_success.hidden = true
                }else if(parseFloat(res.data[0].stock) == 0){
                    log_consumable_badge_warning.hidden = true
                    log_consumable_badge_danger.hidden = false
                    log_consumable_badge_success.hidden = true
                }else{
                    log_consumable_badge_warning.hidden = true
                    log_consumable_badge_danger.hidden = true
                    log_consumable_badge_success.hidden = false
                }
            }else{
                remaining_stock = null
                cid = null

                log_consumables_code.innerText = ""
                log_consumables_description.innerText = ""
                log_consumables_stock.innerText = ""
                log_consumable_badge_danger.hidden = true
                log_consumable_badge_success.hidden = true
            }    
        }else{
            bs5.toast("warning", "<code> The link provided is invalid. Please contact your supervisor to obtain a valid link. Redirecting to home page <span class='fa fa-spin fa-spinner'></span></code>","lg",false,false)
            locationRedirect()
        }
        
    }) 
}

function locationRedirect(){
    setTimeout(() => {
        window.location.replace("../index.php");
    }, 5000);
}

setInterval(() => {
    setdatetime()
}, 1000);

function setdatetime(){
    const date = new Date();
    date_today.valueAsDate = date;

    const formatTime = (date) => {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    };

    time_today.value = formatTime(date);    
}
