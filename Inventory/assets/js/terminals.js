if(document.getElementById("terminals")){
    const add_terminal                         = new bootstrap.Modal(document.getElementById('add_terminal'),unclose);
    let terminalTable = new DataTable('#tb_terminals',{
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        scrollX: true,
        columnDefs: [
            { targets: '_all', className: 'dt-nowrap' },
            {
                targets: 0,
                visible: false,
                searchable: false
            },
            {
                className: 'dt-left',
                targets: '_all'
            },
            {
            targets: -1,   // last column
            render: function(data, type, row) {
                return `
                    <button class="btn btn-sm btn-secondary btn-edit" data-id="${row[0]}">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="${row[0]}">
                        <i class="fa fa-trash"></i>
                    </button>
                `;
            }
        }
            
        ],
        autoWidth: true,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "<button id=\"add_router_btn\" data-bs-toggle=\"modal\" data-bs-target=\"#add_terminal\" class=\"btn btn-sm btn-dark me-3\"><span class=\"fa fa-plus\"></span> Add Terminal</button> Search: "
        }
    });

    callAllAddField()
    function callAllAddField(){
        var cpu_add_field                           = document.getElementById("cpu_add_field")
        var cpu_field                               = 1
        cpu_add_field.addEventListener("click", e => {
            cpu_field++
            cpu_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${cpu_field}" id="" class="form-control mt-4 cpu_model" placeholder="CPU-${cpu_field} Model">`+
                `<input type="text" name="${cpu_field}" id="" class="form-control mt-2 cpu_barcode" placeholder="CPU-${cpu_field} Barcode"></input>`
            )
        })

        var ram_add_field                           = document.getElementById("ram_add_field")
        var ram_field                               = 1
        ram_add_field.addEventListener("click", e => {
            ram_field++
            ram_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${ram_field}" id="" class="form-control mt-4 ram_model" placeholder="RAM-${ram_field} Model">`+
                `<input type="text" name="${ram_field}" id="" class="form-control mt-2 ram_barcode" placeholder="RAM-${ram_field} Barcode"></input>`
            )
        })

        var storage_add_field                       = document.getElementById("storage_add_field")
        var storage_field                           = 1
        storage_add_field.addEventListener("click", e => {
            storage_field++
            storage_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${storage_field}" id="" class="form-control mt-4 storage_model" placeholder="Storage-${storage_field} Model">`+
                `<input type="text" name="${storage_field}" id="" class="form-control mt-2 storage_barcode" placeholder="Storage-${storage_field} Barcode"></input>`
            )
        })

        var psu_add_field                           = document.getElementById("psu_add_field")
        var psu_field                               = 1
        psu_add_field.addEventListener("click", e => {
            psu_field++
            psu_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${psu_field}" id="" class="form-control mt-4 psu_model" placeholder="PSU-${psu_field} Model">`+
                `<input type="text" name="${psu_field}" id="" class="form-control mt-2 psu_barcode" placeholder="PSU-${psu_field} Barcode"></input>`
            )
        })

        var gpu_add_field                           = document.getElementById("gpu_add_field")
        var gpu_field                               = 1
        gpu_add_field.addEventListener("click", e => {
            gpu_field++
            gpu_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${gpu_field}" id="" class="form-control mt-4 gpu_model" placeholder="GPU-${gpu_field} Model">`+
                `<input type="text" name="${gpu_field}" id="" class="form-control mt-2 gpu_barcode" placeholder="GPU-${gpu_field} Barcode"></input>`
            )
        })

        var cs_add_field                            = document.getElementById("cs_add_field")
        var cs_field                                = 1
        cs_add_field.addEventListener("click", e => {
            cs_field++
            cs_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${cs_field}" id="" class="form-control mt-4 cs_model" placeholder="CS-${cs_field} Model">`+
                `<input type="text" name="${cs_field}" id="" class="form-control mt-2 cs_barcode" placeholder="CS-${cs_field} Barcode"></input>`
            )
        })

        var ec_add_field                            = document.getElementById("ec_add_field")
        var ec_field                                = 1
        ec_add_field.addEventListener("click", e => {
            ec_field++
            ec_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${ec_field}" id="" class="form-control mt-4 ec_model" placeholder="EC-${ec_field} Model">`+
                `<input type="text" name="${ec_field}" id="" class="form-control mt-2 ec_barcode" placeholder="EC-${ec_field} Barcode"></input>`
            )
        })

        var id_add_field                            = document.getElementById("id_add_field")
        var id_field                                = 1
        id_add_field.addEventListener("click", e => {
            id_field++
            id_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${id_field}" id="" class="form-control mt-4 id_type" placeholder="ID-${id_field} Type">`+
                `<input type="text" name="${id_field}" id="" class="form-control mt-2 id_model" placeholder="ID-${id_field} Model">`+
                `<input type="text" name="${id_field}" id="" class="form-control mt-2 id_barcode" placeholder="ID-${id_field} Barcode"></input>`
            )
        })

        var od_add_field                            = document.getElementById("od_add_field")
        var od_field                                = 1
        od_add_field.addEventListener("click", e => {
            od_field++
            od_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${od_field}" id="" class="form-control mt-4 od_type" placeholder="OD-${od_field} Type">`+
                `<input type="text" name="${od_field}" id="" class="form-control mt-2 od_model" placeholder="OD-${od_field} Model">`+
                `<input type="text" name="${od_field}" id="" class="form-control mt-2 od_barcode" placeholder="OD-${od_field} Barcode"></input>`
            )
        })    

        var sp_add_field                            = document.getElementById("sp_add_field")
        var sp_field                                = 1
        sp_add_field.addEventListener("click", e => {
            sp_field++
            sp_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${sp_field}" id="" class="form-control mt-4 sp_type" placeholder="SP-${sp_field} Type">`+
                `<input type="text" name="${sp_field}" id="" class="form-control mt-2 sp_model" placeholder="SP-${sp_field} Model">`+
                `<input type="text" name="${sp_field}" id="" class="form-control mt-2 sp_barcode" placeholder="SP-${sp_field} Barcode"></input>`
            )
        })

        var ups_battery_add_field                   = document.getElementById("ups_battery_add_field")
        var ups_battery_field                       = 1
        ups_battery_add_field.addEventListener("click", e => {
            ups_battery_field++
            ups_battery_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${ups_battery_field}" id="" class="form-control mt-4 ups_battery_model" placeholder="Batt-${ups_battery_field} Model">`+
                `<input type="text" name="${ups_battery_field}" id="" class="form-control mt-2 ups_battery_barcode" placeholder="Batt-${ups_battery_field} Barcode"></input>`
            )
        })    
    }
    

    getLocations()
    function getLocations(){
        var Buildings                               = [];
        var terminal_add_location_building          = document.getElementById("terminal_add_location_building")
        var terminal_add_location_building_others   = document.getElementById("terminal_add_location_building_others")
        var terminal_add_location_room              = document.getElementById("terminal_add_location_room")
        var terminal_add_location_room_others       = document.getElementById("terminal_add_location_room_others")
        var terminal_add_location_project           = document.getElementById("terminal_add_location_project")
        var terminal_add_location_project_others    = document.getElementById("terminal_add_location_project_others")


        sole.get("../../controllers/equipments/get_equipment_location_preset.php").then(res => {
            terminal_add_location_building.innerHTML = ""

            var opt_building = document.createElement("option")
            opt_building.value = ""
            opt_building.innerText = "-- Select Building --"
            opt_building.disabled = true
            opt_building.selected = true
            terminal_add_location_building.appendChild(opt_building)

            res.Building.forEach(bldg => {
                var opt_building = document.createElement("option")
                opt_building.value = Object.keys(bldg)[0]
                opt_building.innerText = Object.keys(bldg)[0]
                terminal_add_location_building.appendChild(opt_building)
                Buildings.push(bldg)
            })

            var opt_building = document.createElement("option")
            opt_building.value = "Others"
            opt_building.innerText = "Others"
            terminal_add_location_building.appendChild(opt_building)
            

            terminal_add_location_building.addEventListener("change",e => {
                if(terminal_add_location_building.value && terminal_add_location_building.value != "Others"){
                    terminal_add_location_room.disabled = false
                    terminal_add_location_project.disabled = false
                    
                    terminal_add_location_building_others.value = ""
                    terminal_add_location_room.innerHTML = ""
                    terminal_add_location_project.innerHTML = ""

                    var opt_room = document.createElement("option")
                    opt_room.value = ""
                    opt_room.innerText = "-- Select Room --"
                    opt_room.disabled = true
                    opt_room.selected = true
                    terminal_add_location_room.appendChild(opt_room)

                    var opt_project = document.createElement("option")
                    opt_project.value = ""
                    opt_project.innerText = "-- Select Project / Office --"
                    opt_project.disabled = true
                    opt_project.selected = true
                    terminal_add_location_project.appendChild(opt_project)

                    Buildings.forEach(bldgs => {
                        let key = Object.keys(bldgs)[0]
                        if(key == terminal_add_location_building.value){
                            bldgs[key].Room.forEach(room => {
                                var opt_room = document.createElement("option")
                                opt_room.value = room
                                opt_room.innerText = room
                                terminal_add_location_room.appendChild(opt_room)
                            });

                            bldgs[key].Project.forEach(project => {
                                var opt_project = document.createElement("option")
                                opt_project.value = project
                                opt_project.innerText = project
                                terminal_add_location_project.appendChild(opt_project)
                            });
                        }
                    });

                    var opt_room = document.createElement("option")
                    opt_room.value = "Others"
                    opt_room.innerText = "Others"
                    terminal_add_location_room.appendChild(opt_room)

                    var opt_project = document.createElement("option")
                    opt_project.value = "Others"
                    opt_project.innerText = "Others"
                    terminal_add_location_project.appendChild(opt_project)
                }
                if(terminal_add_location_building.value == "Others"){
                    terminal_add_location_room.value = "Others"
                    terminal_add_location_room.disabled = true
                    terminal_add_location_project.value = "Others"
                    terminal_add_location_project.disabled = true
                }
            })
        })    
    }
    
    
    terminal_add_location_building_others.addEventListener("input", e => {
        if(terminal_add_location_building_others.value){
            terminal_add_location_building.value = "Others"
            terminal_add_location_room.value = "Others"
            terminal_add_location_room.disabled = true
            terminal_add_location_project.value = "Others"
            terminal_add_location_project.disabled = true
        }else{
            terminal_add_location_building.value = ""
            terminal_add_location_room.value = ""
            terminal_add_location_room.disabled = false
            terminal_add_location_project.value = ""
            terminal_add_location_project.disabled = false
        }
    })

    terminal_add_location_room.addEventListener("change", e => {
        if(terminal_add_location_room.value && terminal_add_location_room.value != "Others"){
            terminal_add_location_room_others.value = ""
        }
    })

    terminal_add_location_room_others.addEventListener("input", e => {
        if(terminal_add_location_room_others.value){
            terminal_add_location_room.value = "Others"
        }else{
            if(terminal_add_location_building.value != "Others"){
                terminal_add_location_room.value = ""
            }
        }
    })

    terminal_add_location_project.addEventListener("change", e => {
        if(terminal_add_location_project.value && terminal_add_location_project.value != "Others"){
            terminal_add_location_project_others.value = ""
        }
    })

    terminal_add_location_project_others.addEventListener("input", e => {
        if(terminal_add_location_project_others.value){
            terminal_add_location_project.value = "Others"
        }else{
            if(terminal_add_location_building.value != "Others"){
                terminal_add_location_project.value = ""
            }
        }
    })


    var save_add_terminal                       = document.getElementById("save_add_terminal");
    var terminal_no                             = document.getElementById("terminal_no")
    var cabinet_no                              = document.getElementById("cabinet_no")
    var ip_address                              = document.getElementById("ip_address")
    var remarks_                                = document.getElementById("remarks_")
    var tech_recommendation                     = document.getElementById("tech_recommendation")
    var unit_type                               = document.getElementById("unit_type")
    var motherboard_model                       = document.getElementById("motherboard_model")
    var motherboard_barcode                     = document.getElementById("motherboard_barcode")
    var ups_brand                               = document.getElementById("ups_brand")
    var ups_casing_model                        = document.getElementById("ups_casing_model")
    var ups_casing_barcode                      = document.getElementById("ups_casing_barcode")
    var ups_status                              = document.getElementById("ups_status")
    var kaspersky                               = document.getElementById("kaspersky")
    var bitdefender                             = document.getElementById("bitdefender")
    var windows_update                          = document.getElementById("windows_update")
    var operating_system                        = document.getElementById("operating_system")
    var windows_license                         = document.getElementById("windows_license")

    save_add_terminal.addEventListener("click", e => {
        if(!terminal_no.value){
            bs5.toast("warning","Please input terminal no.")
            return
        }
        var cpu_combined            = ""
        var cpu_model               = document.getElementsByClassName("cpu_model")
        var cpu_barcode             = document.getElementsByClassName("cpu_barcode")
        for (let i = 0; i < cpu_model.length; i++) {
            if(cpu_model[i].value || cpu_barcode[i].value){
                cpu_combined += (cpu_model[i].value ? cpu_model[i].value : "NA")+"---"+(cpu_barcode[i].value ? cpu_barcode[i].value : "NA")
            }
            if(i < cpu_model.length-1 && (cpu_model[i+1].value || cpu_barcode[i+1].value)){
                cpu_combined += "+++"
            }
        }

        var ram_combined            = ""
        var ram_model               = document.getElementsByClassName("ram_model")
        var ram_barcode             = document.getElementsByClassName("ram_barcode")
        for (let i = 0; i < ram_model.length; i++) {
            if(ram_model[i].value || ram_barcode[i].value){
                ram_combined += (ram_model[i].value ? ram_model[i].value : "NA")+"---"+(ram_barcode[i].value ? ram_barcode[i].value : "NA")
            }
            if(i < ram_model.length-1 && (ram_model[i+1].value || ram_barcode[i+1].value)){
                ram_combined += "+++"
            }
        }

        var storage_combined        = ""
        var storage_model           = document.getElementsByClassName("storage_model")
        var storage_barcode         = document.getElementsByClassName("storage_barcode")
        for (let i = 0; i < storage_model.length; i++) {
            if(storage_model[i].value || storage_barcode[i].value){
                storage_combined += (storage_model[i].value ? storage_model[i].value : "NA")+"---"+(storage_barcode[i].value ? storage_barcode[i].value : "NA")
            }
            if(i < storage_model.length-1 && (storage_model[i+1].value || storage_barcode[i+1].value)){
                storage_combined += "+++"
            }
        }

        var psu_combined            = ""
        var psu_model               = document.getElementsByClassName("psu_model")
        var psu_barcode             = document.getElementsByClassName("psu_barcode")
        for (let i = 0; i < psu_model.length; i++) {
            if(psu_model[i].value || psu_barcode[i].value){
                psu_combined += (psu_model[i].value ? psu_model[i].value : "NA")+"---"+(psu_barcode[i].value ? psu_barcode[i].value : "NA")
            }
            if(i < psu_model.length-1 && (psu_model[i+1].value || psu_barcode[i+1].value)){
                psu_combined += "+++"
            }
        }

        var gpu_combined            = ""
        var gpu_model               = document.getElementsByClassName("gpu_model")
        var gpu_barcode             = document.getElementsByClassName("gpu_barcode")
        for (let i = 0; i < gpu_model.length; i++) {
            if(gpu_model[i].value || gpu_barcode[i].value){
                gpu_combined += (gpu_model[i].value ? gpu_model[i].value : "NA")+"---"+(gpu_barcode[i].value ? gpu_barcode[i].value : "NA")
            }
            if(i < gpu_model.length-1 && (gpu_model[i+1].value || gpu_barcode[i+1].value)){
                gpu_combined += "+++"
            }
        }

        var cs_combined            = ""
        var cs_model               = document.getElementsByClassName("cs_model")
        var cs_barcode             = document.getElementsByClassName("cs_barcode")
        for (let i = 0; i < cs_model.length; i++) {
            if(cs_model[i].value || cs_barcode[i].value){
                cs_combined += (cs_model[i].value ? cs_model[i].value : "NA")+"---"+(cs_barcode[i].value ? cs_barcode[i].value : "NA")
            }
            if(i < cs_model.length-1 && (cs_model[i+1].value || cs_barcode[i+1].value)){
                cs_combined += "+++"
            }
        }

        var ec_combined            = ""
        var ec_model               = document.getElementsByClassName("ec_model")
        var ec_barcode             = document.getElementsByClassName("ec_barcode")
        for (let i = 0; i < ec_model.length; i++) {
            if(ec_model[i].value || ec_barcode[i].value){
                ec_combined += (ec_model[i].value ? ec_model[i].value : "NA")+"---"+(ec_barcode[i].value ? ec_barcode[i].value : "NA")
            }
            if(i < ec_model.length-1 && (ec_model[i+1].value || ec_barcode[i+1].value)){
                ec_combined += "+++"
            }
        }

        var id_combined             = ""
        var id_type                 = document.getElementsByClassName("id_type")
        var id_model                = document.getElementsByClassName("id_model")
        var id_barcode              = document.getElementsByClassName("id_barcode")
        for (let i = 0; i < id_model.length; i++) {
            if(id_type[i].value || id_model[i].value || id_barcode[i].value){
                id_combined += (id_type[i].value ? id_type[i].value : "NA")+"---"+(id_model[i].value ? id_model[i].value : "NA")+"---"+(id_barcode[i].value ? id_barcode[i].value : "NA")
            }
            if(i < id_model.length-1 && (id_type[i+1].value || id_model[i+1].value || id_barcode[i+1].value)){
                id_combined += "+++"
            }
        }

        var od_combined             = ""
        var od_type                 = document.getElementsByClassName("od_type")
        var od_model                = document.getElementsByClassName("od_model")
        var od_barcode              = document.getElementsByClassName("od_barcode")
        for (let i = 0; i < od_model.length; i++) {
            if(od_type[i].value || od_model[i].value || od_barcode[i].value){
                od_combined += (od_type[i].value ? od_type[i].value : "NA")+"---"+(od_model[i].value ? od_model[i].value : "NA")+"---"+(od_barcode[i].value ? od_barcode[i].value : "NA")
            }
            if(i < od_model.length-1 && (od_type[i+1].value || od_model[i+1].value || od_barcode[i+1].value)){
                od_combined += "+++"
            }
        }

        var sp_combined             = ""
        var sp_type                 = document.getElementsByClassName("sp_type")
        var sp_model                = document.getElementsByClassName("sp_model")
        var sp_barcode              = document.getElementsByClassName("sp_barcode")
        for (let i = 0; i < sp_model.length; i++) {
            if(sp_type[i].value || sp_model[i].value || sp_barcode[i].value){
                sp_combined += (sp_type[i].value ? sp_type[i].value : "NA")+"---"+(sp_model[i].value ? sp_model[i].value : "NA")+"---"+(sp_barcode[i].value ? sp_barcode[i].value : "NA")
            }
            if(i < sp_model.length-1 && (sp_type[i+1].value || sp_model[i+1].value || sp_barcode[i+1].value)){
                sp_combined += "+++"
            }
        }

        var ups_battery_combined   = ""
        var ups_battery_model      = document.getElementsByClassName("ups_battery_model")
        var ups_battery_barcode    = document.getElementsByClassName("ups_battery_barcode")
        for (let i = 0; i < ups_battery_model.length; i++) {
            if(ups_battery_model[i].value || ups_battery_barcode[i].value){
                ups_battery_combined += (ups_battery_model[i].value ? ups_battery_model[i].value : "NA")+"---"+(ups_battery_barcode[i].value ? ups_battery_barcode[i].value : "NA")
            }
            if(i < ups_battery_model.length-1 && (ups_battery_model[i+1].value || ups_battery_barcode[i+1].value)){
                ups_battery_combined += "+++"
            }
        }

        var building_               = terminal_add_location_building_others.value ? terminal_add_location_building_others.value : terminal_add_location_building.value
        var room_                   = terminal_add_location_room_others.value ? terminal_add_location_room_others.value : terminal_add_location_room.value
        var project_                = terminal_add_location_project_others.value ? terminal_add_location_project_others.value : terminal_add_location_project.value

        if(!building_ || !room_ || !project_){
            bs5.toast("warning","Please select or input location.")
            return
        }

        sole.post("../../controllers/terminals/add_terminal.php", {
            uid                     : localStorage.getItem("userid"),
            terminal_no             : terminal_no.value,
            cabinet_no              : cabinet_no.value,
            ip_address              : ip_address.value,
            building                : building_,
            room                    : room_,
            project                 : project_,
            remarks                 : remarks_.value,
            tech_recommendation     : tech_recommendation.value,
            unit_type               : unit_type.value,
            motherboard_model       : motherboard_model.value,
            motherboard_barcode     : motherboard_barcode.value,
            cpu                     : cpu_combined,
            ram                     : ram_combined,
            storage                 : storage_combined,
            psu                     : psu_combined,
            gpu                     : gpu_combined,
            cs                      : cs_combined,
            ec                      : ec_combined,
            id                      : id_combined,
            od                      : od_combined,
            sp                      : sp_combined,
            ups_battery             : ups_battery_combined,
            ups_brand               : ups_brand.value,
            ups_casing_model        : ups_casing_model.value,
            ups_casing_barcode      : ups_casing_barcode.value,
            ups_status              : ups_status.value,
            kaspersky               : kaspersky.value,
            bitdefender             : bitdefender.value,
            windows_update          : windows_update.value,
            operating_system        : operating_system.value,
            windows_license         : windows_license.value
        }).then(res => {
            if(res[1].status){
                document.getElementById("modal_body_add_terminal").innerHTML = res[0]
                cpu_field           = 1
                ram_field           = 1
                storage_field       = 1
                psu_field           = 1
                cs_field            = 1
                ec_field            = 1
                id_field            = 1
                od_field            = 1
                sp_field            = 1
                ups_battery_field   = 1
                getLocations()
                loadTerminals()
                callAllAddField()
                add_terminal.hide()
            }
            bs5.toast(res[1].type,res[1].message,res[1].size)
        })
    })














    // GET ALL TABLE DATA
    loadTerminals()
    function loadTerminals(){
        sole.get("../../controllers/terminals/get_terminals.php").then(res => {
            terminalTable.clear().draw();
            res.forEach(t => {
                terminalTable.row.add([
                    t["id"],

                    "<b>Terminal No.: </b>"             + t["terminal_no"]                      + "</br>" +
                    "<b>Cabinet No.: </b>"              + (t["cabinet_no"] != "-"               ? t["cabinet_no"]           : "")       + "</br>" +
                    "<b>IP Address: </b>"               + (t["ip_address"] != "-"               ? t["ip_address"]           : ""),

                    "<b>Project: </b>"                  + t["project"]                          + "</br>" +
                    "<b>Room: </b>"                     + t["room"]                             + "</br>" +
                    "<b>Building: </b>"                 + t["building"],

                    t["unit_type"] != "-"               ? t["unit_type"]                                                    : "",

                    "<b>Model: </b>"                    + (t["motherboard_model"] != "-"        ? t["motherboard_model"]    : "")       + "</br>" +
                    "<b>Barcode: </b>"                  + (t["motherboard_barcode"] != "-"      ? t["motherboard_barcode"]  : ""),
                    
                    separatorFormater(t["cpu"],         ["Model","Barcode"],                    "CPU"),
                    separatorFormater(t["ram"],         ["Model","Barcode"],                    "RAM"),
                    separatorFormater(t["storage"],     ["Model","Barcode"],                    "Storage"),
                    separatorFormater(t["psu"],         ["Model","Barcode"],                    "PSU"),
                    separatorFormater(t["cs"],          ["Model","Barcode"],                    "CS"),
                    separatorFormater(t["ec"],          ["Model","Barcode"],                    "EC"),
                    separatorFormater(t["id_"],         ["Type","Model","Barcode"],             "ID"),
                    separatorFormater(t["od"],          ["Type","Model","Barcode"],             "OD"),
                    separatorFormater(t["sp"],          ["Type","Model","Barcode"],             "SP"),

                    t["ups_brand"] != "-"               ? t["ups_brand"]                        : "",

                    "<b>Model: </b>"                    + (t["ups_casing_model"] != "-"         ? t["ups_casing_model"]     : "")       + "</br>" +
                    "<b>Barcode: </b>"                  + (t["ups_casing_barcode"] != "-"       ? t["ups_casing_barcode"]   : "")       + "</br>",

                    separatorFormater(t["ups_battery"],["Model","Barcode"],"Battery"),


                    t["ups_status"] != "-"              ? t["ups_status"]                       : "",
                    t["kaspersky"] != "-"               ? t["kaspersky"]                        : "",
                    t["bitdefender"] != "-"             ? t["bitdefender"]                      : "",
                    t["windows_update"] != "-"          ? t["windows_update"]                   : "",
                    t["operating_system"] != "-"        ? t["operating_system"]                 : "",
                    t["windows_license"] != "-"         ? t["windows_license"]                  : "",
                    t["remarks"] != "-"                 ? t["remarks"]                          : "",
                    t["tech_recommendation"] != "-"     ? t["tech_recommendation"]              : "",
                    "",
                ])
            });
            terminalTable.draw();
        })
    }

    function separatorFormater(data,title,head){
        if(data == "-") return "";

        let response = ""
        let separated = data.split("+++")
        let count = 1;
        separated.forEach(sep => {
            console.log(separated.length)
            response += `<b>--- ${head + " " + count} ---</b></br>`
            let separated_ = sep.split("---")
            for (let i = 0; i < title.length; i++) {
                response += `<b>${title[i]}: </b>${separated_[i] != "NA" ? separated_[i] : ""}</br>${i == title.length-1 && count+1 != separated.length+1 ? "</br>" : ""}`
            }
            count++
        })
        return response;
    }

    // document.querySelector('#tb_terminals tbody').addEventListener('click', function(e) {
    //     const row = e.target.closest('tr');
    //     if (!row) return;

    //     const rowData = terminalTable.row(row).data();
    //     console.log('Row data:', rowData);
    // });



    document.querySelector('#tb_terminals tbody').addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;

        // Remove selected from all rows
        document.querySelectorAll('#tb_terminals tbody tr').forEach(r => {
            r.classList.remove('selected');
        });

        // Add selected to clicked row
        row.classList.add('selected');

        const rowData = terminalTable.row(row).data();

        if (e.target.closest('.btn-edit')){
            console.log("Edit " + rowData[0])
        }else if(e.target.closest('.btn-danger')){
            console.log("Delete " + rowData[0])
        }else{
            return
        }
    });


































}