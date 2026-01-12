var link = "";
var g_name_display = document.getElementById("g_name_display");
var g_search = document.getElementById("g_search");
var g_id = null;
var cid = null;
var remaining_stock = null;


var log_consumables_code = document.getElementById("log_consumables_code")
var log_consumables_stock = document.getElementById("log_consumables_stock")
var log_consumables_description = document.getElementById("log_consumables_description")

var quantity_deduction = document.getElementById("quantity_deduction");
var date_today = document.getElementById("date_today");
var time_today = document.getElementById("time_today");
var remarks = document.getElementById("remarks");
var user_id = document.getElementById("user_id");
var passkey = document.getElementById("passkey");

var cancel_btn = document.getElementById("cancel_btn");
var submit_btn = document.getElementById("submit_btn");


var log_consumable_badge_danger = document.getElementById("log_consumable_badge_danger")
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
                g_name_display.innerHTML = "<span class=\"fa fa-users\"></span> " + res.g_name + " |"
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
    if(cid){
        if(quantity_deduction.value){
            if(user_id.value && passkey.value){
                sole.post("../../controllers/consumables/consumables_log/save_log.php",{
                    gid : g_id,
                    cid : cid,
                    date_today : date_today.value,
                    time_today : time_today.value,
                    remarks : remarks.value,
                    quantity_deduction : quantity_deduction.value,
                    user_id : user_id.value,
                    passkey : passkey.value
                }).then(res => {
                    alert(res.message)
                    if(res.status){
                        cancel_btn.click()
                    }
                })
            }else{
                alert("Please input User ID and Passkey.")
            }
        }else{
            alert("Please input a valid quantity.")
        }
    }else{
        alert("Please select an item first.")
    }
})

cancel_btn.addEventListener("click", e => {
    g_search.value = ""
    search()
    setdatetime()
    quantity_deduction.value = 0
    remarks.value = ""
    user_id.value = ""
    passkey.value = ""
})

quantity_deduction.addEventListener("input",function(){
    if(/^0+\d/.test(quantity_deduction.value)) {
        quantity_deduction.value = quantity_deduction.value.replace(/^0+(?=\d)/,    '');
    }
    if(quantity_deduction.value < 0){
        quantity_deduction.value = 0
    }
    if(!quantity_deduction.value){
        quantity_deduction.value = 0
    }
})

function search(){
    sole.post("../../controllers/consumables/consumables_log/search_consumable.php",{
        search: g_search.value,
        g_id: g_id
    }).then(res => {
        console.log(res)
        if(res.length && g_search.value){
            log_consumables_code.innerText = res[0].code
            log_consumables_description.innerText = res[0].description
            log_consumables_stock.innerText = res[0].stock

            remaining_stock = res[0].stock
            cid = res[0].id

            if(parseFloat(res[0].stock) <= parseFloat(res[0].restock_point)){
                log_consumable_badge_danger.hidden = false
                log_consumable_badge_success.hidden = true
            }else{
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
    }) 
}

function locationRedirect(){
    setTimeout(() => {
        window.location.replace("../index.php");
    }, 5000);
}

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
