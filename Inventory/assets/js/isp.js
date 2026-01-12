if(document.getElementById("isp")){
    let ispTable = new DataTable('#isp_table',{
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
           search: "<button data-bs-toggle=\"modal\" data-bs-target=\"#add_isp\" class=\"btn btn-sm btn-dark me-3\"><span class=\"fa fa-plus\"></span> Add ISP</button> Search: "
        }
    });

    const add_isp_modal = new bootstrap.Modal(document.getElementById('add_isp'),unclose)
    const edit_isp_modal = new bootstrap.Modal(document.getElementById('edit_isp'),unclose)
    const delete_isp_modal = new bootstrap.Modal(document.getElementById('delete_isp'),unclose)

    var add_isp = document.getElementById("add_isp")
    var isp_icon = document.getElementById("isp_icon")
    var add_isp_btn = document.getElementById("add_isp_btn")
    
    var label_name = document.getElementById("name")
    var isp_name = document.getElementById("isp_name")
    var wan_ip = document.getElementById("wan_ip")
    var subnet = document.getElementById("subnet")
    var gateway = document.getElementById("gateway")
    var dns1 = document.getElementById("dns1")
    var dns2 = document.getElementById("dns2")
    var isp_webmgmtpt = document.getElementById("isp_webmgmtpt")

    var edit_isp_icon = document.getElementById("edit_isp_icon")
    var edit_label_name = document.getElementById("edit_name")
    var edit_isp_name = document.getElementById("edit_isp_name")
    var edit_wan_ip = document.getElementById("edit_wan_ip")
    var edit_subnet = document.getElementById("edit_subnet")
    var edit_gateway = document.getElementById("edit_gateway")
    var edit_dns1 = document.getElementById("edit_dns1")
    var edit_dns2 = document.getElementById("edit_dns2")
    var edit_isp_webmgmtpt = document.getElementById("edit_isp_webmgmtpt")

    var edit_isp_title = document.getElementById("edit_isp_title")
    var edit_isp_btn = document.getElementById("edit_isp_btn")

    var delete_isp_name = document.getElementById("delete_isp_name")
    var delete_isp_btn = document.getElementById("delete_isp_btn")
    var delete_isp_message = document.getElementById("delete_isp_message")

    loadPage();
    // LOAD PAGE DATA
    function loadPage(){
        sole.get("../../controllers/isp/get_isp.php").then(res => loadISP(res))
    }

    function getIcon(isp_name){
        if(isp_name == "PLDT Inc."){
            return "<img id=\"isp_icon\" src=\"../../assets/img/pldt.png\" class=\"ht-10\"  style=\"margin-top: -5px;\" alt=\"\" srcset=\"\">"
        }else if(isp_name == "Globe Telecom, Inc."){
            return "<img id=\"isp_icon\" src=\"../../assets/img/globe.png\" class=\"ht-15\"  style=\"margin-top: -5px;\" alt=\"\" srcset=\"\">"
        }else if(isp_name == "Converge ICT Solutions Inc."){
            return "<img id=\"isp_icon\" src=\"../../assets/img/converge.png\" class=\"ht-15\"  style=\"margin-top: -5px;\" alt=\"\" srcset=\"\">"
        }else{
            return "<img id=\"isp_icon\" src=\"../../assets/img/hero.png\" class=\"ht-15\"  style=\"margin-top: -5px;\" alt=\"\" srcset=\"\">"
        }
    }

    function loadISP(res){
        ispTable.clear().draw();
        res.isp.forEach(e => {
            ispTable.row.add([
                e["id"],
                getIcon(e["isp_name"]),
                e["name"] != "-" ? e["name"] : "",
                e["wan_ip"] != "-" ? e["wan_ip"] : "",
                e["subnet"] != "-" ? e["subnet"] : "",
                e["gateway"] != "-" ? e["gateway"] : "",
                e["dns1"] != "-" ? e["dns1"] : "",
                e["dns2"] != "-" ? e["dns2"] : "",
                "<button id=\"edit_isp_"+ e["id"] +"\" i-id=\""+ e["id"] +"\" class=\"edit_isp_row btn btn-sm btn-secondary\"><i i-id=\""+ e["id"] +"\" class=\"edit_isp_row fa fa-edit\"></i></button>" +
                "<button id=\"delete_isp_"+ e["id"] +"\" i-id=\""+ e["id"] +"\" class=\"delete_isp_row btn btn-sm btn-danger ms-1\"><i i-id=\""+ e["id"] +"\" class=\"delete_isp_row fa fa-trash\"></i></button>" 
            ]).draw(false)   
        });
        document.querySelector('#isp_table').addEventListener("click", e=>{
            let tr = "";
            if(e.target.tagName == "I"){
                tr = e.target.parentNode.parentNode.parentNode.children
            }
            if(e.target.tagName == "BUTTON"){
                tr = e.target.parentNode.parentNode.children    
            }
            if(e.target.classList.contains('edit_isp_row')) {
                edit_isp_title.innerText = "Edit ISP: " + tr[1].innerText
                edit_isp_btn.setAttribute("i-id",e.target.getAttribute("i-id"))
                sole.post("../../controllers/isp/find_isp.php",{
                    id: e.target.getAttribute("i-id")
                }).then(res => editForm(res))
            }
            if(e.target.classList.contains('delete_isp_row')) {
                delete_isp_name.innerText = tr[1].innerText
                delete_isp_btn.setAttribute("i-id",e.target.getAttribute("i-id"))
                sole.post("../../controllers/isp/get_router_assigned.php",{
                    id: e.target.getAttribute("i-id")
                }).then(res => deleteMessage(res))
                delete_isp_modal.show()
            }
        })
    }

    // ADD ISP FOCUS
    add_isp.addEventListener('shown.bs.modal', function () {
        isp_name.value = ""
        label_name.focus()
    })

    // EDIT ISP FOCUS
    edit_isp.addEventListener('shown.bs.modal', function () {
        edit_label_name.focus()
    })

    add_isp_btn.addEventListener("click",function(){
        if(!isp_name.value){ isp_name.value = "Others" }
        if(label_name.value){
            if(wan_ip.value){
                sole.post("../../controllers/isp/add_isp.php",{
                    uid: localStorage.getItem("userid"),
                    name: label_name.value,
                    isp_name: isp_name.value,
                    wan_ip: wan_ip.value,
                    subnet: subnet.value,
                    gateway: gateway.value,
                    dns1: dns1.value,
                    dns2: dns2.value,
                    webmgmtpt: isp_webmgmtpt.value
                }).then(res => validateResponse(res,"add_isp"))   
            }else{
                bs5.toast("warning","Please input WAN IP.")
            }
        }else{
            bs5.toast("warning","Please provide name.")
        }
    })

    edit_isp_btn.addEventListener("click",function(){
        if(!edit_isp_name.value){ edit_isp_name.value = "Others" }
        if(edit_label_name.value){
            if(edit_wan_ip.value){
                sole.post("../../controllers/isp/edit_isp.php",{
                    uid: localStorage.getItem("userid"),
                    id: this.getAttribute("i-id"),
                    name: edit_label_name.value,
                    isp_name: edit_isp_name.value,
                    wan_ip: edit_wan_ip.value,
                    subnet: edit_subnet.value,
                    gateway: edit_gateway.value,
                    dns1: edit_dns1.value,
                    dns2: edit_dns2.value,
                    webmgmtpt: edit_isp_webmgmtpt.value
                }).then(res => validateResponse(res,"edit_isp"))   
            }else{
                bs5.toast("warning","Please input WAN IP.")
            }
        }else{
            bs5.toast("warning","Please provide name.")
        }
    })

    isp_name.addEventListener("change",function(){
        if(this.value == "PLDT Inc."){
            isp_icon.setAttribute("src","../../assets/img/pldt.png")
            isp_icon.setAttribute("class","ht-20")
        }else if(this.value == "Globe Telecom, Inc."){
            isp_icon.setAttribute("src","../../assets/img/globe.png")
            isp_icon.setAttribute("class","ht-30")
        }else if(this.value == "Converge ICT Solutions Inc."){
            isp_icon.setAttribute("src","../../assets/img/converge.png")
            isp_icon.setAttribute("class","ht-30")
        }else{
            isp_icon.setAttribute("src","../../assets/img/hero.png")
            isp_icon.setAttribute("class","ht-30")
        }
    })

    edit_isp_name.addEventListener("change",function(){
        if(this.value == "PLDT Inc."){
            edit_isp_icon.setAttribute("src","../../assets/img/pldt.png")
            edit_isp_icon.setAttribute("class","ht-20")
        }else if(this.value == "Globe Telecom, Inc."){
            edit_isp_icon.setAttribute("src","../../assets/img/globe.png")
            edit_isp_icon.setAttribute("class","ht-30")
        }else if(this.value == "Converge ICT Solutions Inc."){
            edit_isp_icon.setAttribute("src","../../assets/img/converge.png")
            edit_isp_icon.setAttribute("class","ht-30")
        }else{
            edit_isp_icon.setAttribute("src","../../assets/img/hero.png")
            edit_isp_icon.setAttribute("class","ht-30")
        }
    })

    delete_isp_btn.addEventListener("click",function(){
        sole.post("../../controllers/isp/delete_isp.php",{
            id: this.getAttribute("i-id")
        }).then(res => validateResponse(res,"delete_isp"))
    })

    function deleteMessage(res){
        if(res.status){
            if(res.wan1.length){
                delete_isp_message.innerHTML = "This ISP will also be removed from router \"<b>" + res.wan1[0]["name"] + "</b>, this can't be undone.\""
            }
            if(res.wan2.length){
                delete_isp_message.innerHTML = "This ISP will also be removed from router \"<b>" + res.wan2[0]["name"] + "</b>, this can't be undone.\""
            }
        }else{
            delete_isp_message.innerHTML = "This ISP is not assigned to any router."
        }
    }

    function editForm(res){
        if(res.isp[0]["isp_name"] == "PLDT Inc."){
            edit_isp_icon.setAttribute("src","../../assets/img/pldt.png")
            edit_isp_icon.setAttribute("class","ht-20")
        }else if(res.isp[0]["isp_name"] == "Globe Telecom, Inc."){
            edit_isp_icon.setAttribute("src","../../assets/img/globe.png")
            edit_isp_icon.setAttribute("class","ht-30")
        }else if(res.isp[0]["isp_name"] == "Converge ICT Solutions Inc."){
            edit_isp_icon.setAttribute("src","../../assets/img/converge.png")
            edit_isp_icon.setAttribute("class","ht-30")
        }else{
            edit_isp_icon.setAttribute("src","../../assets/img/hero.png")
            edit_isp_icon.setAttribute("class","ht-30")
        }

        edit_label_name.value = res.isp[0]["name"] != "-" ? res.isp[0]["name"] : ""
        edit_isp_name.value = res.isp[0]["isp_name"] != "-" ? res.isp[0]["isp_name"] : ""
        edit_wan_ip.value = res.isp[0]["wan_ip"] != "-" ? res.isp[0]["wan_ip"] : ""
        edit_subnet.value = res.isp[0]["subnet"] != "-" ? res.isp[0]["subnet"] : ""
        edit_gateway.value = res.isp[0]["gateway"] != "-" ? res.isp[0]["gateway"] : ""
        edit_dns1.value = res.isp[0]["dns1"] != "-" ? res.isp[0]["dns1"] : ""
        edit_dns2.value = res.isp[0]["dns2"] != "-" ? res.isp[0]["dns2"] : ""
        edit_isp_webmgmtpt.value = res.isp[0]["webmgmtpt"] != "-" ? res.isp[0]["webmgmtpt"] : ""
        edit_isp_modal.show();
    }

    function validateResponse(res, func){
        if(res.status){
            if(func == "add_isp"){
                label_name.value = ""
                isp_name.value = ""
                wan_ip.value = ""
                subnet.value = ""
                gateway.value = ""
                dns1.value = ""
                dns2.value = ""
                isp_webmgmtpt.value = ""
                add_isp_modal.hide();
                sole.get("../../controllers/isp/get_isp.php").then(res => loadISP(res))
            }
            if(func == "edit_isp"){
                sole.get("../../controllers/isp/get_isp.php").then(res => loadISP(res))
            }
            if(func == "delete_isp"){
                sole.get("../../controllers/isp/get_isp.php").then(res => loadISP(res))
            }
            bs5.toast(res.type,res.message,res.size)
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }
}