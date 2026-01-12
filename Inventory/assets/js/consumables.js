if(document.getElementById("consumables")){
    let consumablesTable = new DataTable('#consumables_table',{
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
            },
            { 
                className: 'dt-right', 
                targets: 7
            }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "Search: "
        }
    });

    let consumables_logsTable = new DataTable('#consumables_logs_table',{
        pageLength: 25,
        order: [[5, 'desc']],
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
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
           search: "Search: "
        }
    });

    loadPage();
    // LOAD PAGE DATA
    function loadPage(){
        sole.get("../../controllers/consumables/get_consumables.php").then(res => loadConsumables(res))
    }

    var op_length = [
        "Meter (m)",
        "Centimeter (cm)",
        "Millimeter (mm)",
        "Foot (ft)",
        "Inch (in)",
        "Yard (yd)",
        "Kilometer (km)",
        "Decimeter (dm)",
        "Light Year (ly)",
        "Micrometer (µm)",
        "Parsec (pc)",
        "Astronomical Unit (AU)",
        "Lunar Distance (LD)",
        "Picometer (pm)",
        "Nanometer (nm)",
        "Furlong (fur)",
        "Fathom (fm)",
        "Nautical mile (nmi)",
        "Mile (mi)"
    ];
    var op_weight = [
        "Kilogram (kg)",
        "Milligram (mg)",
        "Gram (g)",
        "Microgram (µg)",
        "Quintal (q)",
        "Carat (ct)",
        "Ton (t)",
        "Short Ton (st)",
        "Long Ton (lt)",
        "Ounce (oz)",
        "Grain (gr)",
        "Dram (dr)",
        "Short Hundredweight (sh cwt)",
        "Long Hundredweight (lg cwt)",
        "Pound (lb)",
        "Stone (st)"
    ];
    var op_volume = [
        "Liter (l)",
        "Milliliter (ml)",
        "Cubic Millimeter (mm³)",
        "Cubic Decimeter (dm³)",
        "Centiliter (cl)",
        "Deciliter (dl)",
        "Cubic Centimeter (cm³)",
        "Cubic Meter (m³)",
        "Hectoliter (hl)",
        "Cubic Foot (ft³)",
        "US Fluid Ounce (US fl oz)",
        "Cubic Yard (yd³)",
        "Cubic Inch (in³)",
        "Acre-foot (af³)",
        "UK Gallon (UK gal)",
        "US Gallon (US gal)",
        "UK Fluid Ounce (UK fl oz)"
    ];
    var op_others = [
        "Piece (pc)",
        "Box (bx)",
        "Sachet (sac)",
        "Sack (fibc)",
        "Tray (tray)",
        "Ream (rm)"
    ];

    const add_consumables_modal = new bootstrap.Modal(document.getElementById('add_consumables'),unclose);
    const edit_consumables_modal = new bootstrap.Modal(document.getElementById('edit_consumables'),unclose);
    const restock_consumables_modal = new bootstrap.Modal(document.getElementById('restock_consumables'),unclose);
    const add_log_modal = new bootstrap.Modal(document.getElementById('add_log_modal'),unclose);
    const delete_consumables_modal = new bootstrap.Modal(document.getElementById('delete_consumables'),unclose);


    var add_consumables = document.getElementById("add_consumables")
    var edit_consumables = document.getElementById("edit_consumables")
    var restock_consumables = document.getElementById("restock_consumables")
    var add_log = document.getElementById("add_log")
    var add_log_m = document.getElementById("add_log_modal")

    var edit_consumable_code = document.getElementById("edit_consumable_code")
    var edit_consumable_description = document.getElementById("edit_consumable_description")
    var edit_consumable_measurement = document.getElementById("edit_consumable_measurement")
    var edit_consumable_unit = document.getElementById("edit_consumable_unit")
    var edit_consumable_stock = document.getElementById("edit_consumable_stock")
    var edit_consumable_restock_point = document.getElementById("edit_consumable_restock_point")
    var edit_consumables_btn = document.getElementById("edit_consumables_btn")

    var consumable_code = document.getElementById("consumable_code")
    var consumable_description = document.getElementById("consumable_description")
    var consumable_measurement = document.getElementById("consumable_measurement")
    var consumable_unit = document.getElementById("consumable_unit")
    var consumable_stock = document.getElementById("consumable_stock")
    var consumable_restock_point = document.getElementById("consumable_restock_point")
    var add_consumables_btn = document.getElementById("add_consumables_btn")

    var delete_consumables_description = document.getElementById("delete_consumables_description")
    var delete_consumables_btn = document.getElementById("delete_consumables_btn")

    var search_consumable = document.getElementById("search_consumable")
    var restock_consumables_code = document.getElementById("restock_consumables_code")
    var restock_consumables_stock = document.getElementById("restock_consumables_stock")
    var restock_consumables_description = document.getElementById("restock_consumables_description")
    var restock_consumables_btn = document.getElementById("restock_consumables_btn")
    var restock_quantity = document.getElementById("restock_quantity")

    var consumable_badge_danger = document.getElementById("consumable_badge_danger")
    var consumable_badge_success = document.getElementById("consumable_badge_success")

    var generate_link_controls = document.getElementById("generate_link_controls")
    var generate_link_btn = document.getElementById("generate_link_btn")
    var regenerate_link_btn = document.getElementById("regenerate_link_btn")
    var delete_link_btn = document.getElementById("delete_link_btn")
    var add_log_link = document.getElementById("add_log_link")
    var show_logs = document.getElementById("show_logs")
    var location_ = window.location.href
    
    var cons = document.getElementById("cons")
    var cons_log = document.getElementById("cons_log")

    show_logs.addEventListener("click", function() {
        location_ = location_ + "&sub=consumable-logs"
        window.location.href = location_
    })

    var params = new URLSearchParams(location_)
    if(params.has('sub')){
        if(params.get('sub') == "consumable-logs"){
            cons.hidden = true
            cons_log.hidden = false
        }else{
            cons.hidden = false
            cons_log.hidden = true
        }
    }else{
        cons.hidden = false
    }

    sole.get("../../controllers/consumables/get_consumables_logs.php")
    .then(res => {
        consumables_logsTable.clear().draw();
        var datas = []
        var ids = []
        res.logs.forEach(log => {
            res.users.forEach(user => {
                if(log.uid == user.id){
                    !ids.includes(user.id) ? ids.push(user.id) : null
                    datas.push([log["id"],user["name"],log["cid"],log["remarks"],log["quantity_deduction"],log["date"] + " " + log["time"]])
                }
            })
        });

        res.logs.forEach(log => {
            if(!ids.includes(parseInt(log.uid))){
                datas.push([log["id"],"Others",log["cid"],log["remarks"],log["quantity_deduction"],log["date"] + " " + log["time"]])
            }
        })

        res.consumables.forEach(cons => {
            for (let i = 0; i < datas.length; i++) {
                if(datas[i][2] == cons.id){
                    datas[i][2] = cons.description
                }
            }
        })

        datas.forEach(data => {
            consumables_logsTable.row.add([
                data[0],
                data[1],
                data[2],
                data[3] == "-" ? "" : data[3],
                data[4],
                data[5]
            ]).draw(false)
        })
    })

    add_consumables.addEventListener('shown.bs.modal', function () {
        sole.get("../../controllers/consumables/get_code.php")
        .then(res => {
            consumable_code.innerHTML = "Code: <b>" + res + "</b>"
        })
        consumable_description.focus()
    })

    edit_consumables.addEventListener('shown.bs.modal', function () {
        edit_consumable_description.focus()
    })

    add_log_m.addEventListener('shown.bs.modal', function () {
        sole.get("../../controllers/consumables/find_link.php")
        .then(res => {
            if(res.status){
                let url = window.location.origin + window.location.pathname;
                let baseUrl = url.substring(0, url.lastIndexOf('/') + 1);
                add_log_link.setAttribute("target","_blank");
                add_log_link.setAttribute("href",baseUrl + "consumables-log.php?glog="+res.link);
                add_log_link.innerText = baseUrl + "consumables-log.php?glog="+res.link;
                regenerate_link_btn.hidden = false
                delete_link_btn.hidden = false
                generate_link_btn.hidden = true
            }else{ 
                add_log_link.innerText = "Click Generate Link"
                add_log_link.removeAttribute("target");
                add_log_link.setAttribute("href","#");
                regenerate_link_btn.hidden = true
                delete_link_btn.hidden = true
                generate_link_btn.hidden = false
            }
        })
    })

    restock_consumables.addEventListener('shown.bs.modal', function () {
        search_consumable.focus()
    })

    add_log.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            add_log_modal.show()
        }else{
            bs5.toast("info","Please operate as group member.")
        }
    })

    add_consumables_btn.addEventListener("click",function(){
        if(consumable_description.value){
            sole.post("../../controllers/consumables/add_consumables.php",{
                uid: localStorage.getItem("userid"),
                description: consumable_description.value,
                measurement: consumable_measurement.value,
                unit: consumable_unit.value,
                stock: consumable_stock.value,
                restock_point: consumable_restock_point.value
            }).then(res => validateResponse(res,"add_consumables"))
        }else{
            bs5.toast("warning","Please add description.")
        }
    })

    edit_consumables_btn.addEventListener("click",function(){
        if(edit_consumable_description.value){
            sole.post("../../controllers/consumables/edit_consumables.php",{
                uid: localStorage.getItem("userid"),
                id: this.getAttribute("c-id"),
                description: edit_consumable_description.value,
                measurement: edit_consumable_measurement.value,
                unit: edit_consumable_unit.value,
                stock: edit_consumable_stock.value,
                restock_point: edit_consumable_restock_point.value
            }).then(res => validateResponse(res,"edit_consumables"))
        }else{
            bs5.toast("warning","Please add description.")
        }
    })

    consumable_measurement.addEventListener("change",function(){
        consumable_unit.innerText = ""
        if(this.value == "Length"){
            op_length.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                consumable_unit.appendChild(opt)
            });
        }else if(this.value == "Weight"){
            op_weight.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                consumable_unit.appendChild(opt)
            });
        }else if(this.value == "Volume"){
            op_volume.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                consumable_unit.appendChild(opt)
            });
        }else if(this.value == "Others"){
            op_others.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                consumable_unit.appendChild(opt)
            });
        }else{
            consumable_unit.innerHTML = "<option value=\"\">-- Select Unit --</option>"
        }
    })

    edit_consumable_measurement.addEventListener("change",function(){
        editSelectMeasurement(this.value)
    })

    delete_consumables_btn.addEventListener("click",function(){
        sole.post("../../controllers/consumables/delete_consumables.php",{
            id: this.getAttribute("c-id")
        }).then(res => validateResponse(res,"delete_consumables"))
    })

    consumable_stock.addEventListener("input",function(){
        if(/^0+\d/.test(consumable_stock.value)) {
            consumable_stock.value = consumable_stock.value.replace(/^0+(?=\d)/, '');
        }
        if(consumable_stock.value < 0){
            consumable_stock.value = 0
        }
        if(!consumable_stock.value){
            consumable_stock.value = 0
        }
    })

    consumable_restock_point.addEventListener("input",function(){
        if(/^0+\d/.test(consumable_restock_point.value)) {
            consumable_restock_point.value = consumable_restock_point.value.replace(/^0+(?=\d)/, '');
        }
        if(consumable_restock_point.value < 0){
            consumable_restock_point.value = 0
        }
        if(!consumable_restock_point.value){
            consumable_restock_point.value = 0
        }
    })

    edit_consumable_stock.addEventListener("input",function(){
        if(/^0+\d/.test(edit_consumable_stock.value)) {
            edit_consumable_stock.value = edit_consumable_stock.value.replace(/^0+(?=\d)/, '');
        }
        if(edit_consumable_stock.value < 0){
            edit_consumable_stock.value = 0
        }
        if(!edit_consumable_stock.value){
            edit_consumable_stock.value = 0
        }
    })

    edit_consumable_restock_point.addEventListener("input",function(){
        if(/^0+\d/.test(edit_consumable_restock_point.value)) {
            edit_consumable_restock_point.value = edit_consumable_restock_point.value.replace(/^0+(?=\d)/, '');
        }
        if(edit_consumable_restock_point.value < 0){
            edit_consumable_restock_point.value = 0
        }
        if(!edit_consumable_restock_point.value){
            edit_consumable_restock_point.value = 0
        }
    })

    search_consumable.addEventListener("input",function(){
        sole.post("../../controllers/consumables/search_consumable.php",{
            search: search_consumable.value
        }).then(res => {
            if(res.length && search_consumable.value){
                restock_consumables_code.innerText = res[0].code
                restock_consumables_description.innerText = res[0].description
                restock_consumables_stock.innerText = res[0].stock
                restock_consumables_btn.setAttribute("sid",res[0].id)   
                if(parseFloat(res[0].stock) <= parseFloat(res[0].restock_point)){
                    consumable_badge_danger.hidden = false
                    consumable_badge_success.hidden = true
                }else{
                    consumable_badge_danger.hidden = true
                    consumable_badge_success.hidden = false
                }
            }else{
                restock_consumables_code.innerText = ""
                restock_consumables_description.innerText = ""
                restock_consumables_stock.innerText = ""
                restock_consumables_btn.setAttribute("sid","")
                consumable_badge_danger.hidden = true
                consumable_badge_success.hidden = true
            }
        })
    })

    restock_consumables_btn.addEventListener("click",function(){
        if(restock_consumables_btn.getAttribute("sid")){
            if(restock_quantity.value > 0){
                sole.post("../../controllers/consumables/restock_consumables.php",{
                    sid: restock_consumables_btn.getAttribute("sid"),
                    quantity: restock_quantity.value,
                }).then(res => validateResponse(res,"restock_consumables"))
            }else{
                bs5.toast("warning","Please enter a valid quantity.")
            }
        }
    })

    restock_quantity.addEventListener("input",function(){
        if(/^0+\d/.test(restock_quantity.value)) {
            restock_quantity.value = restock_quantity.value.replace(/^0+(?=\d)/,    '');
        }
        if(restock_quantity.value < 0){
            restock_quantity.value = 0
        }
        if(!restock_quantity.value){
            restock_quantity.value = 0
        }
    })

    generate_link_btn.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            sole.post("../../controllers/consumables/generate_link.php",{
                type: "generate"
            }).then(res => {
                let url = window.location.origin + window.location.pathname;
                let baseUrl = url.substring(0, url.lastIndexOf('/') + 1);
                add_log_link.setAttribute("target","_blank");
                add_log_link.setAttribute("href",baseUrl + "consumables-log.php?glog="+res);
                add_log_link.innerText = baseUrl + "consumables-log.php?glog="+res;

                regenerate_link_btn.hidden = false
                delete_link_btn.hidden = false
                generate_link_btn.hidden = true
            })
            
        }else{
            bs5.toast("info","Please operate as group member.")
        }
    })

    if(localStorage.getItem("privileges") == "User"){
        generate_link_controls.hidden = true
    }

    regenerate_link_btn.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            sole.post("../../controllers/consumables/generate_link.php",{
                type: "regenerate",
                link: add_log_link.getAttribute("href").split("glog=")[1]
            }).then(res => {
                let url = window.location.origin + window.location.pathname;
                let baseUrl = url.substring(0, url.lastIndexOf('/') + 1);
                add_log_link.setAttribute("target","_blank");
                add_log_link.setAttribute("href",baseUrl + "consumables-log.php?glog="+res);
                add_log_link.innerText = baseUrl + "consumables-log.php?glog="+res;
            })
        }
    })

    delete_link_btn.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            sole.post("../../controllers/consumables/delete_link.php",{
                link: add_log_link.getAttribute("href").split("glog=")[1]
            }).then(res => {
                add_log_link.innerText = "Click Generate Link"
                add_log_link.removeAttribute("target");
                add_log_link.setAttribute("href","#");
                
                regenerate_link_btn.hidden = true
                delete_link_btn.hidden = true
                generate_link_btn.hidden = false
            })
        }
    })

    function loadConsumables(res){
        consumablesTable.clear().draw();
        res.consumables.forEach(e => {
            consumablesTable.row.add([
                e["id"],
                e["code"],
                e["description"],
                e["measurement"],
                e["unit"],
                e["stock"],
                parseFloat(e["stock"]) <= parseFloat(e["restock_point"]) ? "<span class=\"badge bg-danger\">Low Stock</span>" :  "<span class=\"badge bg-success\">In Stock</span>",
                " <button id=\"edit_consumables_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"edit_consumables_row btn btn-sm btn-secondary mb-1\"><i c-id=\""+ e["id"] +"\" class=\"edit_consumables_row fa fa-edit\"></i></button>"+
                " <button id=\"delete_consumables_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"delete_consumables_row btn btn-sm btn-danger mb-1\"><i c-id=\""+ e["id"] +"\" class=\"delete_consumables_row fa fa-trash-o\"></i></button>"
            ]).draw(false) 
        });

        document.querySelector('#consumables_table').addEventListener("click", e=>{
            let tr = "";
            if(e.target.tagName == "I"){
                tr = e.target.parentNode.parentNode.parentNode.children
            }
            if(e.target.tagName == "BUTTON"){
                tr = e.target.parentNode.parentNode.children    
            }
            if(e.target.classList.contains('edit_consumables_row')) {
                edit_consumables_btn.setAttribute("c-id",e.target.getAttribute("c-id"))
                sole.post("../../controllers/consumables/find_consumables.php",{
                    id: e.target.getAttribute("c-id")
                }).then(res => editConsumablesForm(res))
            }
            if(e.target.classList.contains('delete_consumables_row')){
                delete_consumables_description.innerText = tr[1].innerText
                delete_consumables_btn.setAttribute("c-id",e.target.getAttribute("c-id"))
                delete_consumables_modal.show()
            }
        })
    }

    function editSelectMeasurement(data){
        edit_consumable_unit.innerText = ""
        if(data == "Length"){
            op_length.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                edit_consumable_unit.appendChild(opt)
            });
        }else if(data == "Weight"){
            op_weight.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                edit_consumable_unit.appendChild(opt)
            });
        }else if(data == "Volume"){
            op_volume.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                edit_consumable_unit.appendChild(opt)
            });
        }else if(data == "Others"){
            op_others.forEach(op => {
                var opt = document.createElement("option")
                opt.value = op
                opt.innerText = op
                edit_consumable_unit.appendChild(opt)
            });
        }else{
            edit_consumable_unit.innerHTML = "<option value=\"\">-- Select Unit --</option>"
        }
    }

    function editConsumablesForm(res){
        editSelectMeasurement(res["consumable"][0]["measurement"])
        edit_consumable_code.innerHTML = "Code: <b>" + res["consumable"][0]["code"] + "</b>"
        edit_consumable_description.value = res["consumable"][0]["description"]
        edit_consumable_measurement.value = res["consumable"][0]["measurement"]
        edit_consumable_unit.value = res["consumable"][0]["unit"]
        edit_consumable_stock.value = res["consumable"][0]["stock"]
        edit_consumable_restock_point.value = res["consumable"][0]["restock_point"]
        edit_consumables_modal.show()
    }

    function validateResponse(res,func){
        if(res.status){
            if(func == "add_consumables"){
                consumable_description.value = ""
                consumable_measurement.value = ""
                consumable_unit.innerHTML = "<option value=\"\">-- Select Unit --</option>"
                consumable_stock.value = 0
                consumable_restock_point.value = 0
                add_consumables_modal.hide()
                loadPage()
            }
            if(func == "edit_consumables"){
                edit_consumable_description.value = ""
                edit_consumable_measurement.value = ""
                edit_consumable_unit.innerHTML = "<option value=\"\">-- Select Unit --</option>"
                edit_consumable_stock.value = 0
                edit_consumable_restock_point.value = 0
                edit_consumables_modal.hide()
                loadPage()
            }
            if(func == "delete_consumables"){
                delete_consumables_modal.hide()
                loadPage()
            }
            if(func == "restock_consumables"){
                restock_quantity.value = 0
                search_consumable.value = ""
                restock_consumables_btn.setAttribute("sid","")
                restock_consumables_code.innerText = ""
                restock_consumables_description.innerText = ""
                restock_consumables_stock.innerText = ""
                consumable_badge_danger.hidden = true
                consumable_badge_success.hidden = true
                restock_consumables_modal.hide()
                loadPage()
            }
            bs5.toast(res.type,res.message,res.size)
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }
}