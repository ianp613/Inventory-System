if(document.getElementById("mac")){
    let macTable = new DataTable('#wifi_table',{
        order: [[5, 'asc']],
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        scrollX: true,
        columnDefs: [
            {
                target: 0,
                visible: false,
                searchable: false
            },
            { 
                className: 'dt-left', 
                targets: '_all' 
            }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries <button id=\"add_mac_entry_modal_btn\" class=\"btn btn-sm btn-dark\"><span class=\"fa fa-plus\"></span> Add Entry</button>",
           search: "<button data-bs-toggle=\"modal\" data-bs-target=\"#wip_modal\" id=\"ip_import\" style=\"margin-right: 10px; padding-left: 10px;\" class=\"btn btn-sm btn-secondary rounded-pill position-relative\"><span class=\" fa fa-upload\"></span> Import</button><button data-bs-toggle=\"modal\" data-bs-target=\"#wip_modal\" id=\"ip_export\" style=\"margin-right: 10px; padding-left: 10px;\" class=\"btn btn-sm btn-secondary rounded-pill position-relative\"><span class=\" fa fa-download\"></span> Export</button>   Search: "
        }
    });

    loadPage();
    // LOAD PAGE DATA
    function loadPage(){
        sole.get("../../controllers/mac/get_wifi.php").then(res => loadWifi(res))
        if(localStorage.getItem("selected_wifi")){
            sole.post("../../controllers/mac/get_mac.php", {
                wid: localStorage.getItem("selected_wifi_id")
            }).then(res => loadMAC(res))
        }
    }

    const add_wifi_modal = new bootstrap.Modal(document.getElementById('add_wifi'),unclose);
    const edit_wifi_modal = new bootstrap.Modal(document.getElementById('edit_wifi'),unclose);
    const delete_wifi_modal = new bootstrap.Modal(document.getElementById('delete_wifi'),unclose);

    const add_mac_entry_modal = new bootstrap.Modal(document.getElementById('add_mac'),unclose);
    const edit_mac_entry_modal = new bootstrap.Modal(document.getElementById('edit_mac'),unclose);
    const delete_mac_entry_modal = new bootstrap.Modal(document.getElementById('delete_mac'),unclose);

    var add_wifi = document.getElementById("add_wifi")
    var edit_wifi = document.getElementById("edit_wifi")
    var add_wifi_btn = document.getElementById("add_wifi_btn")
    var wifi_name = document.getElementById("wifi_name")
    var wifi_password = document.getElementById("wifi_password")

    var edit_wifi_btn = document.getElementById("edit_wifi_btn")
    var edit_wifi_name = document.getElementById("edit_wifi_name")
    var edit_wifi_password = document.getElementById("edit_wifi_password")
    var edit_wifi_name_temp = ""

    var delete_wifi_name = document.getElementById("delete_wifi_name")
    var delete_wifi_btn = document.getElementById("delete_wifi_btn")

    var delete_ready_state_wifi = document.getElementById("delete_ready_state_wifi")
    var delete_saving_state_wifi = document.getElementById("delete_saving_state_wifi")

    var add_mac_entry_modal_btn = document.getElementById("add_mac_entry_modal_btn")
    var add_mac = document.getElementById("add_mac")
    var add_mac_entry_title = document.getElementById("add_mac_entry_title")

    var edit_mac = document.getElementById("edit_mac")
    var edit_mac_entry_title = document.getElementById("edit_mac_entry_title")

    var mac_address = document.getElementById("mac_address")
    var mac_name = document.getElementById("mac_name")
    var mac_device = document.getElementById("mac_device")
    var mac_project = document.getElementById("mac_project")
    var mac_location = document.getElementById("mac_location")
    var mac_remarks = document.getElementById("mac_remarks")
    var add_mac_entry_btn = document.getElementById("add_mac_entry_btn")

    var edit_mac_address = document.getElementById("edit_mac_address")
    var edit_mac_name = document.getElementById("edit_mac_name")
    var edit_mac_device = document.getElementById("edit_mac_device")
    var edit_mac_project = document.getElementById("edit_mac_project")
    var edit_mac_location = document.getElementById("edit_mac_location")
    var edit_mac_remarks = document.getElementById("edit_mac_remarks")
    var edit_mac_entry_btn = document.getElementById("edit_mac_entry_btn")

    var delete_mac_address = document.getElementById("delete_mac_address")
    var delete_mac_btn = document   .getElementById("delete_mac_btn")


    var wifi_dropdown = document.getElementById("wifi_dropdown")
    var wifi_dropdown_toggle = document.getElementById("wifi_dropdown_toggle")

    add_wifi.addEventListener('shown.bs.modal', function () {
        wifi_name.focus()
    })
    edit_wifi.addEventListener('shown.bs.modal', function () {
        edit_wifi_name_temp = edit_wifi_name.value
        edit_wifi_name.focus()
    })
    add_mac.addEventListener('shown.bs.modal', function () {
        add_mac_entry_title.innerText = "Add Entry to " + localStorage.getItem("selected_wifi")
        mac_address.focus()
    })
    edit_mac.addEventListener('shown.bs.modal', function () {
        edit_mac_address.focus()
    })

    add_wifi_btn.addEventListener("click",function(){
        sole.post("../../controllers/mac/add_wifi.php", {
            uid: localStorage.getItem("userid"),
            wifi_name: wifi_name.value,
            wifi_password: wifi_password.value,
        }).then(res => validateResponse(res,"add_wifi"))
    })

    edit_wifi_btn.addEventListener("click",function(){
        !edit_wifi_name.value ? bs5.toast("warning","Please provide wifi name.") : null

        sole.post("../../controllers/mac/edit_wifi.php",{
            id: edit_wifi_btn.getAttribute("wid"),
            wifi_name: edit_wifi_name.value,
            wifi_password: edit_wifi_password.value,
        }).then(res => validateResponse(res,"edit_wifi")) 
    })

    // POST DELETE NETWOK
    delete_wifi_btn.addEventListener("click",function(){
        delete_ready_state_wifi.style = "display: none;"
        delete_saving_state_wifi.style = "display: flex;"
        sole.post("../../controllers/mac/delete_wifi.php",{
            id: this.getAttribute("wid")
        }).then(res => validateResponse(res,"delete_wifi"))
    })

    add_mac_entry_modal_btn.addEventListener("click",function(){
        if(localStorage.getItem("selected_wifi") != null){
            if(localStorage.getItem("selected_wifi").toLowerCase() != "show all"){
                add_mac_entry_modal.show()
            }else{
                bs5.toast("warning","Please select wifi first.")
            }
        }else{
            bs5.toast("warning","Please select wifi first.")
        }
    })

    // SELECT WIFI
    wifi_dropdown.addEventListener("click", e=>{
        if(e.target.classList.contains("dropdown-item")){
            wifi_dropdown_toggle.innerText = e.target.innerText
            localStorage.setItem("selected_wifi", e.target.innerText);
            localStorage.setItem("selected_wifi_id", e.target.getAttribute("id"));
            macTable.clear().draw();
            sole.post("../../controllers/mac/get_mac.php", {
                wid: localStorage.getItem("selected_wifi_id")
            }).then(res => loadMAC(res))
        }
    })

    // TOGGLE EDIT WIFI MODAL
    wifi_dropdown.addEventListener("contextmenu", e=>{
        if(e.target.innerText.toLowerCase() != "show all"){
            if(e.target.classList.contains("dropdown-item")){
                sole.post("../../controllers/mac/find_wifi.php",{
                    id: e.target.getAttribute("id")
                }).then(res => editwifiForm(res))
            }    
        }
    })

    mac_address.addEventListener("input",function(){
        this.value = this.value.replace(/[^a-zA-Z0-9:]/g, "")
        if(this.value){
            var str = this.value.replace(/:/g, "")
            str = str.match(/.{1,2}/g)
            this.value = str.join(":").toLowerCase()
        }
    })
    edit_mac_address.addEventListener("input",function(){
        this.value = this.value.replace(/[^a-zA-Z0-9:]/g, "")
        if(this.value){
            var str = this.value.replace(/:/g, "")
            str = str.match(/.{1,2}/g)
            this.value = str.join(":").toLowerCase()
        }
    })
    // mac_address.addEventListener("change",function(){
    //     this.value = this.value.replace(/[^a-zA-Z0-9:]/g, "")
    //     if(this.value){
    //         var str = this.value.replace(/:/g, "")
    //         str = str.match(/.{1,2}/g)
    //         this.value = str.join(":")
    //     }
    // })

    add_mac_entry_btn.addEventListener("click",function(){
        if(mac_address.value && mac_address.value.length == 17){
            sole.post("../../controllers/mac/add_mac.php",{
                uid: localStorage.getItem("userid"),
                wid: localStorage.getItem("selected_wifi_id"),
                mac: mac_address.value,
                name: mac_name.value,
                device: mac_device.value,
                project: mac_project.value,
                location: mac_location.value,
                remarks: mac_remarks.value
            }).then(res => {
                if(res.status){
                    validateResponse(res,"add_mac")
                }else{
                    alert(res.message)
                }
            })    
        }else{
            bs5.toast("warning","Please input a valid MAC address.")
        }
        
    })

    edit_mac_entry_btn.addEventListener("click",function(){
        if(edit_mac_address.value && edit_mac_address.value.length == 17){
            sole.post("../../controllers/mac/edit_mac.php",{
                id: this.getAttribute("m-id"),
                mac: edit_mac_address.value,
                name: edit_mac_name.value,
                device: edit_mac_device.value,
                project: edit_mac_project.value,
                location: edit_mac_location.value,
                remarks: edit_mac_remarks.value
            }).then(res => {
                if(res.status){
                    validateResponse(res,"edit_mac")
                }else{
                    alert(res.message)
                }
            })    
        }else{
            bs5.toast("warning","Please input a valid MAC address.")
        }
    })

    delete_mac_btn.addEventListener("click",function(){
        sole.post("../../controllers/mac/delete_mac.php",{
            wid: localStorage.getItem("selected_wifi_id"),
            id: this.getAttribute("m-id")
        }).then(res => validateResponse(res,"delete_mac"))
    })

    function editwifiForm(res){
        if(res.status){
            edit_wifi_name.value = res.wifi[0].name
            edit_wifi_password.value = res.wifi[0].password != "-" ? res.wifi[0].password : ""
            edit_wifi_btn.setAttribute("wid",res.wifi[0].id)
            delete_wifi_name.innerText = res.wifi[0].name
            delete_wifi_btn.setAttribute("wid",res.wifi[0].id)
            edit_wifi_modal.show()
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }

    function editMACForm(res){
        edit_mac_address.value = res.mac[0].mac
        edit_mac_name.value = res.mac[0].name != "-" ? res.mac[0].name : ""
        edit_mac_device.value = res.mac[0].device != "-" ? res.mac[0].device : ""
        edit_mac_project.value = res.mac[0].project != "-" ? res.mac[0].project : ""
        edit_mac_location.value = res.mac[0].location != "-" ? res.mac[0].location : ""
        edit_mac_remarks.value = res.mac[0].remarks != "-" ? res.mac[0].remarks : ""
        edit_mac_entry_modal.show()
    }

    function loadMAC(res){
        var mac_count = 0
        var mac_record = document.getElementById("mac_record")
        macTable.clear().draw();
        res.mac.forEach(e => {
            mac_count++
            macTable.row.add([
                e["id"],
                e["mac"],
                e["name"] != "-" ? e["name"] : "",
                e["device"] != "-" ? e["device"] : "",
                e["project"] != "-" ? e["project"] : "",
                e["location"] != "-" ? e["location"] : "",
                " <button id=\"edit_mac_"+ e["id"] +"\" m-id=\""+ e["id"] +"\" class=\"edit_mac_row btn btn-sm btn-secondary mb-1\"><i m-id=\""+ e["id"] +"\" class=\"edit_mac_row fa fa-edit\"></i></button>"+
                " <button id=\"delete_mac_"+ e["id"] +"\" m-id=\""+ e["id"] +"\" class=\"delete_mac_row btn btn-sm btn-danger mb-1\"><i m-id=\""+ e["id"] +"\" class=\"delete_mac_row fa fa-trash-o\"></i></button>"
            ]).draw(false) 
        });
        mac_record.innerText = "MAC Address: " + mac_count

        document.querySelector('#wifi_table').addEventListener("click", e=>{
            let tr = "";
            if(e.target.tagName == "I"){
                tr = e.target.parentNode.parentNode.parentNode.children
            }
            if(e.target.tagName == "BUTTON"){
                tr = e.target.parentNode.parentNode.children    
            }
            if(e.target.classList.contains('edit_mac_row')) {
                edit_mac_entry_title.innerText = "Edit MAC Address: " + tr[0].innerText.toUpperCase()
                edit_mac_entry_btn.setAttribute("m-id",e.target.getAttribute("m-id"))
                sole.post("../../controllers/mac/find_mac.php",{
                    id: e.target.getAttribute("m-id")
                }).then(res => editMACForm(res))
            }
            if(e.target.classList.contains('delete_mac_row')){
                delete_mac_address.innerText = tr[0].innerText
                delete_mac_btn.setAttribute("m-id",e.target.getAttribute("m-id"))
                delete_mac_entry_modal.show()
            }
        })
    }


    function loadWifi(res){
        if(wifi_dropdown_toggle.innerText == "-- Select Wifi --"){
            if (localStorage.getItem("selected_wifi") && res.wifis.length){
                wifi_dropdown_toggle.innerText = localStorage.getItem("selected_wifi")
            }else{
                if(res.wifis.length){
                    wifi_dropdown_toggle.innerText = res.wifis[0]["name"]
                    localStorage.setItem("selected_wifi", res.wifis[0]["name"]);
                    localStorage.setItem("selected_wifi_id", res.wifis[0]["id"]);
                }else{
                    localStorage.removeItem("selected_wifi");
                    localStorage.removeItem("selected_wifi_id");
                }
            }
        }
        if(localStorage.getItem("selected_wifi")){
            sole.post("../../controllers/mac/get_mac.php", {
                wid: localStorage.getItem("selected_wifi_id")
            }).then(res => loadMAC(res))
        }
        wifi_dropdown.innerHTML = ""
        res.wifis.forEach(wifi => {
            wifi_dropdown.innerHTML += "<li><a href=\"#\" class=\"dropdown-item\" id=\""+ wifi["id"] +"\" >"+ wifi["name"] +"</a></li>"
        });
    }

    function validateResponse(res, func){
        if(func == "delete_wifi"){
            delete_ready_state_wifi.style = ""
            delete_saving_state_wifi.style = "display: none;"
        }
        if(res.status){
            if(func == "add_wifi"){
                wifi_name.value = ""
                wifi_password.value = ""
                add_wifi_modal.hide()
                sole.get("../../controllers/mac/get_wifi.php").then(res => loadWifi(res))
            }
            if(func == "edit_wifi"){
                if(edit_wifi_name_temp == localStorage.getItem("selected_wifi")){
                    wifi_dropdown_toggle.innerText = edit_wifi_name.value
                    localStorage.setItem("selected_wifi", edit_wifi_name.value);
                    localStorage.setItem("selected_wifi_id", edit_wifi_btn.getAttribute("wid"));
                }
                edit_wifi_modal.hide()
                sole.get("../../controllers/mac/get_wifi.php").then(res => loadWifi(res))
            }
            if(func == "delete_wifi"){
                if(delete_wifi_name.innerText == localStorage.getItem("selected_wifi") || localStorage.getItem("selected_wifi").toLowerCase() == "show all"){
                    wifi_dropdown_toggle.innerText = "-- Select Wifi --"
                    macTable.clear().draw();
                    localStorage.removeItem("selected_wifi");
                    localStorage.removeItem("selected_wifi_id");
                }
                delete_wifi_modal.hide()
                sole.get("../../controllers/mac/get_wifi.php").then(res => loadWifi(res))
            }
            if(func == "add_mac"){
                if(localStorage.getItem("selected_wifi")){
                    sole.post("../../controllers/mac/get_mac.php", {
                        wid: localStorage.getItem("selected_wifi_id")
                    }).then(res => loadMAC(res))
                }
                mac_address.value = ""
                mac_name.value = ""
                mac_device.value = "Cellphone"
                mac_project.value = ""
                mac_location.value = ""
                mac_remarks.value = ""
                add_mac_entry_modal.hide()
            }
            if(func == "edit_mac"){
                if(localStorage.getItem("selected_wifi")){
                    sole.post("../../controllers/mac/get_mac.php", {
                        wid: localStorage.getItem("selected_wifi_id")
                    }).then(res => loadMAC(res))
                }
                edit_mac_address.value = ""
                edit_mac_name.value = ""
                mac_device.value = "Cellphone"
                edit_mac_project.value = ""
                edit_mac_location.value = ""
                edit_mac_remarks.value = ""
                edit_mac_entry_modal.hide()
            }
            if(func == "delete_mac"){
                if(localStorage.getItem("selected_wifi")){
                    sole.post("../../controllers/mac/get_mac.php", {
                        wid: localStorage.getItem("selected_wifi_id")
                    }).then(res => loadMAC(res))
                }
                delete_mac_entry_modal.hide()
            }
            bs5.toast(res.type,res.message,res.size)
        }else{
            bs5.toast(res.type,res.message,res.size)    
        }
    }
}