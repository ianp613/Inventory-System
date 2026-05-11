if(document.getElementById("terminals")){
    const add_terminal                         = new bootstrap.Modal(document.getElementById('add_terminal'),unclose);
    add_terminal.show()
    let terminalTable = new DataTable('#tb_terminals',{
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        scrollX: false,
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
           search: "<button id=\"add_router_btn\" data-bs-toggle=\"modal\" data-bs-target=\"#add_terminal\" class=\"btn btn-sm btn-dark me-3\"><span class=\"fa fa-plus\"></span> Add Router</button> Search: "
        }
    });


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