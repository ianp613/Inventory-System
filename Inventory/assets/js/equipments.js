if(document.getElementById("equipments")){
    let entryTable = new DataTable('#equipment_table',{
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
           sLengthMenu: "Show _MENU_entries",
        //    search: "Search: "
           search: "<button hidden id=\"for_status_btn\" data-bs-toggle=\"modal\" data-bs-target=\"#for_status\" style=\"margin-right: 20px; padding-left: 10px;\" class=\"btn btn-sm btn-secondary rounded-pill position-relative\"><span id=\"for_status_count\" class=\"position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger\"></span><span class=\" fa fa-file-pdf-o\"></span> For Status</button>   Search: "
        }
    });

    loadPage();
    
    const add_equipment_modal = new bootstrap.Modal(document.getElementById('add_equipment'),unclose);
    const edit_equipment_modal = new bootstrap.Modal(document.getElementById('edit_equipment'),unclose);
    const delete_equipment_modal = new bootstrap.Modal(document.getElementById('delete_equipment'),unclose);
    const add_entry_modal = new bootstrap.Modal(document.getElementById('add_entry'),unclose);
    const delete_entry_modal = new bootstrap.Modal(document.getElementById('delete_entry'),unclose);
    const edit_entry_modal = new bootstrap.Modal(document.getElementById('edit_entry'),unclose);
    const for_status_modal = new bootstrap.Modal(document.getElementById('for_status'),unclose);
    const barcode_camera_modal = new bootstrap.Modal(document.getElementById('barcode_camera'),unclose);
    var add_entry_title = document.getElementById('add_entry_title');
    var delete_equipment_btn = document.getElementById('delete_equipment_btn')
    var delete_equipment_name = document.getElementById('delete_equipment_name')
    var delete_equipment_btn_proceed = document.getElementById('delete_equipment_btn_proceed')
    // for_status_modal.show()

    // FOCUS ADD EQUIPMENT INPUT
    var add_equipment = document.getElementById('add_equipment')
    var add_equipment_select = document.getElementById('add_equipment_select')
    var add_equipment_input = document.getElementById('add_equipment_input')
    var add_equipment_btn = document.getElementById('add_equipment_btn')

    var barcode_scanner_btn = document.getElementById("barcode_scanner_btn");
    var barcode_scanner_btn_edit = document.getElementById("barcode_scanner_btn_edit");
    var cancel_barcode_scanner_btn = document.getElementById("cancel_barcode_scanner_btn");

    sole.get("../../controllers/get_list.php")
    .then(res => {
        res.equipment.forEach(e => {
            var op = document.createElement("option")
            op.value = e
            op.innerText = e
            add_equipment_select.appendChild(op)
        });
    })

    add_equipment.addEventListener('shown.bs.modal', function () {
        add_equipment_select.focus()
    })

    // POST ADD EQUIPMENT
    add_equipment_btn.addEventListener("click", function () {
        sole.post("../../controllers/equipments/add_equipment.php", {
            uid: localStorage.getItem("userid"),
            name: add_equipment_input.value ? add_equipment_input.value : add_equipment_select.value
        }).then(res => validateResponse(res,"add_equipment"))
    })

    // FOCUS EDIT EQUIPMENT INPUT
    var edit_equipment = document.getElementById('edit_equipment')
    var edit_equipment_input = document.getElementById('edit_equipment_input')
    var edit_equipment_btn = document.getElementById('edit_equipment_btn')
    var edit_equipment_input_temp = ""

    edit_equipment.addEventListener('shown.bs.modal', function () {
        edit_equipment_input_temp = edit_equipment_input.value
        edit_equipment_input.focus()
    })

    // POST EDIT EQUIPMENT
    edit_equipment_btn.addEventListener("click", function () {
        sole.post("../../controllers/equipments/edit_equipment.php", {
            id: edit_equipment_input.getAttribute("eid"),
            name: edit_equipment_input.value
        }).then(res => validateResponse(res,"edit_equipment"))
    })

    // ADD ENTRY FOCUS
    var add_entry = document.getElementById('add_entry')
    var add_entry_description_input = document.getElementById('add_entry_description_input')
    var add_entry_btn = document.getElementById('add_entry_btn')
    var add_entry_modal_btn = document.getElementById('add_entry_modal_btn')

    add_entry_modal_btn.addEventListener("click",function(){
        if(localStorage.getItem("selected_equipment") != null){
            add_entry_modal.show()
        }else{
            bs5.toast("warning","Please select equipment first.")
        }
    })

    add_entry.addEventListener('shown.bs.modal', function () {
        add_entry_title.innerText = "Add Entry to " + localStorage.getItem("selected_equipment")
        add_entry_description_input.focus()
    })

    // EDIT ENTRY FOCUS
    var edit_entry = document.getElementById('edit_entry')
    var edit_entry_description_input = document.getElementById('edit_entry_description_input')
    var edit_entry_btn = document.getElementById('edit_entry_btn')
    var edit_entry_title = document.getElementById('edit_entry_title')

    edit_entry.addEventListener('shown.bs.modal', function () {
        edit_entry_description_input.focus()
    })

    var add_entry_description_input = document.getElementById('add_entry_description_input')
    var add_entry_model_no_input = document.getElementById('add_entry_model_no_input')
    var add_entry_barcode_input = document.getElementById('add_entry_barcode_input')
    var add_entry_specifications_input = document.getElementById('add_entry_specifications_input')
    var add_entry_status_input = document.getElementById('add_entry_status_input')
    var add_entry_remarks_input = document.getElementById('add_entry_remarks_input')

    add_entry_btn.addEventListener("click", function () {
        if(add_entry_description_input.value){
            if(localStorage.getItem("selected_equipment")){
                sole.post("../../controllers/equipments/add_entry.php", {
                    uid: localStorage.getItem("userid"),
                    eid: localStorage.getItem("selected_equipment_id"),
                    description: add_entry_description_input.value,
                    model_no: add_entry_model_no_input.value,
                    barcode: add_entry_barcode_input.value,
                    specifications: add_entry_specifications_input.value,
                    status: add_entry_status_input.value,
                    remarks: add_entry_remarks_input.value
                }).then(res => validateResponse(res,"add_entry"))
            }else{
                bs5.toast("warning","Please select equipment first.")
            }
        }else{
            bs5.toast("warning","Please input description.")
        }
    })

    document.getElementById("for_status_btn").addEventListener("click",function(){

    })

    // TOGGLE EDIT EQUIPMENT MODAL
    var equipment_dropdown = document.getElementById("equipment_dropdown");
    var equipment_dropdown_toggle = document.getElementById("equipment_dropdown_toggle");
    equipment_dropdown.addEventListener("contextmenu", e=>{
        if(e.target.classList.contains("dropdown-item")){
            const edit_equipment_input = document.getElementById("edit_equipment_input");
            edit_equipment_modal.show();
            edit_equipment_input.value = e.target.innerText
            edit_equipment_input.setAttribute("eid",e.target.getAttribute("id"))
            delete_equipment_name.innerText = e.target.innerText
            delete_equipment_btn.setAttribute("eid",e.target.getAttribute("id"))
            edit_equipment_input.focus()
        }
    })

    // SELECT EQUIPMENT
    equipment_dropdown.addEventListener("click", e=>{
        if(e.target.classList.contains("dropdown-item")){
            equipment_dropdown_toggle.innerText = e.target.innerText
            localStorage.setItem("selected_equipment", e.target.innerText);
            localStorage.setItem("selected_equipment_id", e.target.getAttribute("id"));
            add_entry_title.innerText = "Add Entry to " + localStorage.getItem("selected_equipment")
            sole.post("../../controllers/equipments/get_entry.php", {
                eid: localStorage.getItem("selected_equipment_id")
            }).then(res => loadEntry(res))
        }
    })

    // DELETE EQUIPMENT MODAL
    delete_equipment_btn.addEventListener("click",function(){
        delete_equipment_modal.show()
        edit_equipment_modal.hide()
    })

    delete_equipment_btn_proceed.addEventListener("click",function(){
        sole.post("../../controllers/equipments/delete_equipment.php",{
            id: delete_equipment_btn.getAttribute("eid")
        }).then(res => validateResponse(res,"delete_equipment"))
    })

    // LOAD PAGE DATA
    function loadPage(){
        sole.get("../../controllers/equipments/get_equipment.php").then(res => loadEquipment(res))
        if(localStorage.getItem("selected_equipment")){
            sole.post("../../controllers/equipments/get_entry.php", {
                eid: localStorage.getItem("selected_equipment_id")
            }).then(res => loadEntry(res))
        }
    }
    function validateResponse(res,func){
        if(res.status){
            if(func == "edit_entry"){
                if(localStorage.getItem("selected_equipment")){
                    sole.post("../../controllers/equipments/get_entry.php", {
                        eid: localStorage.getItem("selected_equipment_id")
                    }).then(res => loadEntry(res))
                }
                edit_entry_modal.hide();
                edit_entry_description_input.value = ""
                edit_entry_model_no_input.value = ""
                add_entry_barcode_input.value = ""
                edit_entry_barcode_input.value = ""
                edit_entry_specifications_input.value = ""
                edit_entry_status_input.value = ""
                edit_entry_remarks_input.value = ""
            }
            if(func == "delete_entry"){
                if(localStorage.getItem("selected_equipment")){
                    sole.post("../../controllers/equipments/get_entry.php", {
                        eid: localStorage.getItem("selected_equipment_id")
                    }).then(res => loadEntry(res))
                }
            }
            if(func == "add_entry"){
                if(localStorage.getItem("selected_equipment")){
                    sole.post("../../controllers/equipments/get_entry.php", {
                        eid: localStorage.getItem("selected_equipment_id")
                    }).then(res => loadEntry(res))
                }
                add_entry_modal.hide();
                add_entry_description_input.value = ""
                add_entry_model_no_input.value = ""
                add_entry_barcode_input.value = ""
                add_entry_specifications_input.value = ""
                add_entry_status_input.value = ""
                add_entry_remarks_input.value = ""
            }
            if(func == "add_equipment"){
                add_equipment_input.value = ""
                add_equipment_select.value = ""
                sole.get("../../controllers/equipments/get_equipment.php").then(res => loadEquipment(res))
                add_equipment_modal.hide();
            }
            if(func == "edit_equipment"){
                if(edit_equipment_input_temp == localStorage.getItem("selected_equipment")){
                    equipment_dropdown_toggle.innerText = edit_equipment_input.value
                    localStorage.setItem("selected_equipment", edit_equipment_input.value);
                    localStorage.setItem("selected_equipment_id", edit_equipment_input.getAttribute("eid"));
                }
                edit_equipment_modal.hide();
                sole.get("../../controllers/equipments/get_equipment.php").then(res => loadEquipment(res))
            }
            if(func == "delete_equipment"){
                if(delete_equipment_name.innerText == localStorage.getItem("selected_equipment")){
                    equipment_dropdown_toggle.innerText = "-- Select Equipment --"
                    entryTable.clear().draw();
                    localStorage.removeItem("selected_equipment");
                    localStorage.removeItem("selected_equipment_id");
                }
                delete_equipment_modal.hide()
                sole.get("../../controllers/equipments/get_equipment.php").then(res => loadEquipment(res))
            }
            bs5.toast(res.type,res.message,res.size)
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }

    function loadEntry(res){
        var for_status_count = 0;
        entryTable.clear().draw();
        res.entry.forEach(e => {
            e["status"] == "For Status" ? for_status_count++ : null
            entryTable.row.add([
                e["id"],
                e["description"],
                e["model_no"] != "-" ? e["model_no"] : "",
                e["barcode"] != "-" ? e["barcode"] : "",
                e["status"] != "-" ? e["status"] : "",
                " <button id=\"edit_entry_"+ e["id"] +"\" e-id=\""+ e["id"] +"\" class=\"edit_entry_row btn btn-sm btn-secondary mb-1\"><i e-id=\""+ e["id"] +"\" class=\"edit_entry_row fa fa-edit\"></i></button>"+
                " <button id=\"delete_entry_"+ e["id"] +"\" e-id=\""+ e["id"] +"\" class=\"delete_entry_row btn btn-sm btn-danger mb-1\"><i e-id=\""+ e["id"] +"\" class=\"delete_entry_row fa fa-trash-o\"></i></button>"
            ]).draw(false)   
        });
        for_status_count ? document.getElementById("for_status_count").innerText = for_status_count : document.getElementById("for_status_count").innerText = ""
        
        document.querySelector('#equipment_table').addEventListener("click", e=>{
            if (e.target.classList.contains('delete_entry_row')) {
                let tr = "";
                if(e.target.tagName == "I"){
                    tr = e.target.parentNode.parentNode.parentNode.children
                }
                if(e.target.tagName == "BUTTON"){
                    tr = e.target.parentNode.parentNode.children    
                }
                document.getElementById("delete_entry_name").innerText = tr[0].innerText
                delete_entry_modal.show()
                let delete_entry_btn = document.getElementById("delete_entry_btn")
                delete_entry_btn.setAttribute("e-id",e.target.getAttribute("e-id"))
                delete_entry_btn.addEventListener("click", function(){
                    sole.delete("../../controllers/equipments/delete_entry.php",{
                        eid: localStorage.getItem("selected_equipment_id"),
                        description: tr[0].innerText,
                        id: delete_entry_btn.getAttribute("e-id")
                    }).then(res => validateResponse(res,"delete_entry"))
                    delete_entry_modal.hide()
                })
            }
        })
        let tr = document.getElementsByClassName("trow");
        for (let i = 0; i < tr.length; i++) {
            tr[i].addEventListener("contextmenu",function(){
                console.log(tr[i])
            })
        }
        document.querySelector('#equipment_table').addEventListener("click", e=>{
            if (e.target.classList.contains('edit_entry_row')) {
                let tr = "";
                if(e.target.tagName == "I"){
                    tr = e.target.parentNode.parentNode.parentNode.children
                }
                if(e.target.tagName == "BUTTON"){
                    tr = e.target.parentNode.parentNode.children    
                }
                edit_entry_title.innerText = "Edit Entry: " + tr[0].innerText
                edit_entry_btn.setAttribute("e-id",e.target.getAttribute("e-id"))
                sole.post("../../controllers/equipments/find_entry.php",{
                    id: e.target.getAttribute("e-id")
                }).then(res => editForm(res))
                edit_entry_btn.addEventListener("click", e =>{
                    if(edit_entry_description_input.value){
                        if(localStorage.getItem("selected_equipment")){
                            var id = null;
                            e.target.tagName == "SPAN" ? id = e.target.parentNode.getAttribute("e-id") : id = e.target.getAttribute("e-id")
                            sole.post("../../controllers/equipments/edit_entry.php",{
                                eid: localStorage.getItem("selected_equipment_id"),
                                id: id,
                                description: edit_entry_description_input.value,
                                model_no: edit_entry_model_no_input.value,
                                barcode: edit_entry_barcode_input.value,
                                specifications: edit_entry_specifications_input.value,
                                status: edit_entry_status_input.value,
                                remarks: edit_entry_remarks_input.value
                            }).then(res => validateResponse(res,"edit_entry"))
                        }else{
                            bs5.toast("warning","Please select equipment first.")
                        }
                    }else{
                        bs5.toast("warning","Please input description.")
                    }
                    
                })
            }
        })
    }

    // SET EDIT ENTRY FORM VALUE
    var edit_entry_description_input = document.getElementById('edit_entry_description_input')
    var edit_entry_model_no_input = document.getElementById('edit_entry_model_no_input')
    var edit_entry_barcode_input = document.getElementById('edit_entry_barcode_input')
    var edit_entry_specifications_input = document.getElementById('edit_entry_specifications_input')
    var edit_entry_status_input = document.getElementById('edit_entry_status_input')
    var edit_entry_remarks_input = document.getElementById('edit_entry_remarks_input')
    function editForm(res){
        if(res.status){
            edit_entry_description_input.value = res.entry[0].description
            edit_entry_model_no_input.value = res.entry[0].model_no != "-" ? res.entry[0].model_no : ""
            edit_entry_barcode_input.value = res.entry[0].barcode != "-" ? res.entry[0].barcode : ""
            edit_entry_specifications_input.value = res.entry[0].specifications != "-" ? res.entry[0].specifications : ""
            edit_entry_status_input.value = res.entry[0].status != "-" ? res.entry[0].status : "N/A"
            edit_entry_remarks_input.value = res.entry[0].remarks != "-" ? res.entry[0].remarks : ""
            edit_entry_modal.show()
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }

    function loadEquipment(res){
        if(equipment_dropdown_toggle.innerText == "-- Select Equipment --"){
            if (localStorage.getItem("selected_equipment") && res.equipments.length){
                equipment_dropdown_toggle.innerText = localStorage.getItem("selected_equipment")
            }else{
                if(res.equipments.length){
                    equipment_dropdown_toggle.innerText = res.equipments[0]["name"]
                    localStorage.setItem("selected_equipment", res.equipments[0]["name"]);
                    localStorage.setItem("selected_equipment_id", res.equipments[0]["id"]);
                }else{
                    localStorage.removeItem("selected_equipment");
                    localStorage.removeItem("selected_equipment_id");
                }
            }
        }
        if(localStorage.getItem("selected_equipment")){
            sole.post("../../controllers/equipments/get_entry.php", {
                eid: localStorage.getItem("selected_equipment_id")
            }).then(res => loadEntry(res))
        }
        equipment_dropdown.innerHTML = ""
        res.equipments.forEach(equipment => {
            equipment_dropdown.innerHTML += "<li><a href=\"#\" class=\"dropdown-item\" id=\""+ equipment["id"] +"\" >"+ equipment["name"] +"</a></li>"
        });
    }
    barcode_scanner_btn.addEventListener("click",function(){
        barcode_camera_modal.show()
        startBarcodeScanner()
    })
    barcode_scanner_btn_edit.addEventListener("click",function(){
        barcode_camera_modal.show()
        startBarcodeScanner()
    })
    cancel_barcode_scanner_btn.addEventListener("click",function(){
        barcode_camera_modal.hide();
        stopScanner();
    })

    let scannerRunning = false;

    async function startBarcodeScanner() {
    if (scannerRunning) return;

    // 🔐 Check camera support
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        console.log("Camera API not available. Use HTTPS or localhost.");
        return;
    }

    // 🔓 Force permission prompt (Brave fix)
    try {
        await navigator.mediaDevices.getUserMedia({ video: true });
    } catch (err) {
        console.error("Camera permission denied:", err);
        alert("Camera permission is required.");
        return;
    }

    Quagga.init({
        inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector('#scanner'),
        constraints: {
            facingMode: { ideal: "environment" }, // rear camera
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }
        },
        decoder: {
            readers: [
                "code_128_reader",
                "ean_reader",
                "ean_8_reader"
            ]
        },
        locate: true
    }, function(err) {
        if (err) {
            console.error("Quagga init error:", err);
            return;
        }
        Quagga.start();
        scannerRunning = true;
    });

    Quagga.onDetected(onDetectedHandler);
    }

    function stopScanner() {
    if (!scannerRunning) return;

    Quagga.stop();
    Quagga.offDetected(onDetectedHandler);
    scannerRunning = false;
    }

    function onDetectedHandler(result) {
    const code = result.codeResult.code;
    console.log("Scanned:", code);

    document.getElementById('barcodeInput').value = code;

    stopScanner(); // stop after success
    }
}