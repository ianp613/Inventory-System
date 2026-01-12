if(document.getElementById("routers")){
    let routerTable = new DataTable('#router_table',{
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
           search: "<button id=\"add_router_btn\" data-bs-toggle=\"modal\" data-bs-target=\"#add_router\" class=\"btn btn-sm btn-dark me-3\"><span class=\"fa fa-plus\"></span> Add Router</button> Search: "
        }
    });

    let routerISPTable = new DataTable('#router_isp_table',{
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        scrollX: true,
        columnDefs: [
            { 
                className: 'dt-left', 
                targets: '_all' 
            },
            { orderable: false, targets: 0 }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "Search: ",
           emptyTable: 'Select router'
        },
        searching: false,
        paging: false,
        info: false
    });

    let routerDeleteTable = new DataTable('#delete_router_table',{
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        scrollX: true,
        columnDefs: [
            { 
                className: 'dt-left', 
                targets: '_all' 
            },
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "Search: ",
        },
        searching: false,
        paging: false,
        info: false,
        emptyTable: false
    });


    const add_router_modal = new bootstrap.Modal(document.getElementById('add_router'),unclose)
    const edit_router_modal = new bootstrap.Modal(document.getElementById('edit_router'),unclose)
    const delete_router_modal = new bootstrap.Modal(document.getElementById('delete_router'),unclose)

    var add_router_btn = document.getElementById("add_router_btn")
    var router_name = document.getElementById("router_name")
    var router_ip = document.getElementById("router_ip")
    var router_subnet = document.getElementById("router_subnet")
    var router_wan1 = document.getElementById("router_wan1")
    var router_wan2 = document.getElementById("router_wan2")
    var router_wan1_icon = document.getElementById("router_wan1_icon")
    var router_wan2_icon = document.getElementById("router_wan2_icon")
    var wan1_info = document.getElementById("wan1_info")
    var wan2_info = document.getElementById("wan2_info")
    var add_router = document.getElementById("add_router")

    var save_router_btn = document.getElementById("save_router_btn")

    var edit_router_name = document.getElementById("edit_router_name")
    var edit_router_ip = document.getElementById("edit_router_ip")
    var edit_router_subnet = document.getElementById("edit_router_subnet")
    var edit_router_wan1 = document.getElementById("edit_router_wan1")
    var edit_router_wan2 = document.getElementById("edit_router_wan2")
    var edit_router_wan1_icon = document.getElementById("edit_router_wan1_icon")
    var edit_router_wan2_icon = document.getElementById("edit_router_wan2_icon")
    var edit_wan1_info = document.getElementById("edit_wan1_info")
    var edit_wan2_info = document.getElementById("edit_wan2_info")

    var edit_router_title = document.getElementById("edit_router_title")
    var edit_router_btn = document.getElementById("edit_router_btn")

    var delete_router_btn = document.getElementById("delete_router_btn")
    var delete_router_name = document.getElementById("delete_router_name")
    var delete_router_table_container = document.getElementById("delete_router_table_container")
    
    var update_router_btn = document.getElementById("update_router_btn")

    var active_wan = document.getElementById("active_wan")
    var save_active_wan = document.getElementById("save_active_wan")
    var active_wan_id = null
    var bol_unset = false
    var bol_count = 0

    var temp_tr = null;
    var temp_tr_id = null;
    var temp_btn_edit = null;

    var temp_wan1 = ""
    var temp_wan2 = ""

    loadPage();
    // LOAD PAGE DATA
    function loadPage(){
        sole.get("../../controllers/routers/get_router.php").then(res => loadRouter(res))
    }

    function loadRouter(res){
        routerTable.clear().draw();
        res.router.forEach(e => {
            routerTable.row.add([
                e["id"],
                e["name"],
                e["ip"],
                e["subnet"],
                "<button id=\"edit_router_"+ e["id"] +"\" r-id=\""+ e["id"] +"\" class=\"edit_router_row btn btn-sm btn-secondary\"><i r-id=\""+ e["id"] +"\" class=\"edit_router_row fa fa-edit\"></i></button>" +
                "<button id=\"delete_router_"+ e["id"] +"\" r-id=\""+ e["id"] +"\" class=\"delete_router_row btn btn-sm btn-danger ms-1\"><i r-id=\""+ e["id"] +"\" class=\"delete_router_row fa fa-trash\"></i></button>" 
            ]).draw(false)   
        });
        document.querySelector('#router_table').addEventListener("click", e=>{
            let tr = "";
            if(e.target.tagName == "I"){
                tr = e.target.parentNode.parentNode.parentNode.children
            }
            if(e.target.tagName == "BUTTON"){
                tr = e.target.parentNode.parentNode.children    
            }
            if(e.target.parentNode.tagName == "TR" && !e.target.parentNode.classList.contains("tr_exclude")){
                if(temp_tr_id){
                    if(temp_tr_id != e.target.parentNode.children[3].children[0].getAttribute("r-id")){
                        temp_btn_edit.classList.remove("bg-light")
                        temp_btn_edit.classList.remove("text-dark")
                        temp_btn_edit.classList.add("bg-secondary")
                        temp_btn_edit = e.target.parentNode.children[3].children[0]
                        temp_btn_edit.classList.add("bg-light")
                        temp_btn_edit.classList.add("text-dark")
                        temp_btn_edit.classList.remove("bg-secondary")
                        temp_tr.removeAttribute("class")
                        temp_tr = e.target.parentNode
                        temp_tr_id = e.target.parentNode.children[3].children[0].getAttribute("r-id")
                        temp_tr.setAttribute("class","bg-secondary text-light")
                    }
                }else{
                    temp_btn_edit = e.target.parentNode.children[3].children[0]
                    temp_btn_edit.classList.add("bg-light")
                    temp_btn_edit.classList.add("text-dark")
                    temp_btn_edit.classList.remove("bg-secondary")
                    temp_tr = e.target.parentNode
                    temp_tr_id = e.target.parentNode.children[3].children[0].getAttribute("r-id")
                    temp_tr.setAttribute("class","bg-secondary text-light")
                }
                sole.post("../../controllers/routers/get_router_wan.php",{
                    id: temp_tr_id
                }).then(res => validateResponseWANSettings(res))
            }
            if(e.target.classList.contains('edit_router_row')) {
                edit_router_title.innerText = "Edit Router: " + tr[0].innerText
                update_router_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
                sole.post("../../controllers/routers/find_router.php",{
                    id: e.target.getAttribute("r-id")
                }).then(res => editForm(res))
            }
            if(e.target.classList.contains('delete_router_row')) {
                delete_router_btn.setAttribute("r-id",e.target.getAttribute("r-id"))
                sole.post("../../controllers/routers/find_router_delete.php",{
                    id: e.target.getAttribute("r-id")
                }).then(res => deleteForm(res))
            }
            // if(e.target.classList.contains('delete_isp_row')) {
            //     console.log(e.target.getAttribute("i-id"))
            // }
        })
    }

    // ADD ROUTER FOCUS
    add_router.addEventListener('shown.bs.modal', function () {
        router_name.focus()
    })

    add_router_btn.addEventListener("click",function(){
        wan1_info.innerText = "NOT SET"
        wan2_info.innerText = "NOT SET"
        router_wan1_icon.setAttribute("hidden","")   
        router_wan2_icon.setAttribute("hidden","")   
        sole.get("../../controllers/routers/get_available_isp.php").then(res => selectDrop(res,"add_router"))
    })

    update_router_btn.addEventListener("click",function(){
        var message = ""
        !edit_router_subnet.value ? message = "Please provide router subnet." : null
        !edit_router_ip.value ? message = "Please provide router ip." : null
        !edit_router_name.value ? message = "Please provide router name." : null

        if(!message){
            if(edit_router_wan1.value && edit_router_wan2.value && edit_router_wan1.value != "-" && edit_router_wan2.value != "-"){
                if(edit_router_wan1.value != edit_router_wan2.value){
                    postRouterUpdate(this.getAttribute("r-id"))
                }else{
                    if(edit_router_wan1.value == "-" && edit_router_wan2.value == "-"){
                        postRouterUpdate(this.getAttribute("r-id"))
                    }else{
                        bs5.toast("warning","WAN 1 (Primary) should not be the same as WAN 2 (Secondary).")
                    }
                }
            }else{
                postRouterUpdate(this.getAttribute("r-id"))
            }
        }else{
            bs5.toast("warning",message)
        }
    })

    router_wan1.addEventListener("change",function(){
        sole.post("../../controllers/routers/find_isp.php",{
            id: this.value
        }).then(res => setWanAdd(res,"wan1"))
    })

    router_wan2.addEventListener("change",function(){
        sole.post("../../controllers/routers/find_isp.php",{
            id: this.value
        }).then(res => setWanAdd(res,"wan2"))
    })

    edit_router_wan1.addEventListener("change",function(){
        sole.post("../../controllers/routers/find_isp.php",{
            id: this.value
        }).then(res => setWanEdit(res,"wan1"))
    })

    edit_router_wan2.addEventListener("change",function(){
        sole.post("../../controllers/routers/find_isp.php",{
            id: this.value
        }).then(res => setWanEdit(res,"wan2"))
    })

    save_router_btn.addEventListener("click",function(){
        var message = ""
        !router_subnet.value ? message = "Please provide router subnet." : null
        !router_ip.value ? message = "Please provide router ip." : null
        !router_name.value ? message = "Please provide router name." : null

        if(!message){
            if(router_wan1.value && router_wan2.value && router_wan1.value != "-" && router_wan2.value != "-"){
                if(router_wan1.value != router_wan2.value){
                    postRouter()
                }else{
                    if(router_wan1.value == "-" && router_wan2.value == "-"){
                        postRouter()
                    }else{
                        bs5.toast("warning","WAN 1 (Primary) should not be the same as WAN 2 (Secondary).")
                    }
                }
            }else{
                postRouter()
            }
        }else{
            bs5.toast("warning",message)
        }
    })

    function postRouter(){
        sole.post("../../controllers/routers/add_router.php",{
            uid: localStorage.getItem("userid"),
            router_name: router_name.value,
            router_ip: router_ip.value,
            router_subnet: router_subnet.value,
            router_wan1: router_wan1.value,
            router_wan2: router_wan2.value
        }).then(res => validateResponse(res,"add_router"))
    }

    function postRouterUpdate(id){
        sole.post("../../controllers/routers/update_router.php",{
            id: id,
            router_name: edit_router_name.value,
            router_ip: edit_router_ip.value,
            router_subnet: edit_router_subnet.value,
            router_wan1: edit_router_wan1.value,
            router_wan2: edit_router_wan2.value
        }).then(res => validateResponse(res,"edit_router"))
    }

    function editForm(res){
        edit_router_name.value = res.router[0]["name"]
        edit_router_ip.value = res.router[0]["ip"]
        edit_router_subnet.value = res.router[0]["subnet"]

        edit_wan1_info.innerText = "NOT SET"
        edit_wan2_info.innerText = "NOT SET"
        edit_router_wan1_icon.setAttribute("hidden","")   
        edit_router_wan2_icon.setAttribute("hidden","")

        temp_wan1 = res.router[0]["wan1"]
        temp_wan2 = res.router[0]["wan2"]

        sole.post("../../controllers/routers/get_current_isp.php",{
            wan1: res.router[0]["wan1"],
            wan2: res.router[0]["wan2"]
        }).then(res => setWanCurrent(res))
        sole.get("../../controllers/routers/get_available_isp.php").then(res => selectDrop(res,"edit_router"))

        edit_router_modal.show()
    }








    delete_router_btn.addEventListener("click",function(){
        sole.post("../../controllers/routers/delete_router.php",{
            id: this.getAttribute("r-id")
        }).then(res => validateResponse(res,"delete_router"))
        console.log(this.getAttribute("r-id"))
    })

    function deleteForm(res){
        delete_router_modal.show()
        delete_router_name.innerText = res.router[0]["name"]
        if(res.network.length){
            routerDeleteTable.clear().draw();
            res.network.forEach(e => {
                routerDeleteTable.row.add([
                    e["name"],
                    e["from"],
                    e["to"],
                ]).draw(false)   
            });
            delete_router_table_container.removeAttribute("hidden")
        }else{
            delete_router_table_container.setAttribute("hidden","true")
        }
    }

    function setWanCurrent(res){
        edit_router_wan1.innerHTML = ""
        edit_router_wan2.innerHTML = ""
        if(res.wan1.length){
            if(res.wan1[0]["isp_name"] == "PLDT Inc."){
                edit_router_wan1_icon.setAttribute("src","../../assets/img/pldt.png")
                edit_router_wan1_icon.setAttribute("class","ht-20")
                edit_router_wan1_icon.removeAttribute("hidden")
            }
            if(res.wan1[0]["isp_name"] == "Globe Telecom, Inc."){
                edit_router_wan1_icon.setAttribute("src","../../assets/img/globe.png")
                edit_router_wan1_icon.setAttribute("class","ht-30")
                edit_router_wan1_icon.removeAttribute("hidden")
            }
            if(res.wan1[0]["isp_name"] == "Converge ICT Solutions Inc."){
                edit_router_wan1_icon.setAttribute("src","../../assets/img/converge.png")
                edit_router_wan1_icon.setAttribute("class","ht-30")
                edit_router_wan1_icon.removeAttribute("hidden")
            }

            edit_wan1_info.innerHTML = "ISP: " + res.wan1[0]["isp_name"] + "<br>" +
            "Name: " + res.wan1[0]["name"] + "<br>" +
            "WAN IP: " + res.wan1[0]["wan_ip"] + "<br>" +
            "<div class=\"row mt-2\">" + 
                "<div class=\"col-md-6\">" +
                    "Subnet: " + (res.wan1[0]["subnet"] == "-" ? "" : res.wan1[0]["subnet"]) + "<br>" +
                "</div>" +
                "<div class=\"col-md-6\">" +
                    "Gateway: " + (res.wan1[0]["gateway"] == "-" ? "" : res.wan1[0]["gateway"]) + "<br>" +
                "</div>" +
            "</div>" +
            "<div class=\"row\">" + 
                "<div class=\"col-md-6\">" +
                    "DNS 1: " + (res.wan1[0]["dns1"] == "-" ? "" : res.wan1[0]["dns1"]) + "<br>" +
                "</div>" +
                "<div class=\"col-md-6\">" +
                    "DNS 2: " + (res.wan1[0]["dns2"] == "-" ? "" : res.wan1[0]["dns2"]) + "<br>"
                "</div>" +
            "</div>"

            var edit_op1 = document.createElement("option")
            edit_op1.setAttribute("value",res.wan1[0]["id"])
            edit_op1.setAttribute("selected","")
            edit_op1.innerText = res.wan1[0]["name"]
            edit_router_wan1.appendChild(edit_op1)

            var edit_op1_2 = document.createElement("option")
            edit_op1_2.setAttribute("value",res.wan1[0]["id"])
            edit_op1_2.innerText = res.wan1[0]["name"]
            edit_router_wan2.appendChild(edit_op1_2)
        }
        if(res.wan2.length){
            if(res.wan2[0]["isp_name"] == "PLDT Inc."){
                edit_router_wan2_icon.setAttribute("src","../../assets/img/pldt.png")
                edit_router_wan2_icon.setAttribute("class","ht-20")
                edit_router_wan2_icon.removeAttribute("hidden")
            }
            if(res.wan2[0]["isp_name"] == "Globe Telecom, Inc."){
                edit_router_wan2_icon.setAttribute("src","../../assets/img/globe.png")
                edit_router_wan2_icon.setAttribute("class","ht-30")
                edit_router_wan2_icon.removeAttribute("hidden")
            }
            if(res.wan2[0]["isp_name"] == "Converge ICT Solutions Inc."){
                edit_router_wan2_icon.setAttribute("src","../../assets/img/converge.png")
                edit_router_wan2_icon.setAttribute("class","ht-30")
                edit_router_wan2_icon.removeAttribute("hidden")
            }

            edit_wan2_info.innerHTML = "ISP: " + res.wan2[0]["isp_name"] + "<br>" +
            "Name: " + res.wan2[0]["name"] + "<br>" +
            "WAN IP: " + res.wan2[0]["wan_ip"] + "<br>" +
            "<div class=\"row mt-2\">" + 
                "<div class=\"col-md-6\">" +
                    "Subnet: " + (res.wan2[0]["subnet"] == "-" ? "" : res.wan2[0]["subnet"]) + "<br>" +
                "</div>" +
                "<div class=\"col-md-6\">" +
                    "Gateway: " + (res.wan2[0]["gateway"] == "-" ? "" : res.wan2[0]["gateway"]) + "<br>" +
                "</div>" +
            "</div>" +
            "<div class=\"row\">" + 
                "<div class=\"col-md-6\">" +
                    "DNS 1: " + (res.wan2[0]["dns1"] == "-" ? "" : res.wan2[0]["dns1"]) + "<br>" +
                "</div>" +
                "<div class=\"col-md-6\">" +
                    "DNS 2: " + (res.wan2[0]["dns2"] == "-" ? "" : res.wan2[0]["dns2"]) + "<br>"
                "</div>" +
            "</div>"
            var edit_op2 = document.createElement("option")
            edit_op2.setAttribute("value",res.wan2[0]["id"])
            edit_op2.setAttribute("selected","")
            edit_op2.innerText = res.wan2[0]["name"]
            edit_router_wan2.appendChild(edit_op2)

            var edit_op2_1 = document.createElement("option")
            edit_op2_1.setAttribute("value",res.wan2[0]["id"])
            edit_op2_1.innerText = res.wan2[0]["name"]
            edit_router_wan1.appendChild(edit_op2_1)
        }
    }

    function setWanAdd(res,func){
        if(res.length){
            if(func == "wan1"){
                if(res[0]["isp_name"] == "PLDT Inc."){
                    router_wan1_icon.setAttribute("src","../../assets/img/pldt.png")
                    router_wan1_icon.setAttribute("class","ht-20")
                    router_wan1_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Globe Telecom, Inc."){
                    router_wan1_icon.setAttribute("src","../../assets/img/globe.png")
                    router_wan1_icon.setAttribute("class","ht-30")
                    router_wan1_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Converge ICT Solutions Inc."){
                    router_wan1_icon.setAttribute("src","../../assets/img/converge.png")
                    router_wan1_icon.setAttribute("class","ht-30")
                    router_wan1_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Others"){
                    router_wan1_icon.setAttribute("src","../../assets/img/hero.png")
                    router_wan1_icon.setAttribute("class","ht-30")
                    router_wan1_icon.removeAttribute("hidden")
                }

                wan1_info.innerHTML = "ISP: " + res[0]["isp_name"] + "<br>" +
                "Name: " + res[0]["name"] + "<br>" +
                "WAN IP: " + res[0]["wan_ip"] + "<br>" +
                "<div class=\"row mt-2\">" + 
                    "<div class=\"col-md-6\">" +
                        "Subnet: " + (res[0]["subnet"] == "-" ? "" : res[0]["subnet"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "Gateway: " + (res[0]["gateway"] == "-" ? "" : res[0]["gateway"]) + "<br>" +
                    "</div>" +
                "</div>" +
                "<div class=\"row\">" + 
                    "<div class=\"col-md-6\">" +
                        "DNS 1: " + (res[0]["dns1"] == "-" ? "" : res[0]["dns1"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "DNS 2: " + (res[0]["dns2"] == "-" ? "" : res[0]["dns2"]) + "<br>"
                    "</div>" +
                "</div>"      
            }
            if(func == "wan2"){
                if(res[0]["isp_name"] == "PLDT Inc."){
                    router_wan2_icon.setAttribute("src","../../assets/img/pldt.png")
                    router_wan2_icon.setAttribute("class","ht-20")
                    router_wan2_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Globe Telecom, Inc."){
                    router_wan2_icon.setAttribute("src","../../assets/img/globe.png")
                    router_wan2_icon.setAttribute("class","ht-30")
                    router_wan2_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Converge ICT Solutions Inc."){
                    router_wan2_icon.setAttribute("src","../../assets/img/converge.png")
                    router_wan2_icon.setAttribute("class","ht-30")
                    router_wan2_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Others"){
                    router_wan2_icon.setAttribute("src","../../assets/img/hero.png")
                    router_wan2_icon.setAttribute("class","ht-30")
                    router_wan2_icon.removeAttribute("hidden")
                }


                wan2_info.innerHTML = "ISP: " + res[0]["isp_name"] + "<br>" +
                "Name: " + res[0]["name"] + "<br>" +
                "WAN IP: " + res[0]["wan_ip"] + "<br>" +
                "<div class=\"row mt-2\">" + 
                    "<div class=\"col-md-6\">" +
                        "Subnet: " + (res[0]["subnet"] == "-" ? "" : res[0]["subnet"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "Gateway: " + (res[0]["gateway"] == "-" ? "" : res[0]["gateway"]) + "<br>" +
                    "</div>" +
                "</div>" +
                "<div class=\"row\">" + 
                    "<div class=\"col-md-6\">" +
                        "DNS 1: " + (res[0]["dns1"] == "-" ? "" : res[0]["dns1"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "DNS 2: " + (res[0]["dns2"] == "-" ? "" : res[0]["dns2"]) + "<br>"
                    "</div>" +
                "</div>"  
            }    
        }else{
            if(func == "wan1"){
                router_wan1_icon.setAttribute("hidden","")    
                wan1_info.innerText = "NOT SET"
            }
            if(func == "wan2"){
                router_wan2_icon.setAttribute("hidden","")  
                wan2_info.innerText = "NOT SET"
            }
        }
    }

    function setWanEdit(res,func){
        if(res.length){
            if(func == "wan1"){
                if(res[0]["isp_name"] == "PLDT Inc."){
                    edit_router_wan1_icon.setAttribute("src","../../assets/img/pldt.png")
                    edit_router_wan1_icon.setAttribute("class","ht-20")
                    edit_router_wan1_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Globe Telecom, Inc."){
                    edit_router_wan1_icon.setAttribute("src","../../assets/img/globe.png")
                    edit_router_wan1_icon.setAttribute("class","ht-30")
                    edit_router_wan1_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Converge ICT Solutions Inc."){
                    edit_router_wan1_icon.setAttribute("src","../../assets/img/converge.png")
                    edit_router_wan1_icon.setAttribute("class","ht-30")
                    edit_router_wan1_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Others"){
                    edit_router_wan1_icon.setAttribute("src","../../assets/img/hero.png")
                    edit_router_wan1_icon.setAttribute("class","ht-30")
                    edit_router_wan1_icon.removeAttribute("hidden")
                }

                edit_wan1_info.innerHTML = "ISP: " + res[0]["isp_name"] + "<br>" +
                "Name: " + res[0]["name"] + "<br>" +
                "WAN IP: " + res[0]["wan_ip"] + "<br>" +
                "<div class=\"row mt-2\">" + 
                    "<div class=\"col-md-6\">" +
                        "Subnet: " + (res[0]["subnet"] == "-" ? "" : res[0]["subnet"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "Gateway: " + (res[0]["gateway"] == "-" ? "" : res[0]["gateway"]) + "<br>" +
                    "</div>" +
                "</div>" +
                "<div class=\"row\">" + 
                    "<div class=\"col-md-6\">" +
                        "DNS 1: " + (res[0]["dns1"] == "-" ? "" : res[0]["dns1"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "DNS 2: " + (res[0]["dns2"] == "-" ? "" : res[0]["dns2"]) + "<br>"
                    "</div>" +
                "</div>"      
            }
            if(func == "wan2"){
                if(res[0]["isp_name"] == "PLDT Inc."){
                    edit_router_wan2_icon.setAttribute("src","../../assets/img/pldt.png")
                    edit_router_wan2_icon.setAttribute("class","ht-20")
                    edit_router_wan2_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Globe Telecom, Inc."){
                    edit_router_wan2_icon.setAttribute("src","../../assets/img/globe.png")
                    edit_router_wan2_icon.setAttribute("class","ht-30")
                    edit_router_wan2_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Converge ICT Solutions Inc."){
                    edit_router_wan2_icon.setAttribute("src","../../assets/img/converge.png")
                    edit_router_wan2_icon.setAttribute("class","ht-30")
                    edit_router_wan2_icon.removeAttribute("hidden")
                }else if(res[0]["isp_name"] == "Others"){
                    edit_router_wan2_icon.setAttribute("src","../../assets/img/hero.png")
                    edit_router_wan2_icon.setAttribute("class","ht-30")
                    edit_router_wan2_icon.removeAttribute("hidden")
                }

                edit_wan2_info.innerHTML = "ISP: " + res[0]["isp_name"] + "<br>" +
                "Name: " + res[0]["name"] + "<br>" +
                "WAN IP: " + res[0]["wan_ip"] + "<br>" +
                "<div class=\"row mt-2\">" + 
                    "<div class=\"col-md-6\">" +
                        "Subnet: " + (res[0]["subnet"] == "-" ? "" : res[0]["subnet"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "Gateway: " + (res[0]["gateway"] == "-" ? "" : res[0]["gateway"]) + "<br>" +
                    "</div>" +
                "</div>" +
                "<div class=\"row\">" + 
                    "<div class=\"col-md-6\">" +
                        "DNS 1: " + (res[0]["dns1"] == "-" ? "" : res[0]["dns1"]) + "<br>" +
                    "</div>" +
                    "<div class=\"col-md-6\">" +
                        "DNS 2: " + (res[0]["dns2"] == "-" ? "" : res[0]["dns2"]) + "<br>"
                    "</div>" +
                "</div>"  
            }    
        }else{
            if(func == "wan1"){
                edit_router_wan1_icon.setAttribute("hidden","")    
                edit_wan1_info.innerText = "NOT SET"
            }
            if(func == "wan2"){
                edit_router_wan2_icon.setAttribute("hidden","")  
                edit_wan2_info.innerText = "NOT SET"
            }
        }
    }

    function selectDrop(res,func){
        if(func == "add_router"){
            router_wan1.innerHTML = ""
            router_wan2.innerHTML = ""

            var op1_sel = document.createElement("option")
            op1_sel.setAttribute("disabled","")
            op1_sel.setAttribute("selected","")
            op1_sel.setAttribute("value","-")
            op1_sel.innerText = "-- Select WAN 1 --"
            router_wan1.appendChild(op1_sel)

            var op2_sel = document.createElement("option")
            op2_sel.setAttribute("disabled","")
            op2_sel.setAttribute("selected","")
            op2_sel.setAttribute("value","-")
            op2_sel.innerText = "-- Select WAN 2 --"
            router_wan2.appendChild(op2_sel)


            res.isp.forEach(e => {
                var op1 = document.createElement("option")
                op1.setAttribute("value",e["id"])
                op1.innerText = e["name"]
                router_wan1.appendChild(op1)
            });

            var wan1op = document.createElement("option")
            wan1op.setAttribute("value","-")
            wan1op.innerText = "N/A"

            router_wan1.appendChild(wan1op)
            
            res.isp.forEach(e => {
                var op2 = document.createElement("option")
                op2.setAttribute("value",e["id"])
                op2.innerText = e["name"]
                router_wan2.appendChild(op2)
            });

            var wan2op = document.createElement("option")
            wan2op.setAttribute("value","-")
            wan2op.innerText = "N/A"
            router_wan2.appendChild(wan2op)    
        }
        if(func == "edit_router"){
            if(temp_wan1 == "-"){
                var op1_sel = document.createElement("option")
                op1_sel.setAttribute("disabled","")
                op1_sel.setAttribute("selected","")
                op1_sel.setAttribute("value","-")
                op1_sel.innerText = "-- Select WAN 1 --"
                checkOptionIfExist(edit_router_wan1,"-- Select WAN 1 --") ? edit_router_wan1.insertAdjacentElement("afterbegin",op1_sel) : null
            }
            if(temp_wan2 == "-"){
                var op2_sel = document.createElement("option")
                op2_sel.setAttribute("disabled","")
                op2_sel.setAttribute("selected","")
                op2_sel.setAttribute("value","-")
                op2_sel.innerText = "-- Select WAN 2 --"
                checkOptionIfExist(edit_router_wan2,"-- Select WAN 2 --") ? edit_router_wan2.insertAdjacentElement("afterbegin",op2_sel) : null
            }
 
            res.isp.forEach(e => {
                var op1 = document.createElement("option")
                op1.setAttribute("value",e["id"])
                op1.innerText = e["name"]
                checkOptionIfExist(edit_router_wan1,e["name"]) ? edit_router_wan1.appendChild(op1) : null
            });

            var wan1op = document.createElement("option")
            wan1op.setAttribute("value","-")
            wan1op.innerText = "N/A"
            checkOptionIfExist(edit_router_wan1,"N/A") ? edit_router_wan1.appendChild(wan1op) : null
            
            res.isp.forEach(e => {
                var op2 = document.createElement("option")
                op2.setAttribute("value",e["id"])
                op2.innerText = e["name"]
                checkOptionIfExist(edit_router_wan2,e["name"]) ? edit_router_wan2.appendChild(op2) : null
            });

            var wan2op = document.createElement("option")
            wan2op.setAttribute("value","-")
            wan2op.innerText = "N/A"
            checkOptionIfExist(edit_router_wan2,"N/A") ? edit_router_wan2.appendChild(wan2op) : null
        }  
    }

    function checkOptionIfExist(select,option){
        var ops = [];
        for (let i = 0; i < select.children.length; i++) {
            ops.push(select.children[i].innerText)
        }
        if(ops.indexOf(option)  !== -1){
            return false
        }else{
            return true
        }
        
    }

    function checkActive(active,wan,name,res){
        let head = name + ": <b class=\"text-primary\">Standby</b>"
        let data = ""
        let icon = ""

        var op = document.createElement("option")
        op.value = wan[0]["id"]
        op.innerText = name
        if(active != "-"){
            if(active == wan[0]["id"]){
                head = name + ": <b class=\"text-success\">In Use</b>"
                op.innerText = name + " (Active)"
                op.setAttribute("selected","")
                active_wan_id = wan[0]["id"]
                bol_unset = true
            }
        }
        active_wan.appendChild(op)
        
        if(bol_unset && name == "WAN 1"){
            if(!res.wan2.length){
                bol_unset ? active_wan.insertAdjacentHTML("beforeend","<option value=\"-\">Unset WAN</option>") : null
            }
        }else if(bol_unset && name == "WAN 2"){
            bol_unset ? active_wan.insertAdjacentHTML("beforeend","<option value=\"-\">Unset WAN</option>") : null
            bol_count = 0
            bol_unset = false
        }else{
            bol_count++
        }


        if(wan[0]["isp_name"] == "PLDT Inc."){
            icon = "<img class=\"ht-15 mb-2\" src=\"../../assets/img/pldt.png\"><br>"
        }else if(wan[0]["isp_name"] == "Globe Telecom, Inc."){
            icon = "<img class=\"ht-25 mb-2\" src=\"../../assets/img/globe.png\"><br>"
        }else if(wan[0]["isp_name"] == "Converge ICT Solutions Inc."){
            icon = "<img class=\"ht-25 mb-2\" src=\"../../assets/img/converge.png\"><br>"
        }else if(wan[0]["isp_name"] == "Others"){
            icon = "<img class=\"ht-25 mb-2\" src=\"../../assets/img/hero.png\"><br>"
        }


        data = icon + head + "<br>" +
        "Name: " + wan[0]["name"] + "<br>" +
        "WAN IP: " + wan[0]["wan_ip"] + "<br>" +
        "<div class=\"row mt-2\">" + 
            "<div class=\"col-md-6\">" +
                "Subnet: " + (wan[0]["subnet"] == "-" ? "" : wan[0]["subnet"]) + "<br>" +
            "</div>" +
            "<div class=\"col-md-6\">" +
                "Gateway: " + (wan[0]["gateway"] == "-" ? "" : wan[0]["gateway"]) + "<br>" +
            "</div>" +
        "</div>" +
        "<div class=\"row\">" + 
            "<div class=\"col-md-6\">" +
                "DNS 1: " + (wan[0]["dns1"] == "-" ? "" : wan[0]["dns1"]) + "<br>" +
            "</div>" +
            "<div class=\"col-md-6\">" +
                "DNS 2: " + (wan[0]["dns2"] == "-" ? "" : wan[0]["dns2"]) + "<br>"
            "</div>" +
        "</div>"  
        return data
    }

    active_wan.addEventListener("change",function(){
        if(this.value == active_wan_id){
            save_active_wan.setAttribute("hidden","")
        }else{
            save_active_wan.removeAttribute("hidden")
        }
    })

    save_active_wan.addEventListener("click",function(){
        sole.post("../../controllers/routers/set_active_wan.php",{
            id: temp_tr_id,
            active_wan: active_wan.value
        }).then(res => validateResponseWANSettings(res))
        active_wan_id = null
    })

    function validateResponseWANSettings(res){
        var bol = false;
        bol_unset = false
        if(res.status){
            save_active_wan.setAttribute("hidden","")
            active_wan.innerHTML = "<option disabled selected value=\"-\">-- Select Active WAN --</option>"
            routerISPTable.clear().draw();
            if(res.wan1.length){
                routerISPTable.row.add([
                    checkActive(res.router["active"],res.wan1,"WAN 1",res)
                ]).draw(false)
                bol = true
            }
            if(res.wan2.length){
                routerISPTable.row.add([
                    checkActive(res.router["active"],res.wan2,"WAN 2",res)
                ]).draw(false)
                bol = true
            }
            if(!bol){
                routerISPTable.row.add([
                    "<div class=\"w-100 text-center text-danger fw-bold\">WAN is not set</div>"
                ]).draw(false)
            }
        }else{
            bs5.toast(res.type,res.message,res.size)
        }    
    }
    function validateResponse(res, func){
        if(res.status){
            if(func == "add_router"){
                router_name.value = ""
                router_ip.value = ""
                router_subnet.value = ""
                add_router_modal.hide()
                loadPage()
            }
            if(func == "edit_router"){
                edit_router_name.value = ""
                edit_router_ip.value = ""
                edit_router_subnet.value = ""
                edit_router_modal.hide()
                edit_router_wan1.innerHTML = ""
                edit_router_wan2.innerHTML = ""
                loadPage()
            }
            if(func == "delete_router"){
                delete_router_modal.hide()
                loadPage()
            }
            bs5.toast(res.type,res.message,res.size)
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }

    document.body.addEventListener("click",e => {
        let table = document.querySelector("#router_isp_table");
        if(!table.contains(e.target) && e.target.parentNode.tagName != "TR" && temp_tr_id && !e.target.classList.contains("dt-column-order") && !e.target.classList.contains("dt-column-title") && !e.target.classList.contains("edit_router_row") && !e.target.classList.contains("delete_router_row") && !e.target.classList.contains("save_active_wan")){
            temp_tr.removeAttribute("class")
            temp_tr = null
            temp_btn_edit.classList.remove("bg-light")
            temp_btn_edit.classList.remove("text-dark")
            temp_btn_edit.classList.add("bg-secondary")
            temp_btn_edit = null
            temp_tr_id = null
            active_wan_id = null
            routerISPTable.clear().draw();
            save_active_wan.setAttribute("hidden","")
            active_wan.innerHTML = "<option disabled selected value=\"-\">-- Select Active WAN --</option>"
        }
    })
}