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
                targets: 8
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
                className: 'word-nl', 
                target: 4
            }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: localStorage.getItem("privileges") != "User" ? "<button id=\"clear_consumable_logs_btn\" class=\"btn btn-sm btn-danger me-3\">Clear Logs</button>" : "" + "Search: "
        }
    });

    let consumables_RequestsTable = new DataTable('#consumables_requests_table',{
        pageLength: 25,
        order: [[6, 'desc']],
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        // scrollX: true,
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
                className: 'word-nl', 
                target: 4
            }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "Search: "
        }
    });

    let consumables_RequestsOthersTable = new DataTable('#consumables_requests_others_table',{
        pageLength: 25,
        order: [[6, 'desc']],
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        // scrollX: true,
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
                className: 'word-nl', 
                target: 4
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
        "Ream (rm)",
        "Can (cn)"
    ];

    const add_consumables_modal = new bootstrap.Modal(document.getElementById('add_consumables'),unclose);
    const edit_consumables_modal = new bootstrap.Modal(document.getElementById('edit_consumables'),unclose);
    const restock_consumables_modal = new bootstrap.Modal(document.getElementById('restock_consumables'),unclose);
    const add_log_modal = new bootstrap.Modal(document.getElementById('add_log_modal'),unclose);
    const delete_consumables_modal = new bootstrap.Modal(document.getElementById('delete_consumables'),unclose);
    const clear_consumable_log_modal = new bootstrap.Modal(document.getElementById('clear_consumable_log'),unclose);
    const cancel_request_modal = new bootstrap.Modal(document.getElementById('cancel_request'),unclose);
    const declined_request_modal = new bootstrap.Modal(document.getElementById('declined_request'),unclose);
    const decline_request_modal = new bootstrap.Modal(document.getElementById('decline_request'),unclose);


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

    var cancel_request_btn = document.getElementById("cancel_request_btn")
    var declined_request_btn = document.getElementById("declined_request_btn")
    var declined_request_remarks = document.getElementById("declined_request_remarks")

    var decline_request_btn = document.getElementById("decline_request_btn")
    var decline_request_remarks = document.getElementById("decline_request_remarks")

    var search_consumable = document.getElementById("search_consumable")
    var restock_consumables_code = document.getElementById("restock_consumables_code")
    var restock_consumables_stock = document.getElementById("restock_consumables_stock")
    var restock_consumables_description = document.getElementById("restock_consumables_description")
    var restock_consumables_btn = document.getElementById("restock_consumables_btn")
    var restock_quantity = document.getElementById("restock_quantity")

    var consumable_badge_danger = document.getElementById("consumable_badge_danger")
    var consumable_badge_success = document.getElementById("consumable_badge_success")
    var consumable_badge_warning = document.getElementById("consumable_badge_warning")

    var generate_link_controls = document.getElementById("generate_link_controls")
    generate_link_controls.hidden = true
    var generate_link_btn = document.getElementById("generate_link_btn")
    var regenerate_link_btn = document.getElementById("regenerate_link_btn")
    var delete_link_btn = document.getElementById("delete_link_btn")
    var add_log_link = document.getElementById("add_log_link")
    var your_passkey = document.getElementById("your_passkey")
    var group_links = document.getElementById("group_links")
    var show_logs = document.getElementById("show_logs")
    var consumable_request = document.getElementById("consumable_request")
    var location_ = window.location.href
    
    var cons = document.getElementById("cons")
    var cons_log = document.getElementById("cons_log")
    var cons_request = document.getElementById("cons_request")
    var cons_request_others = document.getElementById("cons_request_others")
    var request_menu_btn = document.getElementById("request_menu_btn")
    var other_request = document.getElementById("other_request")
    var your_request = document.getElementById("your_request")

    var clear_consumable_logs_btn = document.getElementById("clear_consumable_logs_btn")
    var clear_consumable_log_confirm = document.getElementById("clear_consumable_log_confirm")
    var glink_temp = " +++ +++ "

    if(localStorage.getItem("privileges") != "User"){
        clear_consumable_logs_btn.addEventListener("click", function () {
            if(JSON.parse(localStorage.getItem("g_member"))){
                clear_consumable_log_modal.show()
            }else{
                ss.toast("Please operate as group member.","info",null,null,"#212529")
            }
        })    
    }
    
    clear_consumable_log_confirm.addEventListener("click", function () {
        sole.get("../../controllers/consumables/delete_consumables_log.php")
        .then(res => {
            ss.toast(res.message,res.type,null,null,"#212529")
            clear_consumable_log_modal.hide()
            get_consumables_logs()
        })
    })

    show_logs.addEventListener("click", function() {
        location_ = location_ + "&sub=consumable-logs"
        window.location.href = location_
    })
    consumable_request.addEventListener("click", function() {
        location_ = location_ + "&sub=consumable-requests"
        window.location.href = location_
    })

    var params = new URLSearchParams(location_)
    if(params.has('sub')){
        if(params.get('sub') == "consumable-logs" || params.get('sub') == "consumable-logs#"){
            cons.hidden = true
            cons_request.hidden = true
            cons_log.hidden = false
            request_menu_btn.hidden = true
        }else if(params.get('sub') == "consumable-requests" || params.get('sub') == "consumable-requests#"){
            cons.hidden = true
            cons_request.hidden = false
            cons_log.hidden = true
            if(localStorage.getItem("c_authority") == "true"){
                cons_request_others.hidden = false
                cons_request.hidden = true
                request_menu_btn.hidden = false    
            }else{
                request_menu_btn.hidden = true 
            }
            
        }else{
            cons.hidden = false
            cons_request.hidden = true
            cons_log.hidden = true
            request_menu_btn.hidden = true
        }
    }else{
        cons.hidden = false
        cons_request.hidden = true
        cons_log.hidden = true
        request_menu_btn.hidden = true
    }

    your_request.addEventListener("click", e => {
        cons_request_others.hidden = true
        cons_request.hidden = false
        your_request.classList.add("alert-dark")
        other_request.classList.remove("alert-dark")
    })

    other_request.addEventListener("click", e => {
        cons_request_others.hidden = false
        cons_request.hidden = true
        your_request.classList.remove("alert-dark")
        other_request.classList.add("alert-dark")
    })

    if(localStorage.getItem("privileges") != "User"){
        cons.children[0].hidden = false
        cons.children[1].hidden = false
    }

    function get_consumables_logs() {
        sole.get("../../controllers/consumables/get_consumables_logs.php")
        .then(res => {
            consumables_logsTable.clear();
            var datas = []
            var ids = []
            res.logs.forEach(log => {
                res.users.forEach(user => {
                    if(log.uid == user.id){
                        !ids.includes(user.id) ? ids.push(user.id) : null
                        datas.push([log.id,user.name,log.cid,log.quantity_deduction,log.remarks,log.date + " " + log.time])
                    }
                })
            });

            res.logs.forEach(log => {
                if(!ids.includes(parseInt(log.uid))){
                    datas.push([log.id,"Others",log.cid,log.quantity_deduction,log.remarks,log.date + " " + log.time])
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
                    data[3],
                    data[4] == "-" ? "" : data[4],
                    data[5]
                ])
            })
            consumables_logsTable.draw()
        })    
    }
    function get_consumables_requests_others(){
        sole.post("../../controllers/consumables/get_consumables_requests.php",{
            type : "group"
        }).then(res => {
            consumables_RequestsOthersTable.clear();
            var datas = []
            var ids = []

            res.requests.forEach(request => {
                res.consumables.forEach(cons => {
                    if(cons.id == request.cid){
                        ids.push(cons.id)
                        datas.push([request.id,request.uid,cons.description,request.requested_quantity,request.remarks,request.status,request.date + " " + request.time])
                    }
                })
            })

            res.requests.forEach(request => {
                if(!ids.includes(parseInt(request.cid))){
                    datas.push([request.id,request.uid,"Not Found",request.requested_quantity,request.remarks,request.status,request.date + " " + request.time])
                }
            })

            datas.forEach(data => {
                res.users.forEach(user => {
                    if(user.id == parseInt(data[1])){
                        consumables_RequestsOthersTable.row.add([
                            data[0],
                            user.name,
                            data[2] == "Not Found" ? "<h6 class=\"text-danger\">"+data[2]+"</h6>" : data[2],
                            data[3],
                            data[4] == "-" ? "" : data[4],
                            data[5] == "For Approval" ? "<h6 class=\"text-primary\">"+data[5]+"</h6>" : data[5] == "Approved" ? "<h6 class=\"text-success\">"+data[5]+"</h6>" : "<h6 class=\"text-danger\">"+data[5]+"</h6>",
                            data[6],
                            get_UserRequestOthersBotton(data)
                        ])
                    }
                })
                consumables_RequestsOthersTable.draw()
            })
        })
    }

    document.querySelector('#consumables_requests_others_table').addEventListener("click", e=>{
        let tr = "";
        if(e.target.tagName == "I"){
            tr = e.target.parentNode.parentNode.parentNode.children
        }
        if(e.target.tagName == "BUTTON"){
            tr = e.target.parentNode.parentNode.children    
        }
        if(e.target.classList.contains('declined_request_row')) {
            declined_request_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
            sole.post("../../controllers/consumables/get_request.php",{
                id : e.target.getAttribute("r-id")
            }).then(res => {
                declined_request_remarks.innerText = res[0].declined_remarks != "-" ? res[0].declined_remarks : "Request is invalid."
            })
            declined_request_modal.show()
        }

        if(e.target.classList.contains('decline_request_row')) {
            decline_request_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
            decline_request_modal.show()
        }

        if(e.target.classList.contains('approve_request_row')) {
            sole.post("../../controllers/consumables/approve_request.php",{
                id : e.target.getAttribute("r-id")
            }).then(res => {
                get_consumables_requests_others()
                ss.toast(res.message,res.type,null,null,"#212529")
            })
        }
        if(e.target.classList.contains('claimed_request_row')) {
            sole.post("../../controllers/consumables/claimed_request.php",{
                id : e.target.getAttribute("r-id")
            }).then(res => {
                get_consumables_requests_others()
                ss.toast(res.message,res.type,null,null,"#212529") 
            })
        }
        if(e.target.classList.contains('cancel_request_row')) {
            cancel_request_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
            cancel_request_modal.show()
        }
    })

    function get_consumables_requests(){
        sole.post("../../controllers/consumables/get_consumables_requests.php",{
            type : "user"
        }).then(res => {
            consumables_RequestsTable.clear();
            var datas = []
            var ids = []

            res.requests.forEach(request => {
                res.consumables.forEach(cons => {
                    if(cons.id == request.cid){
                        ids.push(cons.id)
                        datas.push([request.id,request.gid,cons.description,request.requested_quantity,request.remarks,request.status,request.date + " " + request.time])
                    }
                })
            })

            res.requests.forEach(request => {
                if(!ids.includes(parseInt(request.cid))){
                    datas.push([request.id,request.gid,"Not Found",request.requested_quantity,request.remarks,request.status,request.date + " " + request.time])
                }
            })

            datas.forEach(data => {
                res.groups.forEach(group => {
                    if(group.id == parseInt(data[1])){
                        consumables_RequestsTable.row.add([
                            data[0],
                            group.group_name,
                            data[2] == "Not Found" ? "<h6 class=\"text-danger\">"+data[2]+"</h6>" : data[2],
                            data[3],
                            data[4] == "-" ? "" : data[4],
                            data[5] == "For Approval" ? "<h6 class=\"text-primary\">"+data[5]+"</h6>" : data[5] == "Approved" ? "<h6 class=\"text-success\">"+data[5]+"</h6>" : "<h6 class=\"text-danger\">"+data[5]+"</h6>",
                            data[6],
                            get_UserRequestBotton(data)
                        ])
                    }
                })
                consumables_RequestsTable.draw()
            })
        })
    }

    document.querySelector('#consumables_requests_table').addEventListener("click", e=>{
        let tr = "";
        if(e.target.tagName == "I"){
            tr = e.target.parentNode.parentNode.parentNode.children
        }
        if(e.target.tagName == "BUTTON"){
            tr = e.target.parentNode.parentNode.children    
        }
        if(e.target.classList.contains('cancel_request_row')) {
            cancel_request_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
            cancel_request_modal.show()
        }
        if(e.target.classList.contains('declined_request_row')) {
            declined_request_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
            sole.post("../../controllers/consumables/get_request.php",{
                id : e.target.getAttribute("r-id")
            }).then(res => {
                declined_request_remarks.innerText = res[0].declined_remarks
            })
            declined_request_modal.show()
        }
    })

    function get_UserRequestBotton(data){
        if(data[5] == "For Approval"){
            return "<button r-id=\""+data[0]+"\" class=\"cancel_request_row btn btn-sm btn-secondary\"><i r-id=\""+data[0]+"\" class=\"cancel_request_row fa fa-trash\"></i> Cancel</button>";
        }else if(data[5] == "Declined"){
            return "<button r-id=\""+data[0]+"\" class=\"declined_request_row btn btn-sm btn-danger alert-danger fw-bolder\"><i r-id=\""+data[0]+"\" class=\"declined_request_row fa fa-question-circle-o\"></i> Details</button>";
        }else{
            return ""
        }
    }

    function get_UserRequestOthersBotton(data){
        if(data[2] == "Not Found"){
            return "<button r-id=\""+data[0]+"\" class=\"cancel_request_row btn btn-sm btn-secondary\"><i r-id=\""+data[0]+"\" class=\"cancel_request_row fa fa-trash\"></i> Cancel</button>";
        }
        if(data[5] == "For Approval"){
            return "<button r-id=\""+data[0]+"\" class=\"approve_request_row btn btn-sm btn-primary\"><i r-id=\""+data[0]+"\" class=\"approve_request_row fa fa-check\"></i> Approve </button> <button r-id=\""+data[0]+"\" class=\"decline_request_row btn btn-sm btn-danger\"><i r-id=\""+data[0]+"\" class=\"decline_request_row fa fa-remove\"></i> Decline</button>";
        }else if(data[5] == "Declined"){
            return "<button r-id=\""+data[0]+"\" class=\"declined_request_row btn btn-sm btn-danger alert-danger fw-bolder\"><i r-id=\""+data[0]+"\" class=\"declined_request_row fa fa-question-circle-o\"></i> Details</button>";
        }else if(data[5] == "Approved"){
            return "<button r-id=\""+data[0]+"\" class=\"claimed_request_row btn btn-sm btn-success\"><i r-id=\""+data[0]+"\" class=\"claimed_request_row fa fa-check\"></i> Mark as Claimed</button>";
        }else{
            return ""
        }
    }

    cancel_request_btn.addEventListener("click", e => {
        deleteRequest(cancel_request_btn)
        cancel_request_modal.hide()
    })

    declined_request_btn.addEventListener("click", e => {
        deleteRequest(declined_request_btn)
        declined_request_modal.hide()
    })

    decline_request_btn.addEventListener("click", e => {
        if(!decline_request_remarks.value){
            ss.toast("Please input a reason for declining.","warning",null,null,"#212529")
            return
        }
        sole.post("../../controllers/consumables/decline_request.php",{
            id : decline_request_btn.getAttribute("r-id"),
            remarks : decline_request_remarks.value ? decline_request_remarks.value : "-"
        }).then(res => {
            decline_request_modal.hide()
            ss.toast(res.message,res.type,null,null,"#212529")
            get_consumables_requests_others()
        })
    })

    function deleteRequest(el){
        sole.post("../../controllers/consumables/cancel_request.php",{
            id : el.getAttribute("r-id")
        }).then(res => {
            ss.toast(res.message,res.type,null,null,"#212529")
            get_consumables_requests()
            if(localStorage.getItem("c_authority") == "true"){
                get_consumables_requests_others()
            }
        })
    }

    get_consumables_logs()
    get_consumables_requests()
    if(localStorage.getItem("c_authority") == "true"){
        get_consumables_requests_others()
    }
    

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
        your_passkey.innerText = localStorage.getItem("passkey")
        getLinks()
    })

    function getLinks(call = false){
        sole.get("../../controllers/consumables/find_link.php")
        .then(res => {
            group_links.innerHTML = ""
            var op = document.createElement("option")
            op.value = " +++ +++ "
            op.selected = true
            op.disabled = true
            op.innerText = "-- Group List --"
            group_links.appendChild(op)
            
            var lid = []

            res.links.forEach(li => {
                lid.push(li[0])
            })

            res.groups.forEach(gr => {
                var op = document.createElement("option")
                if(lid.includes(gr.id)){
                    res.links.forEach(li => {
                        if(li[0] == gr.id){
                            op.value = gr.group_name + "+++" + li[1] + "+++" + "1"
                        }
                    })
                }else{
                    op.value = gr.group_name + "+++" + "Link unavailable, please ask the " + gr.group_name + " to generate a link." + "+++" + "0"
                }
                op.innerText = gr.group_name
                group_links.appendChild(op)
            })
            if(call){
                group_links.value = glink_temp    
            }else{
                add_log_link.innerText = "Please select a group."
                add_log_link.classList.add("no-event")
                generate_link_controls.hidden = true
            }
        })
    }

    group_links.addEventListener("change", e => {
        var data = group_links.value.split("+++")
        add_log_link.innerText = data[1]

        let url = window.location.origin + window.location.pathname;
        let baseUrl = url.substring(0, url.lastIndexOf('/') + 1);
        add_log_link.setAttribute("target","_blank");

        if(data[2] == "1"){
            add_log_link.setAttribute("href",baseUrl + "consumables-log.php?glog="+data[1]);
            add_log_link.innerHTML = "<span class=\"fa fa-external-link\"></span> " + baseUrl + "consumables-log.php?glog="+data[1];
            add_log_link.classList.remove("no-event")
        }else{
            add_log_link.setAttribute("href","#");
            if(data[0] == localStorage.getItem("g_name")){
                add_log_link.innerText = localStorage.getItem("privileges") == "User" ? "Link unavailable, please ask your group supervisor to generate a link." : "Click generate link."
            }
            add_log_link.classList.add("no-event")
        }
        if(data[0] == localStorage.getItem("g_name") && localStorage.getItem("privileges") != "User"){
            generate_link_controls.hidden = false
            if(data[2] == "1"){
                generate_link_btn.hidden = true
                regenerate_link_btn.hidden = false
                delete_link_btn.hidden = false
            }else{
                generate_link_btn.hidden = false
                regenerate_link_btn.hidden = true
                delete_link_btn.hidden = true
            }
        }else{
            generate_link_controls.hidden = true
        }
    })

    restock_consumables.addEventListener('shown.bs.modal', function () {
        search_consumable.focus()
    })

    add_log.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            add_log_modal.show()
        }else{
            ss.toast("Please operate as group member.","info",null,null,"#212529")
        }
    })

    add_consumables_btn.addEventListener("click",function(){
        if(!consumable_measurement.value){
            ss.toast("Please select measurement.","warning",null,null,"#212529")
            return
        }
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
            ss.toast("Please add description.","warning",null,null,"#212529")
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
            ss.toast("Please add description.","warning",null,null,"#212529")
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

                if(parseFloat(res[0].stock) <= parseFloat(res[0].restock_point) && parseFloat(res[0].stock) != 0){
                    consumable_badge_warning.hidden = false
                    consumable_badge_danger.hidden = true
                    consumable_badge_success.hidden = true
                }else if(parseFloat(res[0].stock) == 0){
                    consumable_badge_warning.hidden = true
                    consumable_badge_danger.hidden = false
                    consumable_badge_success.hidden = true
                }else{
                    consumable_badge_warning.hidden = true
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
                consumable_badge_warning.hidden = true
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
                ss.toast("Please enter a valid quantity.","warning",null,null,"#212529")
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
                add_log_link.innerHTML = "<span class=\"fa fa-external-link\"></span> " + baseUrl + "consumables-log.php?glog="+res;

                add_log_link.classList.remove("no-event")

                regenerate_link_btn.hidden = false
                delete_link_btn.hidden = false
                generate_link_btn.hidden = true
                glink_temp = localStorage.getItem("g_name") + "+++" + res + "+++" + "1"
                getLinks(true)
            })
            
        }else{
            ss.toast("Please operate as group member.","info",null,null,"#212529")
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
                add_log_link.innerHTML = "<span class=\"fa fa-external-link\"></span> " + baseUrl + "consumables-log.php?glog="+res;
                glink_temp = localStorage.getItem("g_name") + "+++" + res + "+++" + "1"

                add_log_link.classList.remove("no-event")

                getLinks(true)
            })
        }
    })

    delete_link_btn.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            sole.post("../../controllers/consumables/delete_link.php",{
                link: add_log_link.getAttribute("href").split("glog=")[1]
            }).then(res => {
                add_log_link.innerText = localStorage.getItem("privileges") == "User" ? "Link unavailable, please ask your group supervisor to generate a link." : "Click generate link."
                add_log_link.removeAttribute("target");
                add_log_link.setAttribute("href","#");

                add_log_link.classList.add("no-event")
                
                regenerate_link_btn.hidden = true
                delete_link_btn.hidden = true
                generate_link_btn.hidden = false
            })
        }
    })

    function loadConsumables(res){
        consumablesTable.clear();
        res.consumables.forEach(e => {
            consumablesTable.row.add([
                e["id"],
                e["code"],
                e["description"],
                e["measurement"],
                e["unit"],
                e["stock"],
                parseFloat(e["stock"]) < parseFloat(e["restock_point"]) && parseFloat(e["stock"]) > 0 ? "<span class=\"badge bg-warning\">Low Stock</span>" : (parseFloat(e["stock"]) == 0) ? "<span class=\"badge bg-danger\">Out of Stock</span>" : "<span class=\"badge bg-success\">In Stock</span>",
                e["last_restock"] != "-" ? e["last_restock"] : e["created_at"],
                " <button id=\"edit_consumables_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"edit_consumables_row btn btn-sm btn-secondary mb-1\"><i c-id=\""+ e["id"] +"\" class=\"edit_consumables_row fa fa-edit\"></i></button>"+
                " <button id=\"delete_consumables_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"delete_consumables_row btn btn-sm btn-danger mb-1\"><i c-id=\""+ e["id"] +"\" class=\"delete_consumables_row fa fa-trash-o\"></i></button>"
            ])
        });
        consumablesTable.draw()

        localStorage.getItem("privileges") != "User" ? consumablesTable.column(8).visible(true) : consumablesTable.column(8).visible(false)
    }

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
            ss.toast(res.message,res.type,null,null,"#212529")
        }else{
            ss.toast(res.message,res.type,null,null,"#212529")
        }
    }
    document.querySelector('#consumables_table tbody').addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;

        // Remove selected from all rows
        document.querySelectorAll('#consumables_table tbody tr').forEach(r => {
            r.classList.remove('selected');
        });

        // Add selected to clicked row
        row.classList.add('selected');
    });

    document.querySelector('#consumables_logs_table tbody').addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;

        // Remove selected from all rows
        document.querySelectorAll('#consumables_logs_table tbody tr').forEach(r => {
            r.classList.remove('selected');
        });

        // Add selected to clicked row
        row.classList.add('selected');
    });    

    document.querySelector('#consumables_requests_table tbody').addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;

        // Remove selected from all rows
        document.querySelectorAll('#consumables_requests_table tbody tr').forEach(r => {
            r.classList.remove('selected');
        });

        // Add selected to clicked row
        row.classList.add('selected');
    }); 

    document.querySelector('#consumables_requests_others_table tbody').addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;

        // Remove selected from all rows
        document.querySelectorAll('#consumables_requests_others_table tbody tr').forEach(r => {
            r.classList.remove('selected');
        });

        // Add selected to clicked row
        row.classList.add('selected');
    });     
}