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

    var Buildings = [];
    var add_location_building = document.getElementById("add_location_building")
    var add_location_building_others = document.getElementById("add_location_building_others")
    var add_location_room = document.getElementById("add_location_room")
    var add_location_room_others = document.getElementById("add_location_room_others")
    var add_location_project = document.getElementById("add_location_project")
    var add_location_project_others = document.getElementById("add_location_project_others")
    var add_location_cabinet = document.getElementById("add_location_cabinet")

    sole.get("../../controllers/equipments/get_equipment_location_preset.php").then(res => {
        add_location_building.innerHTML = ""

        var opt_building = document.createElement("option")
        opt_building.value = ""
        opt_building.innerText = "-- Select Building --"
        opt_building.disabled = true
        opt_building.selected = true
        add_location_building.appendChild(opt_building)

        res.Building.forEach(bldg => {
            var opt_building = document.createElement("option")
            opt_building.value = Object.keys(bldg)[0]
            opt_building.innerText = Object.keys(bldg)[0]
            add_location_building.appendChild(opt_building)
            Buildings.push(bldg)
        })

        var opt_building = document.createElement("option")
        opt_building.value = "Others"
        opt_building.innerText = "Others"
        add_location_building.appendChild(opt_building)
        

        add_location_building.addEventListener("change",e => {
            if(add_location_building.value && add_location_building.value != "Others"){
                add_location_room.disabled = false
                add_location_project.disabled = false
                
                add_location_building_others.value = ""
                add_location_room.innerHTML = ""
                add_location_project.innerHTML = ""

                var opt_room = document.createElement("option")
                opt_room.value = ""
                opt_room.innerText = "-- Select Room --"
                opt_room.disabled = true
                opt_room.selected = true
                add_location_room.appendChild(opt_room)

                var opt_project = document.createElement("option")
                opt_project.value = ""
                opt_project.innerText = "-- Select Project / Office --"
                opt_project.disabled = true
                opt_project.selected = true
                add_location_project.appendChild(opt_project)

                Buildings.forEach(bldgs => {
                    let key = Object.keys(bldgs)[0]
                    if(key == add_location_building.value){
                        bldgs[key].Room.forEach(room => {
                            var opt_room = document.createElement("option")
                            opt_room.value = room
                            opt_room.innerText = room
                            add_location_room.appendChild(opt_room)
                        });

                        bldgs[key].Project.forEach(project => {
                            var opt_project = document.createElement("option")
                            opt_project.value = project
                            opt_project.innerText = project
                            add_location_project.appendChild(opt_project)
                        });
                    }
                });

                var opt_room = document.createElement("option")
                opt_room.value = "Others"
                opt_room.innerText = "Others"
                add_location_room.appendChild(opt_room)

                var opt_project = document.createElement("option")
                opt_project.value = "Others"
                opt_project.innerText = "Others"
                add_location_project.appendChild(opt_project)
            }
            if(add_location_building.value == "Others"){
                add_location_room.value = "Others"
                add_location_room.disabled = true
                add_location_project.value = "Others"
                add_location_project.disabled = true
            }
        })
    })
    
    add_location_building_others.addEventListener("input", e => {
        if(add_location_building_others.value){
            add_location_building.value = "Others"
            add_location_room.value = "Others"
            add_location_room.disabled = true
            add_location_project.value = "Others"
            add_location_project.disabled = true
        }else{
            add_location_building.value = ""
            add_location_room.value = ""
            add_location_room.disabled = false
            add_location_project.value = ""
            add_location_project.disabled = false
        }
    })

    add_location_room.addEventListener("change", e => {
        if(add_location_room.value && add_location_room.value != "Others"){
            add_location_room_others.value = ""
        }
    })

    add_location_room_others.addEventListener("input", e => {
        if(add_location_room_others.value){
            add_location_room.value = "Others"
        }else{
            if(add_location_building.value != "Others"){
                add_location_room.value = ""
            }
        }
    })

    add_location_project.addEventListener("change", e => {
        if(add_location_project.value && add_location_project.value != "Others"){
            add_location_project_others.value = ""
        }
    })

    add_location_project_others.addEventListener("input", e => {
        if(add_location_project_others.value){
            add_location_project.value = "Others"
        }else{
            if(add_location_building.value != "Others"){
                add_location_project.value = ""
            }
        }
    })

    var Buildings_edit = []
    var edit_location_building = document.getElementById("edit_location_building")
    var edit_location_building_others = document.getElementById("edit_location_building_others")
    var edit_location_room = document.getElementById("edit_location_room")
    var edit_location_room_others = document.getElementById("edit_location_room_others")
    var edit_location_project = document.getElementById("edit_location_project")
    var edit_location_project_others = document.getElementById("edit_location_project_others")
    var edit_location_cabinet = document.getElementById("edit_location_cabinet")

    sole.get("../../controllers/equipments/get_equipment_location_preset.php").then(res => {
        edit_location_building.innerHTML = ""

        var opt_building = document.createElement("option")
        opt_building.value = ""
        opt_building.innerText = "-- Select Building --"
        opt_building.disabled = true
        opt_building.selected = true
        edit_location_building.appendChild(opt_building)

        res.Building.forEach(bldg => {
            var opt_building = document.createElement("option")
            opt_building.value = Object.keys(bldg)[0]
            opt_building.innerText = Object.keys(bldg)[0]
            edit_location_building.appendChild(opt_building)
            Buildings_edit.push(bldg)
        })

        var opt_building = document.createElement("option")
        opt_building.value = "Others"
        opt_building.innerText = "Others"
        edit_location_building.appendChild(opt_building)
        

        edit_location_building.addEventListener("change",e => {
            if(edit_location_building.value && edit_location_building.value != "Others"){
                edit_location_room.disabled = false
                edit_location_project.disabled = false
                
                edit_location_building_others.value = ""
                edit_location_room.innerHTML = ""
                edit_location_project.innerHTML = ""

                var opt_room = document.createElement("option")
                opt_room.value = ""
                opt_room.innerText = "-- Select Room --"
                opt_room.disabled = true
                opt_room.selected = true
                edit_location_room.appendChild(opt_room)

                var opt_project = document.createElement("option")
                opt_project.value = ""
                opt_project.innerText = "-- Select Project / Office --"
                opt_project.disabled = true
                opt_project.selected = true
                edit_location_project.appendChild(opt_project)

                Buildings_edit.forEach(bldgs => {
                    let key = Object.keys(bldgs)[0]
                    if(key == edit_location_building.value){
                        bldgs[key].Room.forEach(room => {
                            var opt_room = document.createElement("option")
                            opt_room.value = room
                            opt_room.innerText = room
                            edit_location_room.appendChild(opt_room)
                        });

                        bldgs[key].Project.forEach(project => {
                            var opt_project = document.createElement("option")
                            opt_project.value = project
                            opt_project.innerText = project
                            edit_location_project.appendChild(opt_project)
                        });
                    }
                });

                var opt_room = document.createElement("option")
                opt_room.value = "Others"
                opt_room.innerText = "Others"
                edit_location_room.appendChild(opt_room)

                var opt_project = document.createElement("option")
                opt_project.value = "Others"
                opt_project.innerText = "Others"
                edit_location_project.appendChild(opt_project)
            }
            if(edit_location_building.value == "Others"){
                edit_location_room.value = "Others"
                edit_location_room.disabled = true
                edit_location_project.value = "Others"
                edit_location_project.disabled = true
            }
        })
    })
    
    edit_location_building_others.addEventListener("input", e => {
        if(edit_location_building_others.value){
            edit_location_building.value = "Others"
            edit_location_room.value = "Others"
            edit_location_room.disabled = true
            edit_location_project.value = "Others"
            edit_location_project.disabled = true
        }else{
            edit_location_building.value = ""
            edit_location_room.value = ""
            edit_location_room.disabled = false
            edit_location_project.value = ""
            edit_location_project.disabled = false
        }
    })

    edit_location_room.addEventListener("change", e => {
        if(edit_location_room.value && edit_location_room.value != "Others"){
            edit_location_room_others.value = ""
        }
    })

    edit_location_room_others.addEventListener("input", e => {
        if(edit_location_room_others.value){
            edit_location_room.value = "Others"
        }else{
            if(edit_location_building.value != "Others"){
                edit_location_room.value = ""
            }
        }
    })

    edit_location_project.addEventListener("change", e => {
        if(edit_location_project.value && edit_location_project.value != "Others"){
            edit_location_project_others.value = ""
        }
    })

    edit_location_project_others.addEventListener("input", e => {
        if(edit_location_project_others.value){
            edit_location_project.value = "Others"
        }else{
            if(edit_location_building.value != "Others"){
                edit_location_project.value = ""
            }
        }
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
        var building = "-";
        var room = "-";
        var project = "-";
        if(add_location_building.value && add_location_building.value != "Others"){
            building = add_location_building.value
        }else if(add_location_building.value == "Others"){
            building = add_location_building_others.value
        }
        if(add_location_room.value && add_location_room.value != "Others"){
            room = add_location_room.value
        }else if(add_location_room.value == "Others"){
            room = add_location_room_others.value
        }
        if(add_location_project.value && add_location_project.value != "Others"){
            project = add_location_project.value
        }else if(add_location_project.value == "Others"){
            project = add_location_project_others.value
        }
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
                    building: building,
                    room: room,
                    project: project,
                    cabinet: add_location_cabinet.value,
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
                (e["building"] != "-" ? e["building"] : "") + (e["room"] != "-" ? " - " + e["room"] : "") + (e["project"] != "-" ? " - " + e["project"] : ""),
                e["status"] != "-" ? e["status"] : "",
                " <button id=\"edit_entry_"+ e["id"] +"\" e-id=\""+ e["id"] +"\" class=\"edit_entry_row btn btn-sm btn-secondary mb-1\"><i e-id=\""+ e["id"] +"\" class=\"edit_entry_row fa fa-edit\"></i></button>"+
                " <button id=\"delete_entry_"+ e["id"] +"\" e-id=\""+ e["id"] +"\" class=\"delete_entry_row btn btn-sm btn-danger mb-1\"><i e-id=\""+ e["id"] +"\" class=\"delete_entry_row fa fa-trash-o\"></i></button>"
            ]).draw(false)   
        });
        for_status_count ? document.getElementById("for_status_count").innerText = for_status_count : document.getElementById("for_status_count").innerText = ""
        
        // let tr = document.getElementsByClassName("trow");
        // for (let i = 0; i < tr.length; i++) {
        //     tr[i].addEventListener("contextmenu",function(){
        //         console.log(tr[i])
        //     })
        // }
    }

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
                var building = "-";
                var room = "-";
                var project = "-";
                if(edit_location_building.value && edit_location_building.value != "Others"){
                    building = edit_location_building.value
                }else if(edit_location_building.value == "Others"){
                    building = edit_location_building_others.value
                }
                if(edit_location_room.value && edit_location_room.value != "Others"){
                    room = edit_location_room.value
                }else if(edit_location_room.value == "Others"){
                    room = edit_location_room_others.value
                }
                if(edit_location_project.value && edit_location_project.value != "Others"){
                    project = edit_location_project.value
                }else if(edit_location_project.value == "Others"){
                    project = edit_location_project_others.value
                }
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
                            building: building,
                            room: room,
                            project: project,
                            cabinet: edit_location_cabinet.value,
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

            let Building_ = [];

            Buildings_edit.forEach(bldg => {
                if(Object.keys(bldg)[0] == res.entry[0].building){
                    Building_.push(bldg)
                }
            })


            if(Building_.length){
                edit_location_building_others.value = ""
                edit_location_room.disabled = false
                edit_location_project.disabled = false
                edit_location_building.value = res.entry[0].building != "-" ? res.entry[0].building : ""
                edit_location_room.innerHTML = ""
                edit_location_project.innerHTML = ""
                
                var opt_room = document.createElement("option")
                opt_room.value = ""
                opt_room.innerText = "-- Select Room --"
                opt_room.disabled = true
                opt_room.selected = true
                edit_location_room.appendChild(opt_room)

                var opt_room = document.createElement("option")
                opt_room.value = ""
                opt_room.innerText = "-- Select Project / Office --"
                opt_room.disabled = true
                opt_room.selected = true
                edit_location_project.appendChild(opt_room)


                var Room_ = "";
                Building_[0][Object.keys(Building_[0])[0]].Room.forEach(room => {
                    var opt_room = document.createElement("option")
                    opt_room.value = room
                    opt_room.innerText = room
                    edit_location_room.appendChild(opt_room)
                    if(room.toLowerCase() == res.entry[0].room.toLowerCase()){
                        Room_ = room
                    }
                })

                var Project_ = "";
                Building_[0][Object.keys(Building_[0])[0]].Project.forEach(proj => {
                    var opt_project = document.createElement("option")
                    opt_project.value = proj
                    opt_project.innerText = proj
                    edit_location_project.appendChild(opt_project)
                    if(proj.toLowerCase() == res.entry[0].project.toLowerCase()){
                        Project_ = proj
                    }
                })

                var opt_room = document.createElement("option")
                opt_room.value = "Others"
                opt_room.innerText = "Others"
                edit_location_room.appendChild(opt_room)

                var opt_project = document.createElement("option")
                opt_project.value = "Others"
                opt_project.innerText = "Others"
                edit_location_project.appendChild(opt_project)
                
                if(res.entry[0].room != "-"){
                    if(Building_[0][Object.keys(Building_[0])[0]].Room.some(v => v.toLowerCase() === res.entry[0].room.toLowerCase())){
                        edit_location_room.value = Room_
                        edit_location_room_others.value = ""
                    }else{
                        edit_location_room.value = "Others"
                        edit_location_room_others.value = res.entry[0].room
                    }
                }else{
                    edit_location_room.value = ""
                    edit_location_room_others.value = ""
                }

                if(res.entry[0].project != "-"){

                    if(Building_[0][Object.keys(Building_[0])[0]].Project.some(v => v.toLowerCase() === res.entry[0].project.toLowerCase())){
                        edit_location_project.value = Project_
                        edit_location_project_others.value = ""
                    }else{
                        edit_location_project.value = "Others"
                        edit_location_project_others.value = res.entry[0].project
                    }
                }else{
                    edit_location_project.value = ""
                    edit_location_project_others.value = ""
                }
            }else{
                if(res.entry[0].building != "-"){
                    edit_location_building_others.value = res.entry[0].building != "-" ? res.entry[0].building : ""
                    edit_location_room_others.value = res.entry[0].room != "-" ? res.entry[0].room : ""
                    edit_location_project_others.value = res.entry[0].project != "-" ? res.entry[0].project : ""

                    edit_location_building.value = "Others"
                    edit_location_room.value = "Others"
                    edit_location_room.disabled = true
                    edit_location_project.value = "Others"
                    edit_location_project.disabled = true
                }else{
                    edit_location_building_others.value = res.entry[0].building != "-" ? res.entry[0].building : ""

                    if(res.entry[0].room != "-"){
                        edit_location_room.value = "Others"
                        edit_location_room_others.value = res.entry[0].room
                    }else{
                        edit_location_room.value = ""
                        edit_location_room_others.value = ""
                    }

                    if(res.entry[0].project != "-"){
                        edit_location_project.value = "Others"
                        edit_location_project_others.value = res.entry[0].project
                    }else{
                        edit_location_project.value = ""
                        edit_location_project_others.value = ""
                    }

                    edit_location_building.value = ""
                    edit_location_room.disabled = false
                    edit_location_project.disabled = false
                }
            }
            
            edit_location_cabinet.value = res.entry[0].cabinet != "-" ? res.entry[0].cabinet : ""
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
        add_entry_modal.hide()
        startScanner()
    })
    barcode_scanner_btn_edit.addEventListener("click",function(){
        barcode_camera_modal.show()
        add_entry_modal.hide()
        startScanner()
    })
    cancel_barcode_scanner_btn.addEventListener("click",function(){
        barcode_camera_modal.hide()
        add_entry_modal.show()
        stopScanner();
    })


    let scanner = null;
    let running = false;
    let firstScan = true; // flag for first detection

    function onScanSuccess(decodedText) {
        add_entry_barcode_input.value = decodedText;
        console.log("Scanned:", decodedText);

        barcode_camera_modal.hide();
        add_entry_modal.show();
        stopScanner();

        // Shrink scan frame after first detection
        if (firstScan && scanner) {
            scanner.setQrBox({ width: 250, height: 100 }); // smaller frame
            firstScan = false;
        }
    }

    function enableAutoFocus() {
        try {
            const track = scanner.getRunningTrack();
            const capabilities = track.getCapabilities();

            if (capabilities.focusMode) {
                track.applyConstraints({
                    advanced: [{ focusMode: "continuous" }]
                });
                console.log("Auto focus enabled");
            }
        } catch (e) {
            console.log("Focus not supported");
        }
    }

    async function startScanner() {
        if (running) return;

        // check camera support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.log("Camera API not available. Use HTTPS or localhost.");
            return;
        }

        try {
            await navigator.mediaDevices.getUserMedia({ video: true });
        } catch (err) {
            console.error("Camera permission denied:", err);
            alert("Camera permission is required.");
            return;
        }

        running = true;
        document.getElementById("scanner").disabled = true;

        // remove old video
        document.getElementById("scanner").innerHTML = "";

        firstScan = true; // reset flag

        scanner = new Html5Qrcode("scanner");

        const formatsToSupport = [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.ITF
        ];

        scanner.start(
            { facingMode: "environment" },
            {
                fps: 12,
                qrbox: (videoWidth, videoHeight) => {
                    // Make the frame 80% of the video width and 80% of the height
                    const width = videoWidth * 0.8;
                    const height = videoHeight * 0.8;
                    return { width, height };
                }, // wide initial scan line
                formatsToSupport: formatsToSupport
            },
            onScanSuccess
        ).then(() => {
            enableAutoFocus();
        }).catch(err => {
            console.error(err);
            running = false;
        });
    }

    function stopScanner() {
        if (scanner && running) {
            scanner.stop().then(() => {
                scanner.clear();
                running = false;
                console.log("Scanner stopped");
            });
        }
    }

    // tap to refocus
    document.getElementById("scanner").addEventListener("click", () => {
        try {
            if (!scanner) return;
            const track = scanner.getRunningTrack();
            const capabilities = track.getCapabilities();

            if (capabilities.focusMode) {
                track.applyConstraints({
                    advanced: [{ focusMode: "single-shot" }]
                });
                console.log("Tap focus");
            }
        } catch (e) {
            console.log("Tap focus not supported");
        }
    });


    // let scannerRunning = false;

    // async function startBarcodeScanner() {
    // if (scannerRunning) return;

    // // 🔐 Check camera support
    // if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    //     console.log("Camera API not available. Use HTTPS or localhost.");
    //     return;
    // }

    // // 🔓 Force permission prompt (Brave fix)
    // try {
    //     await navigator.mediaDevices.getUserMedia({ video: true });
    // } catch (err) {
    //     console.error("Camera permission denied:", err);
    //     alert("Camera permission is required.");
    //     return;
    // }

    // Quagga.init({
    //     inputStream: {
    //     name: "Live",
    //     type: "LiveStream",
    //     target: document.querySelector('#scanner'),
    //     constraints: {
    //         facingMode: { ideal: "environment" }, // rear camera
    //         width: { ideal: 1280 },
    //         height: { ideal: 720 }
    //     }
    //     },
    //     decoder: {
    //         readers: [
    //             "code_128_reader",
    //             "ean_reader",
    //             "ean_8_reader"
    //         ]
    //     },
    //     locate: true
    // }, function(err) {
    //     if (err) {
    //         console.error("Quagga init error:", err);
    //         return;
    //     }
    //         Quagga.start();
    //         scannerRunning = true;
    //     });

    //     Quagga.onDetected(onDetectedHandler);
    // }

    // function stopScanner() {
    //     if (!scannerRunning) return;

    //     Quagga.stop();
    //     Quagga.offDetected(onDetectedHandler);
    //     scannerRunning = false;
    //     barcode_camera_modal.hide()
    // }

    // function onDetectedHandler(result) {
    //     const code = result.codeResult.code;
    //     console.log("Scanned:", code);

    //     add_entry_barcode_input.value = code;

    //     stopScanner(); // stop after success
    // }
}