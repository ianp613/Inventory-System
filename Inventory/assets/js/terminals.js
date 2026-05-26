if(document.getElementById("terminals")){
    const add_terminal                         = new bootstrap.Modal(document.getElementById('add_terminal'),unclose);
    const edit_terminal                        = new bootstrap.Modal(document.getElementById('edit_terminal'));
    const delete_terminal                      = new bootstrap.Modal(document.getElementById('delete_terminal'),unclose);
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

    callAllAddFieldAdd()
    function callAllAddFieldAdd(){
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

    var edit_cpu_field                               = 1
    var edit_ram_field                               = 1
    var edit_storage_field                           = 1
    var edit_psu_field                               = 1
    var edit_gpu_field                               = 1
    var edit_cs_field                                = 1
    var edit_ec_field                                = 1
    var edit_id_field                                = 1
    var edit_od_field                                = 1
    var edit_sp_field                                = 1
    var edit_ups_battery_field                       = 1

    callAllAddFieldEdit()
    function callAllAddFieldEdit(){
        var edit_cpu_add_field                           = document.getElementById("edit_cpu_add_field")
        edit_cpu_add_field.addEventListener("click", e => {
            edit_cpu_field++
            edit_cpu_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_cpu_field}" id="" class="form-control mt-4 edit_cpu_model" placeholder="CPU-${edit_cpu_field} Model">`+
                `<input type="text" name="${edit_cpu_field}" id="" class="form-control mt-2 edit_cpu_barcode" placeholder="CPU-${edit_cpu_field} Barcode"></input>`
            )
        })

        var edit_ram_add_field                           = document.getElementById("edit_ram_add_field")
        edit_ram_add_field.addEventListener("click", e => {
            edit_ram_field++
            edit_ram_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_ram_field}" id="" class="form-control mt-4 edit_ram_model" placeholder="RAM-${edit_ram_field} Model">`+
                `<input type="text" name="${edit_ram_field}" id="" class="form-control mt-2 edit_ram_barcode" placeholder="RAM-${edit_ram_field} Barcode"></input>`
            )
        })

        var edit_storage_add_field                       = document.getElementById("edit_storage_add_field")
        edit_storage_add_field.addEventListener("click", e => {
            edit_storage_field++
            edit_storage_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_storage_field}" id="" class="form-control mt-4 edit_storage_model" placeholder="Storage-${edit_storage_field} Model">`+
                `<input type="text" name="${edit_storage_field}" id="" class="form-control mt-2 edit_storage_barcode" placeholder="Storage-${edit_storage_field} Barcode"></input>`
            )
        })

        var edit_psu_add_field                           = document.getElementById("edit_psu_add_field")
        edit_psu_add_field.addEventListener("click", e => {
            edit_psu_field++
            edit_psu_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_psu_field}" id="" class="form-control mt-4 edit_psu_model" placeholder="PSU-${edit_psu_field} Model">`+
                `<input type="text" name="${edit_psu_field}" id="" class="form-control mt-2 edit_psu_barcode" placeholder="PSU-${edit_psu_field} Barcode"></input>`
            )
        })

        var edit_gpu_add_field                           = document.getElementById("edit_gpu_add_field")
        edit_gpu_add_field.addEventListener("click", e => {
            edit_gpu_field++
            edit_gpu_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_gpu_field}" id="" class="form-control mt-4 edit_gpu_model" placeholder="GPU-${edit_gpu_field} Model">`+
                `<input type="text" name="${edit_gpu_field}" id="" class="form-control mt-2 edit_gpu_barcode" placeholder="GPU-${edit_gpu_field} Barcode"></input>`
            )
        })

        var edit_cs_add_field                            = document.getElementById("edit_cs_add_field")
        edit_cs_add_field.addEventListener("click", e => {
            edit_cs_field++
            edit_cs_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_cs_field}" id="" class="form-control mt-4 edit_cs_model" placeholder="CS-${edit_cs_field} Model">`+
                `<input type="text" name="${edit_cs_field}" id="" class="form-control mt-2 edit_cs_barcode" placeholder="CS-${edit_cs_field} Barcode"></input>`
            )
        })

        var edit_ec_add_field                            = document.getElementById("edit_ec_add_field")
        edit_ec_add_field.addEventListener("click", e => {
            edit_ec_field++
            edit_ec_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_ec_field}" id="" class="form-control mt-4 edit_ec_model" placeholder="EC-${edit_ec_field} Model">`+
                `<input type="text" name="${edit_ec_field}" id="" class="form-control mt-2 edit_ec_barcode" placeholder="EC-${edit_ec_field} Barcode"></input>`
            )
        })

        var edit_id_add_field                            = document.getElementById("edit_id_add_field")
        edit_id_add_field.addEventListener("click", e => {
            edit_id_field++
            edit_id_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_id_field}" id="" class="form-control mt-4 edit_id_type" placeholder="ID-${edit_id_field} Type">`+
                `<input type="text" name="${edit_id_field}" id="" class="form-control mt-2 edit_id_model" placeholder="ID-${edit_id_field} Model">`+
                `<input type="text" name="${edit_id_field}" id="" class="form-control mt-2 edit_id_barcode" placeholder="ID-${edit_id_field} Barcode"></input>`
            )
        })

        var edit_od_add_field                            = document.getElementById("edit_od_add_field")
        edit_od_add_field.addEventListener("click", e => {
            edit_od_field++
            edit_od_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_od_field}" id="" class="form-control mt-4 edit_od_type" placeholder="OD-${edit_od_field} Type">`+
                `<input type="text" name="${edit_od_field}" id="" class="form-control mt-2 edit_od_model" placeholder="OD-${edit_od_field} Model">`+
                `<input type="text" name="${edit_od_field}" id="" class="form-control mt-2 edit_od_barcode" placeholder="OD-${edit_od_field} Barcode"></input>`
            )
        })    

        var edit_sp_add_field                            = document.getElementById("edit_sp_add_field")
        edit_sp_add_field.addEventListener("click", e => {
            edit_sp_field++
            edit_sp_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_sp_field}" id="" class="form-control mt-4 edit_sp_type" placeholder="SP-${edit_sp_field} Type">`+
                `<input type="text" name="${edit_sp_field}" id="" class="form-control mt-2 edit_sp_model" placeholder="SP-${edit_sp_field} Model">`+
                `<input type="text" name="${edit_sp_field}" id="" class="form-control mt-2 edit_sp_barcode" placeholder="SP-${edit_sp_field} Barcode"></input>`
            )
        })

        var edit_ups_battery_add_field                   = document.getElementById("edit_ups_battery_add_field")
        edit_ups_battery_add_field.addEventListener("click", e => {
            edit_ups_battery_field++
            edit_ups_battery_add_field.parentElement.insertAdjacentHTML("beforebegin",
                `<input type="text" name="${edit_ups_battery_field}" id="" class="form-control mt-4 edit_ups_battery_model" placeholder="Batt-${edit_ups_battery_field} Model">`+
                `<input type="text" name="${edit_ups_battery_field}" id="" class="form-control mt-2 edit_ups_battery_barcode" placeholder="Batt-${edit_ups_battery_field} Barcode"></input>`
            )
        })    
    }
    

    getLocationsAdd()
    function getLocationsAdd(){
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
    }



    getLocationsEdit()
    var edit_terminal_add_location_building          = document.getElementById("edit_terminal_add_location_building")
    var edit_terminal_add_location_building_others   = document.getElementById("edit_terminal_add_location_building_others")
    var edit_terminal_add_location_room              = document.getElementById("edit_terminal_add_location_room")
    var edit_terminal_add_location_room_others       = document.getElementById("edit_terminal_add_location_room_others")
    var edit_terminal_add_location_project           = document.getElementById("edit_terminal_add_location_project")
    var edit_terminal_add_location_project_others    = document.getElementById("edit_terminal_add_location_project_others")
    function getLocationsEdit(){
        var edit_Buildings                               = [];
        edit_terminal_add_location_building          = document.getElementById("edit_terminal_add_location_building")
        edit_terminal_add_location_building_others   = document.getElementById("edit_terminal_add_location_building_others")
        edit_terminal_add_location_room              = document.getElementById("edit_terminal_add_location_room")
        edit_terminal_add_location_room_others       = document.getElementById("edit_terminal_add_location_room_others")
        edit_terminal_add_location_project           = document.getElementById("edit_terminal_add_location_project")
        edit_terminal_add_location_project_others    = document.getElementById("edit_terminal_add_location_project_others")


        sole.get("../../controllers/equipments/get_equipment_location_preset.php").then(res => {
            edit_terminal_add_location_building.innerHTML = ""

            var edit_opt_building = document.createElement("option")
            edit_opt_building.value = ""
            edit_opt_building.innerText = "-- Select Building --"
            edit_opt_building.disabled = true
            edit_opt_building.selected = true
            edit_terminal_add_location_building.appendChild(edit_opt_building)

            res.Building.forEach(bldg => {
                var edit_opt_building = document.createElement("option")
                edit_opt_building.value = Object.keys(bldg)[0]
                edit_opt_building.innerText = Object.keys(bldg)[0]
                edit_terminal_add_location_building.appendChild(edit_opt_building)
                edit_Buildings.push(bldg)
            })

            var edit_opt_building = document.createElement("option")
            edit_opt_building.value = "Others"
            edit_opt_building.innerText = "Others"
            edit_terminal_add_location_building.appendChild(edit_opt_building)
            

            edit_terminal_add_location_building.addEventListener("change", e => {
                if(edit_terminal_add_location_building.value && edit_terminal_add_location_building.value != "Others"){
                    edit_terminal_add_location_room.disabled = false
                    edit_terminal_add_location_project.disabled = false
                    
                    edit_terminal_add_location_building_others.value = ""
                    edit_terminal_add_location_room.innerHTML = ""
                    edit_terminal_add_location_project.innerHTML = ""

                    var edit_opt_room = document.createElement("option")
                    edit_opt_room.value = ""
                    edit_opt_room.innerText = "-- Select Room --"
                    edit_opt_room.disabled = true
                    edit_opt_room.selected = true
                    edit_terminal_add_location_room.appendChild(edit_opt_room)

                    var edit_opt_project = document.createElement("option")
                    edit_opt_project.value = ""
                    edit_opt_project.innerText = "-- Select Project / Office --"
                    edit_opt_project.disabled = true
                    edit_opt_project.selected = true
                    edit_terminal_add_location_project.appendChild(edit_opt_project)

                    edit_Buildings.forEach(bldgs => {
                        let edit_key = Object.keys(bldgs)[0]
                        if(edit_key == edit_terminal_add_location_building.value){
                            bldgs[edit_key].Room.forEach(room => {
                                var edit_opt_room = document.createElement("option")
                                edit_opt_room.value = room
                                edit_opt_room.innerText = room
                                edit_terminal_add_location_room.appendChild(edit_opt_room)
                            });

                            bldgs[edit_key].Project.forEach(project => {
                                var edit_opt_project = document.createElement("option")
                                edit_opt_project.value = project
                                edit_opt_project.innerText = project
                                edit_terminal_add_location_project.appendChild(edit_opt_project)
                            });
                        }
                    });

                    var edit_opt_room = document.createElement("option")
                    edit_opt_room.value = "Others"
                    edit_opt_room.innerText = "Others"
                    edit_terminal_add_location_room.appendChild(edit_opt_room)

                    var edit_opt_project = document.createElement("option")
                    edit_opt_project.value = "Others"
                    edit_opt_project.innerText = "Others"
                    edit_terminal_add_location_project.appendChild(edit_opt_project)
                }
                if(edit_terminal_add_location_building.value == "Others"){
                    edit_terminal_add_location_room.value = "Others"
                    edit_terminal_add_location_room.disabled = true
                    edit_terminal_add_location_project.value = "Others"
                    edit_terminal_add_location_project.disabled = true
                }
            })
        })
        
        edit_terminal_add_location_building_others.addEventListener("input", e => {
            if(edit_terminal_add_location_building_others.value){
                edit_terminal_add_location_building.value = "Others"
                edit_terminal_add_location_room.value = "Others"
                edit_terminal_add_location_room.disabled = true
                edit_terminal_add_location_project.value = "Others"
                edit_terminal_add_location_project.disabled = true
            }else{
                edit_terminal_add_location_building.value = ""
                edit_terminal_add_location_room.value = ""
                edit_terminal_add_location_room.disabled = false
                edit_terminal_add_location_project.value = ""
                edit_terminal_add_location_project.disabled = false
            }
        })

        edit_terminal_add_location_room.addEventListener("change", e => {
            if(edit_terminal_add_location_room.value && edit_terminal_add_location_room.value != "Others"){
                edit_terminal_add_location_room_others.value = ""
            }
        })

        edit_terminal_add_location_room_others.addEventListener("input", e => {
            if(edit_terminal_add_location_room_others.value){
                edit_terminal_add_location_room.value = "Others"
            }else{
                if(edit_terminal_add_location_building.value != "Others"){
                    edit_terminal_add_location_room.value = ""
                }
            }
        })

        edit_terminal_add_location_project.addEventListener("change", e => {
            if(edit_terminal_add_location_project.value && edit_terminal_add_location_project.value != "Others"){
                edit_terminal_add_location_project_others.value = ""
            }
        })

        edit_terminal_add_location_project_others.addEventListener("input", e => {
            if(edit_terminal_add_location_project_others.value){
                edit_terminal_add_location_project.value = "Others"
            }else{
                if(edit_terminal_add_location_building.value != "Others"){
                    edit_terminal_add_location_project.value = ""
                }
            }
        })
    }


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
        terminal_no                             = document.getElementById("terminal_no")
        cabinet_no                              = document.getElementById("cabinet_no")
        ip_address                              = document.getElementById("ip_address")
        remarks_                                = document.getElementById("remarks_")
        tech_recommendation                     = document.getElementById("tech_recommendation")
        unit_type                               = document.getElementById("unit_type")
        motherboard_model                       = document.getElementById("motherboard_model")
        motherboard_barcode                     = document.getElementById("motherboard_barcode")
        ups_brand                               = document.getElementById("ups_brand")
        ups_casing_model                        = document.getElementById("ups_casing_model")
        ups_casing_barcode                      = document.getElementById("ups_casing_barcode")
        ups_status                              = document.getElementById("ups_status")
        kaspersky                               = document.getElementById("kaspersky")
        bitdefender                             = document.getElementById("bitdefender")
        windows_update                          = document.getElementById("windows_update")
        operating_system                        = document.getElementById("operating_system")
        windows_license                         = document.getElementById("windows_license")

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

        var building_               = terminal_add_location_building_others.value ? terminal_add_location_building_others.value : terminal_add_location_building.value && terminal_add_location_building.value != "Others" ? terminal_add_location_building.value : ""
        var room_                   = terminal_add_location_room_others.value ? terminal_add_location_room_others.value : terminal_add_location_room.value && terminal_add_location_room.value != "Others" ? terminal_add_location_room.value : ""
        var project_                = terminal_add_location_project_others.value ? terminal_add_location_project_others.value : terminal_add_location_project.value && terminal_add_location_project.value != "Others" ? terminal_add_location_project.value : ""

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
                getLocationsAdd()
                loadTerminals(res[2])
                callAllAddFieldAdd()
                add_terminal.hide()
            }
            bs5.toast(res[1].type,res[1].message,res[1].size)
        })
    })

    var save_edit_terminal                           = document.getElementById("save_edit_terminal");
    var edit_terminal_no                             = document.getElementById("edit_terminal_no")
    var edit_cabinet_no                              = document.getElementById("edit_cabinet_no")
    var edit_ip_address                              = document.getElementById("edit_ip_address")
    var edit_remarks_                                = document.getElementById("edit_remarks_")
    var edit_tech_recommendation                     = document.getElementById("edit_tech_recommendation")
    var edit_unit_type                               = document.getElementById("edit_unit_type")
    var edit_motherboard_model                       = document.getElementById("edit_motherboard_model")
    var edit_motherboard_barcode                     = document.getElementById("edit_motherboard_barcode")
    var edit_ups_brand                               = document.getElementById("edit_ups_brand")
    var edit_ups_casing_model                        = document.getElementById("edit_ups_casing_model")
    var edit_ups_casing_barcode                      = document.getElementById("edit_ups_casing_barcode")
    var edit_ups_status                              = document.getElementById("edit_ups_status")
    var edit_kaspersky                               = document.getElementById("edit_kaspersky")
    var edit_bitdefender                             = document.getElementById("edit_bitdefender")
    var edit_windows_update                          = document.getElementById("edit_windows_update")
    var edit_operating_system                        = document.getElementById("edit_operating_system")
    var edit_windows_license                         = document.getElementById("edit_windows_license")

    save_edit_terminal.addEventListener("click", e => {
        if(!edit_terminal_no.value){
            bs5.toast("warning","Please input terminal no.")
            return
        }
        var edit_cpu_combined            = ""
        var edit_cpu_model               = document.getElementsByClassName("edit_cpu_model")
        var edit_cpu_barcode             = document.getElementsByClassName("edit_cpu_barcode")
        for (let i = 0; i < edit_cpu_model.length; i++) {
            if(edit_cpu_model[i].value || edit_cpu_barcode[i].value){
                edit_cpu_combined += (edit_cpu_model[i].value ? edit_cpu_model[i].value : "NA")+"---"+(edit_cpu_barcode[i].value ? edit_cpu_barcode[i].value : "NA")
            }
            if(i < edit_cpu_model.length-1 && (edit_cpu_model[i+1].value || edit_cpu_barcode[i+1].value)){
                edit_cpu_combined += "+++"
            }
        }

        var edit_ram_combined            = ""
        var edit_ram_model               = document.getElementsByClassName("edit_ram_model")
        var edit_ram_barcode             = document.getElementsByClassName("edit_ram_barcode")
        for (let i = 0; i < edit_ram_model.length; i++) {
            if(edit_ram_model[i].value || edit_ram_barcode[i].value){
                edit_ram_combined += (edit_ram_model[i].value ? edit_ram_model[i].value : "NA")+"---"+(edit_ram_barcode[i].value ? edit_ram_barcode[i].value : "NA")
            }
            if(i < edit_ram_model.length-1 && (edit_ram_model[i+1].value || edit_ram_barcode[i+1].value)){
                edit_ram_combined += "+++"
            }
        }

        var edit_storage_combined        = ""
        var edit_storage_model           = document.getElementsByClassName("edit_storage_model")
        var edit_storage_barcode         = document.getElementsByClassName("edit_storage_barcode")
        for (let i = 0; i < edit_storage_model.length; i++) {
            if(edit_storage_model[i].value || edit_storage_barcode[i].value){
                edit_storage_combined += (edit_storage_model[i].value ? edit_storage_model[i].value : "NA")+"---"+(edit_storage_barcode[i].value ? edit_storage_barcode[i].value : "NA")
            }
            if(i < edit_storage_model.length-1 && (edit_storage_model[i+1].value || edit_storage_barcode[i+1].value)){
                edit_storage_combined += "+++"
            }
        }

        var edit_psu_combined            = ""
        var edit_psu_model               = document.getElementsByClassName("edit_psu_model")
        var edit_psu_barcode             = document.getElementsByClassName("edit_psu_barcode")
        for (let i = 0; i < edit_psu_model.length; i++) {
            if(edit_psu_model[i].value || edit_psu_barcode[i].value){
                edit_psu_combined += (edit_psu_model[i].value ? edit_psu_model[i].value : "NA")+"---"+(edit_psu_barcode[i].value ? edit_psu_barcode[i].value : "NA")
            }
            if(i < edit_psu_model.length-1 && (edit_psu_model[i+1].value || edit_psu_barcode[i+1].value)){
                edit_psu_combined += "+++"
            }
        }

        var edit_gpu_combined            = ""
        var edit_gpu_model               = document.getElementsByClassName("edit_gpu_model")
        var edit_gpu_barcode             = document.getElementsByClassName("edit_gpu_barcode")
        for (let i = 0; i < edit_gpu_model.length; i++) {
            if(edit_gpu_model[i].value || edit_gpu_barcode[i].value){
                edit_gpu_combined += (edit_gpu_model[i].value ? edit_gpu_model[i].value : "NA")+"---"+(edit_gpu_barcode[i].value ? edit_gpu_barcode[i].value : "NA")
            }
            if(i < edit_gpu_model.length-1 && (edit_gpu_model[i+1].value || edit_gpu_barcode[i+1].value)){
                edit_gpu_combined += "+++"
            }
        }

        var edit_cs_combined            = ""
        var edit_cs_model               = document.getElementsByClassName("edit_cs_model")
        var edit_cs_barcode             = document.getElementsByClassName("edit_cs_barcode")
        for (let i = 0; i < edit_cs_model.length; i++) {
            if(edit_cs_model[i].value || edit_cs_barcode[i].value){
                edit_cs_combined += (edit_cs_model[i].value ? edit_cs_model[i].value : "NA")+"---"+(edit_cs_barcode[i].value ? edit_cs_barcode[i].value : "NA")
            }
            if(i < edit_cs_model.length-1 && (edit_cs_model[i+1].value || edit_cs_barcode[i+1].value)){
                edit_cs_combined += "+++"
            }
        }

        var edit_ec_combined            = ""
        var edit_ec_model               = document.getElementsByClassName("edit_ec_model")
        var edit_ec_barcode             = document.getElementsByClassName("edit_ec_barcode")
        for (let i = 0; i < edit_ec_model.length; i++) {
            if(edit_ec_model[i].value || edit_ec_barcode[i].value){
                edit_ec_combined += (edit_ec_model[i].value ? edit_ec_model[i].value : "NA")+"---"+(edit_ec_barcode[i].value ? edit_ec_barcode[i].value : "NA")
            }
            if(i < edit_ec_model.length-1 && (edit_ec_model[i+1].value || edit_ec_barcode[i+1].value)){
                edit_ec_combined += "+++"
            }
        }

        var edit_id_combined             = ""
        var edit_id_type                 = document.getElementsByClassName("edit_id_type")
        var edit_id_model                = document.getElementsByClassName("edit_id_model")
        var edit_id_barcode              = document.getElementsByClassName("edit_id_barcode")
        for (let i = 0; i < edit_id_model.length; i++) {
            if(edit_id_type[i].value || edit_id_model[i].value || edit_id_barcode[i].value){
                edit_id_combined += (edit_id_type[i].value ? edit_id_type[i].value : "NA")+"---"+(edit_id_model[i].value ? edit_id_model[i].value : "NA")+"---"+(edit_id_barcode[i].value ? edit_id_barcode[i].value : "NA")
            }
            if(i < edit_id_model.length-1 && (edit_id_type[i+1].value || edit_id_model[i+1].value || edit_id_barcode[i+1].value)){
                edit_id_combined += "+++"
            }
        }

        var edit_od_combined             = ""
        var edit_od_type                 = document.getElementsByClassName("edit_od_type")
        var edit_od_model                = document.getElementsByClassName("edit_od_model")
        var edit_od_barcode              = document.getElementsByClassName("edit_od_barcode")
        for (let i = 0; i < edit_od_model.length; i++) {
            if(edit_od_type[i].value || edit_od_model[i].value || edit_od_barcode[i].value){
                edit_od_combined += (edit_od_type[i].value ? edit_od_type[i].value : "NA")+"---"+(edit_od_model[i].value ? edit_od_model[i].value : "NA")+"---"+(edit_od_barcode[i].value ? edit_od_barcode[i].value : "NA")
            }
            if(i < edit_od_model.length-1 && (edit_od_type[i+1].value || edit_od_model[i+1].value || edit_od_barcode[i+1].value)){
                edit_od_combined += "+++"
            }
        }

        var edit_sp_combined             = ""
        var edit_sp_type                 = document.getElementsByClassName("edit_sp_type")
        var edit_sp_model                = document.getElementsByClassName("edit_sp_model")
        var edit_sp_barcode              = document.getElementsByClassName("edit_sp_barcode")
        for (let i = 0; i < edit_sp_model.length; i++) {
            if(edit_sp_type[i].value || edit_sp_model[i].value || edit_sp_barcode[i].value){
                edit_sp_combined += (edit_sp_type[i].value ? edit_sp_type[i].value : "NA")+"---"+(edit_sp_model[i].value ? edit_sp_model[i].value : "NA")+"---"+(edit_sp_barcode[i].value ? edit_sp_barcode[i].value : "NA")
            }
            if(i < edit_sp_model.length-1 && (edit_sp_type[i+1].value || edit_sp_model[i+1].value || edit_sp_barcode[i+1].value)){
                edit_sp_combined += "+++"
            }
        }

        var edit_ups_battery_combined   = ""
        var edit_ups_battery_model      = document.getElementsByClassName("edit_ups_battery_model")
        var edit_ups_battery_barcode    = document.getElementsByClassName("edit_ups_battery_barcode")
        for (let i = 0; i < edit_ups_battery_model.length; i++) {
            if(edit_ups_battery_model[i].value || edit_ups_battery_barcode[i].value){
                edit_ups_battery_combined += (edit_ups_battery_model[i].value ? edit_ups_battery_model[i].value : "NA")+"---"+(edit_ups_battery_barcode[i].value ? edit_ups_battery_barcode[i].value : "NA")
            }
            if(i < edit_ups_battery_model.length-1 && (edit_ups_battery_model[i+1].value || edit_ups_battery_barcode[i+1].value)){
                edit_ups_battery_combined += "+++"
            }
        }

        var edit_building_          = edit_terminal_add_location_building_others.value ? edit_terminal_add_location_building_others.value : edit_terminal_add_location_building.value && edit_terminal_add_location_building.value != "Others" ? edit_terminal_add_location_building.value : ""
        var edit_room_              = edit_terminal_add_location_room_others.value ? edit_terminal_add_location_room_others.value : edit_terminal_add_location_room.value && edit_terminal_add_location_room.value != "Others" ? edit_terminal_add_location_room.value : ""
        var edit_project_           = edit_terminal_add_location_project_others.value ? edit_terminal_add_location_project_others.value : edit_terminal_add_location_project.value && edit_terminal_add_location_project.value != "Others" ? edit_terminal_add_location_project.value : ""

        if(!edit_building_ || !edit_room_ || !edit_project_){
            bs5.toast("warning","Please select or input location.")
            return
        }

        sole.post("../../controllers/terminals/edit_terminal.php", {
            id                      : rowData[0],
            terminal_no             : edit_terminal_no.value,
            cabinet_no              : edit_cabinet_no.value,
            ip_address              : edit_ip_address.value,
            building                : edit_building_,
            room                    : edit_room_,
            project                 : edit_project_,
            remarks                 : edit_remarks_.value,
            tech_recommendation     : edit_tech_recommendation.value,
            unit_type               : edit_unit_type.value,
            motherboard_model       : edit_motherboard_model.value,
            motherboard_barcode     : edit_motherboard_barcode.value,
            cpu                     : edit_cpu_combined,
            ram                     : edit_ram_combined,
            storage                 : edit_storage_combined,
            psu                     : edit_psu_combined,
            gpu                     : edit_gpu_combined,
            cs                      : edit_cs_combined,
            ec                      : edit_ec_combined,
            id_                      : edit_id_combined,
            od                      : edit_od_combined,
            sp                      : edit_sp_combined,
            ups_battery             : edit_ups_battery_combined,
            ups_brand               : edit_ups_brand.value,
            ups_casing_model        : edit_ups_casing_model.value,
            ups_casing_barcode      : edit_ups_casing_barcode.value,
            ups_status              : edit_ups_status.value,
            kaspersky               : edit_kaspersky.value,
            bitdefender             : edit_bitdefender.value,
            windows_update          : edit_windows_update.value,
            operating_system        : edit_operating_system.value,
            windows_license         : edit_windows_license.value
        }).then(res => {
            if(res[1].status){
                loadTerminals_EditRow(res[0])
                edit_terminal.hide()
            }
            bs5.toast(res[1].type,res[1].message,res[1].size)
        })
    })








    // GET ALL TABLE DATA
    loadTerminals()
    function loadTerminals(terminal = false){
        if(!terminal){
            console.log("load all")
            sole.get("../../controllers/terminals/get_terminals.php").then(res => {
                terminalTable.clear().draw();
                res.forEach(t => {
                    loadTerminals_AddRow(t)
                });
                terminalTable.draw();
            })    
        }else{
            console.log("load one")
            loadTerminals_AddRow(terminal)
            terminalTable.draw()
        }
    }

    function loadTerminals_EditRow(t){
        rowData[1]  =   "<b>Terminal No.: </b>"             + t["terminal_no"]                      + "</br>" +
                        "<b>Cabinet No.: </b>"              + (t["cabinet_no"] != "-"               ? t["cabinet_no"]           : "")       + "</br>" +
                        "<b>IP Address: </b>"               + (t["ip_address"] != "-"               ? t["ip_address"]           : "")
        rowData[2]  =   "<b>Project: </b>"                  + t["project"]                          + "</br>" +
                        "<b>Room: </b>"                     + t["room"]                             + "</br>" +
                        "<b>Building: </b>"                 + t["building"]
        rowData[3]  =   t["unit_type"] != "-"               ? t["unit_type"]                                                    : ""
        rowData[4]  =   "<b>Model: </b>"                    + (t["motherboard_model"] != "-"        ? t["motherboard_model"]    : "")       + "</br>" +
                        "<b>Barcode: </b>"                  + (t["motherboard_barcode"] != "-"      ? t["motherboard_barcode"]  : "")
        rowData[5]  =   separatorFormater(t["cpu"],         ["Model","Barcode"],                    "CPU")
        rowData[6]  =   separatorFormater(t["ram"],         ["Model","Barcode"],                    "RAM")
        rowData[7]  =   separatorFormater(t["storage"],     ["Model","Barcode"],                    "Storage")
        rowData[8]  =   separatorFormater(t["psu"],         ["Model","Barcode"],                    "PSU")
        rowData[9]  =   separatorFormater(t["cs"],          ["Model","Barcode"],                    "CS")
        rowData[10] =   separatorFormater(t["ec"],          ["Model","Barcode"],                    "EC")
        rowData[11] =   separatorFormater(t["id_"],         ["Type","Model","Barcode"],             "ID")
        rowData[12] =   separatorFormater(t["od"],          ["Type","Model","Barcode"],             "OD")
        rowData[13] =   separatorFormater(t["sp"],          ["Type","Model","Barcode"],             "SP")
        rowData[14] =   t["ups_brand"] != "-"               ? t["ups_brand"]                        : ""
        rowData[15] =   "<b>Model: </b>"                    + (t["ups_casing_model"] != "-"         ? t["ups_casing_model"]     : "")       + "</br>" +
                        "<b>Barcode: </b>"                  + (t["ups_casing_barcode"] != "-"       ? t["ups_casing_barcode"]   : "")       + "</br>"
        rowData[16] =   separatorFormater(t["ups_battery"],["Model","Barcode"],"Battery")
        rowData[17] =   t["ups_status"] != "-"              ? t["ups_status"]                       : ""
        rowData[18] =   t["kaspersky"] != "-"               ? t["kaspersky"]                        : ""
        rowData[19] =   t["bitdefender"] != "-"             ? t["bitdefender"]                      : ""
        rowData[20] =   t["windows_update"] != "-"          ? t["windows_update"]                   : ""
        rowData[21] =   t["operating_system"] != "-"        ? t["operating_system"]                 : ""
        rowData[22] =   t["windows_license"] != "-"         ? t["windows_license"]                  : ""
        rowData[23] =   t["remarks"] != "-"                 ? t["remarks"]                          : ""
        rowData[24] =   t["tech_recommendation"] != "-"     ? t["tech_recommendation"]              : ""
        terminalTable.row(row).data(rowData).draw(false);
    }


    function loadTerminals_AddRow(t){
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
    }

    function separatorFormater(data,title,head){
        if(data == "-") return "";

        let response = ""
        let separated = data.split("+++").filter(Boolean)
        let count = 1;
        separated.forEach(sep => {
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


    var row         = null;
    var rowData     = null;
    document.querySelector('#tb_terminals tbody').addEventListener('click', function(e) {
        row = e.target.closest('tr');
        if (!row) return;
        // Remove selected from all rows
        document.querySelectorAll('#tb_terminals tbody tr').forEach(r => {
            r.classList.remove('selected');
        });
        // Add selected to clicked row
        row.classList.add('selected');
        rowData = terminalTable.row(row).data();

        if (e.target.closest('.btn-edit')){
            sole.post("../../controllers/terminals/find_terminal.php",{
                id : rowData[0]
            }).then(res => {
                edit_terminal.show()
                document.getElementById("modal_body_edit_terminal").innerHTML = res[0]
                edit_cpu_field           = 1
                edit_ram_field           = 1
                edit_storage_field       = 1
                edit_psu_field           = 1
                edit_cs_field            = 1
                edit_ec_field            = 1
                edit_id_field            = 1
                edit_od_field            = 1
                edit_sp_field            = 1
                edit_ups_battery_field   = 1
                getLocationsEdit()
                callAllAddFieldEdit()
                editTerminal(res[1])
            })
        }else if(e.target.closest('.btn-danger')){
            sole.post("../../controllers/terminals/find_terminal.php",{
                id : rowData[0]
            }).then(res => {
                delete_terminal.show()
                document.querySelector("#delete_terminal_name").innerText = res[1]["terminal_no"]
            })
        }else{
            return
        }
    });

    document.querySelector("#delete_terminal_btn").addEventListener("click", e => {
        if (!row) return;
        sole.post("../../controllers/terminals/delete_terminal.php",{
            id : rowData[0]
        }).then(res => {
            if(res.status){
                terminalTable.row(row).remove().draw(false);
                row = null;
            }
            bs5.toast(res.type,res.message,res.size)
        })
    })

    function editTerminal(res){
        save_edit_terminal                           = document.getElementById("save_edit_terminal");
        edit_terminal_no                             = document.getElementById("edit_terminal_no")
        edit_cabinet_no                              = document.getElementById("edit_cabinet_no")
        edit_ip_address                              = document.getElementById("edit_ip_address")
        edit_remarks_                                = document.getElementById("edit_remarks_")
        edit_tech_recommendation                     = document.getElementById("edit_tech_recommendation")
        edit_unit_type                               = document.getElementById("edit_unit_type")
        edit_motherboard_model                       = document.getElementById("edit_motherboard_model")
        edit_motherboard_barcode                     = document.getElementById("edit_motherboard_barcode")
        edit_ups_brand                               = document.getElementById("edit_ups_brand")
        edit_ups_casing_model                        = document.getElementById("edit_ups_casing_model")
        edit_ups_casing_barcode                      = document.getElementById("edit_ups_casing_barcode")
        edit_ups_status                              = document.getElementById("edit_ups_status")
        edit_kaspersky                               = document.getElementById("edit_kaspersky")
        edit_bitdefender                             = document.getElementById("edit_bitdefender")
        edit_windows_update                          = document.getElementById("edit_windows_update")
        edit_operating_system                        = document.getElementById("edit_operating_system")
        edit_windows_license                         = document.getElementById("edit_windows_license")

        // ✅ Basic fields
        edit_terminal_no.value          = res.terminal_no
        edit_cabinet_no.value           = res.cabinet_no                != "-" ? res.cabinet_no : ""
        edit_ip_address.value           = res.ip_address                != "-" ? res.ip_address : ""
        edit_remarks_.value             = res.remarks                   != "-" ? res.remarks : ""
        edit_tech_recommendation.value  = res.tech_recommendation       != "-" ? res.tech_recommendation : ""
        edit_unit_type.value            = res.unit_type                 != "-" ? res.unit_type : ""
        edit_motherboard_model.value    = res.motherboard_model         != "-" ? res.motherboard_model : ""
        edit_motherboard_barcode.value  = res.motherboard_barcode       != "-" ? res.motherboard_barcode : ""
        edit_ups_brand.value            = res.ups_brand                 != "-" ? res.ups_brand : ""
        edit_ups_casing_model.value     = res.ups_casing_model          != "-" ? res.ups_casing_model : ""
        edit_ups_casing_barcode.value   = res.ups_casing_barcode        != "-" ? res.ups_casing_barcode : ""
        edit_ups_status.value           = res.ups_status                != "-" ? res.ups_status : ""
        edit_kaspersky.value            = res.kaspersky                 != "-" ? res.kaspersky : ""
        edit_bitdefender.value          = res.bitdefender               != "-" ? res.bitdefender : ""
        edit_windows_update.value       = res.windows_update            != "-" ? res.windows_update : ""
        edit_operating_system.value     = res.operating_system          != "-" ? res.operating_system : ""
        edit_windows_license.value      = res.windows_license           != "-" ? res.windows_license : ""

        // ✅ Location fields
        sole.get("../../controllers/equipments/get_equipment_location_preset.php").then(res_ => {
            let Building_ = [];

            res_.Building.forEach(bldg => {
                if(Object.keys(bldg)[0] == res.building){
                    Building_.push(bldg)
                }
            })

            if(Building_.length){
                edit_terminal_add_location_building_others.value = ""
                edit_terminal_add_location_room.disabled = false
                edit_terminal_add_location_project.disabled = false
                edit_terminal_add_location_building.value = res.building != "-" ? res.building : ""
                edit_terminal_add_location_room.innerHTML = ""
                edit_terminal_add_location_project.innerHTML = ""
                
                var opt_room = document.createElement("option")
                opt_room.value = ""
                opt_room.innerText = "-- Select Room --"
                opt_room.disabled = true
                opt_room.selected = true
                edit_terminal_add_location_room.appendChild(opt_room)

                var opt_room = document.createElement("option")
                opt_room.value = ""
                opt_room.innerText = "-- Select Project / Office --"
                opt_room.disabled = true
                opt_room.selected = true
                edit_terminal_add_location_project.appendChild(opt_room)


                var Room_ = "";
                Building_[0][Object.keys(Building_[0])[0]].Room.forEach(room => {
                    var opt_room = document.createElement("option")
                    opt_room.value = room
                    opt_room.innerText = room
                    edit_terminal_add_location_room.appendChild(opt_room)
                    if(room.toLowerCase() == res.room.toLowerCase()){
                        Room_ = room
                    }
                })

                var Project_ = "";
                Building_[0][Object.keys(Building_[0])[0]].Project.forEach(proj => {
                    var opt_project = document.createElement("option")
                    opt_project.value = proj
                    opt_project.innerText = proj
                    edit_terminal_add_location_project.appendChild(opt_project)
                    if(proj.toLowerCase() == res.project.toLowerCase()){
                        Project_ = proj
                    }
                })

                var opt_room = document.createElement("option")
                opt_room.value = "Others"
                opt_room.innerText = "Others"
                edit_terminal_add_location_room.appendChild(opt_room)

                var opt_project = document.createElement("option")
                opt_project.value = "Others"
                opt_project.innerText = "Others"
                edit_terminal_add_location_project.appendChild(opt_project)
                
                if(res.room != "-"){
                    if(Building_[0][Object.keys(Building_[0])[0]].Room.some(v => v.toLowerCase() === res.room.toLowerCase())){
                        edit_terminal_add_location_room.value = Room_
                        edit_terminal_add_location_room_others.value = ""
                    }else{
                        edit_terminal_add_location_room.value = "Others"
                        edit_terminal_add_location_room_others.value = res.room
                    }
                }else{
                    edit_terminal_add_location_room.value = ""
                    edit_terminal_add_location_room_others.value = ""
                }

                if(res.project != "-"){

                    if(Building_[0][Object.keys(Building_[0])[0]].Project.some(v => v.toLowerCase() === res.project.toLowerCase())){
                        edit_terminal_add_location_project.value = Project_
                        edit_terminal_add_location_project_others.value = ""
                    }else{
                        edit_terminal_add_location_project.value = "Others"
                        edit_terminal_add_location_project_others.value = res.project
                    }
                }else{
                    edit_terminal_add_location_project.value = ""
                    edit_terminal_add_location_project_others.value = ""
                }
            }else{

                edit_terminal_add_location_building_others.value = res.building != "-" ? res.building : ""

                if(res.room != "-"){
                    edit_terminal_add_location_room.value = "Others"
                    edit_terminal_add_location_room_others.value = res.room
                }else{
                    edit_terminal_add_location_room.value = ""
                    edit_terminal_add_location_room_others.value = ""
                }

                if(res.project != "-"){
                    edit_terminal_add_location_project.value = "Others"
                    edit_terminal_add_location_project_others.value = res.project
                }else{
                    edit_terminal_add_location_project.value = ""
                    edit_terminal_add_location_project_others.value = ""
                }

                edit_terminal_add_location_building.value = ""
                edit_terminal_add_location_room.disabled = false
                edit_terminal_add_location_project.disabled = false
            }    
        })

        // ✅ For model---barcode format (cpu, ram, storage, psu, gpu, cs, ec, ups_battery)
        function fillCombinedFields(combined, addFieldId, modelClass, barcodeClass, placeholder, counterName){
            if(combined == "-") return

            const items       = combined.split("+++").filter(Boolean)
            const addFieldBtn = document.getElementById(addFieldId)

            items.forEach((item, i) => {
                const parts   = item.split("---")
                const model   = parts[0] !== "NA" ? parts[0] : ""
                const barcode = parts[1] !== "NA" ? parts[1] : ""

                if(i === 0){
                    // ✅ Fill existing first field
                    const modelFields   = document.getElementsByClassName(modelClass)
                    const barcodeFields = document.getElementsByClassName(barcodeClass)
                    if(modelFields[0])   modelFields[0].value   = model
                    if(barcodeFields[0]) barcodeFields[0].value = barcode
                } else {
                    // ✅ Increment the global counter by name
                    window[counterName]++

                    // ✅ Add new row
                    addFieldBtn.parentElement.insertAdjacentHTML("beforebegin",
                        `<input type="text" name="${window[counterName]}" class="form-control mt-4 ${modelClass}" placeholder="${placeholder}-${window[counterName]} Model" value="${model}">` +
                        `<input type="text" name="${window[counterName]}" class="form-control mt-2 ${barcodeClass}" placeholder="${placeholder}-${window[counterName]} Barcode" value="${barcode}">`
                    )
                }
            })
        }

        // ✅ For type---model---barcode format (id, od, sp)
        function fillCombinedFieldsWithType(combined, addFieldId, typeClass, modelClass, barcodeClass, placeholder, counterName){
            if(combined == "-") return

            const items       = combined.split("+++").filter(Boolean)
            const addFieldBtn = document.getElementById(addFieldId)

            items.forEach((item, i) => {
                const parts   = item.split("---")
                const type    = parts[0] !== "NA" ? parts[0] : ""
                const model   = parts[1] !== "NA" ? parts[1] : ""
                const barcode = parts[2] !== "NA" ? parts[2] : ""

                if(i === 0){
                    const typeFields    = document.getElementsByClassName(typeClass)
                    const modelFields   = document.getElementsByClassName(modelClass)
                    const barcodeFields = document.getElementsByClassName(barcodeClass)
                    if(typeFields[0])    typeFields[0].value    = type
                    if(modelFields[0])   modelFields[0].value   = model
                    if(barcodeFields[0]) barcodeFields[0].value = barcode
                } else {
                    // ✅ Increment the global counter by name
                    window[counterName]++

                    addFieldBtn.parentElement.insertAdjacentHTML("beforebegin",
                        `<input type="text" name="${window[counterName]}" class="form-control mt-4 ${typeClass}" placeholder="${placeholder}-${window[counterName]} Type" value="${type}">` +
                        `<input type="text" name="${window[counterName]}" class="form-control mt-2 ${modelClass}" placeholder="${placeholder}-${window[counterName]} Model" value="${model}">` +
                        `<input type="text" name="${window[counterName]}" class="form-control mt-2 ${barcodeClass}" placeholder="${placeholder}-${window[counterName]} Barcode" value="${barcode}">`
                    )
                }
            })
        }

        fillCombinedFields(res.cpu,         "edit_cpu_add_field",         "edit_cpu_model",         "edit_cpu_barcode",         "CPU",     "edit_cpu_field")
        fillCombinedFields(res.ram,         "edit_ram_add_field",         "edit_ram_model",         "edit_ram_barcode",         "RAM",     "edit_ram_field")
        fillCombinedFields(res.storage,     "edit_storage_add_field",     "edit_storage_model",     "edit_storage_barcode",     "Storage", "edit_storage_field")
        fillCombinedFields(res.psu,         "edit_psu_add_field",         "edit_psu_model",         "edit_psu_barcode",         "PSU",     "edit_psu_field")
        fillCombinedFields(res.gpu,         "edit_gpu_add_field",         "edit_gpu_model",         "edit_gpu_barcode",         "GPU",     "edit_gpu_field")
        fillCombinedFields(res.cs,          "edit_cs_add_field",          "edit_cs_model",          "edit_cs_barcode",          "CS",      "edit_cs_field")
        fillCombinedFields(res.ec,          "edit_ec_add_field",          "edit_ec_model",          "edit_ec_barcode",          "EC",      "edit_ec_field")
        fillCombinedFields(res.ups_battery, "edit_ups_battery_add_field", "edit_ups_battery_model", "edit_ups_battery_barcode", "Batt",    "edit_ups_battery_field")

        fillCombinedFieldsWithType(res.id_, "edit_id_add_field", "edit_id_type", "edit_id_model", "edit_id_barcode", "ID", "edit_id_field")
        fillCombinedFieldsWithType(res.od, "edit_od_add_field", "edit_od_type", "edit_od_model", "edit_od_barcode", "OD", "edit_od_field")
        fillCombinedFieldsWithType(res.sp, "edit_sp_add_field", "edit_sp_type", "edit_sp_model", "edit_sp_barcode", "SP", "edit_sp_field")
    }




































}