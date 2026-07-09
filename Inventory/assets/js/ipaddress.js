if(document.getElementById("ipaddress")){
    let ipTable = new DataTable('#network_table',{
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
                target: 5,
                visible: false,
            },
            { 
                className: 'dt-left', 
                targets: '_all' 
            }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "<button id=\"ip_import\" style=\"margin-right: 10px; padding-left: 10px;\" class=\"btn btn-sm btn-secondary rounded-pill position-relative\"><span class=\" fa fa-upload\"></span> Import</button><button id=\"ip_export\" style=\"margin-right: 10px; padding-left: 10px;\" class=\"btn btn-sm btn-secondary rounded-pill position-relative\"><span class=\" fa fa-download\"></span> Export</button>   Search: "
        }
    });

    loadPage();
    // LOAD PAGE DATA
    function loadPage(){
        sole.get("../../controllers/ipaddress/get_network.php").then(res => loadNetwork(res))
        if(localStorage.getItem("selected_network")){
            sole.post("../../controllers/ipaddress/get_ip.php", {
                nid: localStorage.getItem("selected_network_id")
            }).then(res => loadIP(res))
        }
    }

    const add_network_modal = new bootstrap.Modal(document.getElementById('add_network'),unclose);
    const edit_network_modal = new bootstrap.Modal(document.getElementById('edit_network'),unclose);
    const delete_network_modal = new bootstrap.Modal(document.getElementById('delete_network'),unclose);
    const edit_ip_modal = new bootstrap.Modal(document.getElementById('edit_ip'),unclose);
    const import_modal = new bootstrap.Modal(document.getElementById('import_modal'),unclose);
    const unassign_ip_modal = new bootstrap.Modal(document.getElementById('unassign_ip_modal'),unclose);

    var add_network = document.getElementById("add_network")
    var edit_network = document.getElementById("edit_network")
    var network_name = document.getElementById("network_name")
    var ip_range_from = document.getElementById("ip_range_from")
    var ip_range_to = document.getElementById("ip_range_to")
    var ip_subnet = document.getElementById("ip_subnet")
    var ip_gateway_select = document.getElementById("ip_gateway_select")
    var ip_gateway = document.getElementById("ip_gateway")
    var add_network_btn = document.getElementById("add_network_btn")
    var ready_state = document.getElementById("ready_state")
    var saving_state = document.getElementById("saving_state")
    var edit_network_name = document.getElementById("edit_network_name")
    var edit_ip_range_from = document.getElementById("edit_ip_range_from")
    var edit_ip_range_to = document.getElementById("edit_ip_range_to")
    var edit_ip_subnet = document.getElementById("edit_ip_subnet")
    var edit_ip_gateway_select = document.getElementById("edit_ip_gateway_select")
    var edit_ip_gateway = document.getElementById("edit_ip_gateway")
    var edit_network_btn = document.getElementById("edit_network_btn")
    var edit_ready_state = document.getElementById("edit_ready_state")
    var edit_saving_state = document.getElementById("edit_saving_state")
    var delete_network_name = document.getElementById("delete_network_name")
    var delete_network_btn = document.getElementById("delete_network_btn")
    var delete_ready_state = document.getElementById("delete_ready_state")
    var delete_saving_state = document.getElementById("delete_saving_state")
    var ip_import_input = document.getElementById("ip_import_input")
    var ip_import = document.getElementById("ip_import")
    var import_message = document.getElementById("import_message")

    var hostname = document.getElementById("hostname")
    var site = document.getElementById("site")
    var server = document.getElementById("server")
    var webmgmtpt = document.getElementById("webmgmtpt")
    var username = document.getElementById("username")
    var state = document.getElementById("state")
    var password = document.getElementById("password")
    var remarks = document.getElementById("remarks")
    
    // EDIT ENTRY FOCUS
    var edit_ip = document.getElementById('edit_ip')
    // var edit_entry_description_input = document.getElementById('edit_entry_description_input')
    var edit_ip_btn = document.getElementById('edit_ip_btn')
    var edit_ip_title = document.getElementById('edit_ip_title')
    var edit_network_name_temp = ""

    var unassign_ip_name = document.getElementById("unassign_ip_name")
    var unassign_ip_btn = document.getElementById("unassign_ip_btn")

    // EDIT IP FOCUS
    edit_ip.addEventListener('shown.bs.modal', function () {
        hostname.focus()
    })

    // ADD NETWORK FOCUS
    add_network.addEventListener('shown.bs.modal', function () {
        network_name.focus()
        sole.get("../../controllers/ipaddress/get_routers.php")
        .then(res => dropRouters_Add(res))
    })
    // EDIT NETWORK FOCUS
    edit_network.addEventListener('shown.bs.modal', function () {
        sole.post("../../controllers/ipaddress/get_routers_edit.php",{
            id: edit_network_btn.getAttribute("nid")
        }).then(res => dropRouters_Edit(res))
        edit_network_name_temp = edit_network_name.value;
        edit_network_name.focus()
    })

    // POST EDIT NETWORK
    edit_network_btn.addEventListener("click", function(){
        !edit_network_name.value ? ss.toast("Please provide network name.","warning",null,null,"#212529") : null

        edit_ready_state.style = "display: none;"
        edit_saving_state.style = "display: flex;"

        sole.post("../../controllers/ipaddress/edit_network.php",{
            id: edit_network_btn.getAttribute("nid"),
            name: edit_network_name.value,
        }).then(res => validateResponse(res,"edit_network"))  
    })

    function ipToLong(ip) {
        return ip.split('.').reduce((acc, octet) => (acc << 8) + parseInt(octet, 10), 0) >>> 0;
    }

    function longToIp(long) {
        return [
            (long >>> 24) & 0xFF,
            (long >>> 16) & 0xFF,
            (long >>> 8) & 0xFF,
            long & 0xFF
        ].join('.');
    }

    function cidrToRange(ip, prefix) {
        const ipLong = ipToLong(ip);
        const mask = prefix === 0 ? 0 : (0xFFFFFFFF << (32 - prefix)) >>> 0;

        const network = ipLong & mask;
        const broadcast = network | (~mask >>> 0);

        if (prefix === 31 || prefix === 32) {
            return [null, null]; // no usable hosts
        }

        return [network + 1, broadcast - 1];
    }

    function listUsableIPsFromCIDR(ip, subnet, nid) {
        // subnet format: "255.255.255.252 / 30"
        const [maskStr, prefixStr] = subnet.split("/");
        const prefix = parseInt(prefixStr.trim(), 10);

        const [start, end] = cidrToRange(ip, prefix);

        if (start === null || end === null) {
            return "";
        }

        let output = "(";

        for (let i = start; i <= end; i++) {
            output += `'${nid}', '${longToIp(i)}', '${subnet}', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-'`;

            if (i < end) {
            output += " ),\n( ";
            }
        }

        return output;
    }

    // POST ADD NETWORK
    add_network_btn.addEventListener("click", function () {
        if(!ip_gateway_select.value || ip_gateway_select.value == "-"){
            ss.toast("Please select a router.","warning",null,null,"#212529")
            return
        }
        ready_state.style = "display: none;"
        saving_state.style = "display: flex;"
        sole.post("../../controllers/ipaddress/add_network.php", {
            uid: localStorage.getItem("userid"),
            name: network_name.value,
            from: ip_range_from.value,
            to: ip_range_to.value,
            subnet: ip_subnet.value,
            gateway: ip_gateway_select.value.split("|")
        }).then(res => validateResponse(res,"add_network"))
    })

    // POST DELETE NETWOK
    delete_network_btn.addEventListener("click",function(){
        delete_ready_state.style = "display: none;"
        delete_saving_state.style = "display: flex;"
        sole.post("../../controllers/ipaddress/delete_network.php",{
            id: this.getAttribute("nid")
        }).then(res => validateResponse(res,"delete_network"))
    })

    // POST EDIT IP
    edit_ip_btn.addEventListener("click",function(){
        sole.post("../../controllers/ipaddress/edit_ip.php",{
            id: this.getAttribute("i-id"),
            hostname: hostname.value,
            site: site.value,
            server: server.value,
            webmgmtpt: webmgmtpt.value,
            username: username.value,
            password: password.value,
            state: state.value,
            remarks: remarks.value
        }).then(res => validateResponse(res,"edit_ip"))
    })

    // UNASSIGN IP
    unassign_ip_btn.addEventListener("click",function(){
        sole.post("../../controllers/ipaddress/unassign_ip.php",{
            id: this.getAttribute("i-id")
        }).then(res => validateResponse(res,"unassign_ip"))
    })

    // TOGGLE EDIT NETWORK MODAL
    var network_dropdown = document.getElementById("network_dropdown");
    var network_dropdown_toggle = document.getElementById("network_dropdown_toggle");
    network_dropdown.addEventListener("contextmenu", e=>{
        if(e.target.classList.contains("dropdown-item")){
            sole.post("../../controllers/ipaddress/find_network.php",{
                id: e.target.getAttribute("id")
            }).then(res => editNetworkForm(res))
        }
    })

    // SELECT NETWORK
    network_dropdown.addEventListener("click", e=>{
        if(e.target.classList.contains("dropdown-item")){
            network_dropdown_toggle.innerText = e.target.innerText
            localStorage.setItem("selected_network", e.target.innerText);
            localStorage.setItem("selected_network_id", e.target.getAttribute("id"));
            sole.post("../../controllers/ipaddress/get_ip.php", {
                nid: localStorage.getItem("selected_network_id")
            }).then(res => loadIP(res))
        }
    })


    // SET GATEWAY
    ip_gateway_select.addEventListener("change",function(){
        if(this.value != "-"){
            let val = this.value.split("|")
            let range_ = getUsableRange(val[1],parseInt(val[2].split("/")[1]))
            ip_gateway.value = val[1]
            ip_subnet.value = val[2]
            ip_range_from.value = range_.firstUsable
            ip_range_to.value = range_.lastUsable
        }else{
            ip_range_from.value = ""
            ip_range_to.value = ""
            ip_subnet.value = ""
            ip_gateway.value = ""
        }
    })

    // SET GATEWAY
    edit_ip_gateway_select.addEventListener("change",function(){
        if(this.value != "-"){
            let val = this.value.split("|")
            let range_ = getUsableRange(val[1],parseInt(val[2].split("/")[1]))
            edit_ip_gateway.value = val[1]
            edit_ip_subnet.value = val[2]
            edit_ip_range_from.value = range_.firstUsable
            edit_ip_range_to.value = range_.lastUsable
        }else{
            edit_ip_range_from.value = ""
            edit_ip_range_to.value = ""
            edit_ip_subnet.value = ""
            edit_ip_gateway.value = ""
        }
    })

    function dropRouters_Add(res){
        ip_gateway_select.innerHTML = ""
        var op = document.createElement("option")
        op.setAttribute("disabled","true")
        op.setAttribute("selected","true")
        op.value = "-"
        op.innerText = "-- Select Router --"
        ip_gateway_select.appendChild(op)

        res.forEach(r => {
            var op = document.createElement("option")
            op.value = r["id"] +  "|" + r["ip"] + "|" + r["subnet"]
            op.innerText = r["name"]
            ip_gateway_select.appendChild(op)
        });
    }

    function dropRouters_Edit(res){

        edit_ip_gateway_select.innerHTML = ""
        var op = document.createElement("option")
        op.setAttribute("disabled","true")
        op.setAttribute("selected","true")
        op.value = "-"
        op.innerText = "-- Select Router --"
        edit_ip_gateway_select.appendChild(op)

        var gateway_ip = false
        res.router.forEach(r => {
            var op = document.createElement("option")
            op.value = r["id"] +  "|" + r["ip"] + "|" + r["subnet"]
            if(res.network[0]["rid"] == r["id"]){
                op.setAttribute("selected","true")
                edit_ip_gateway.value = r["ip"]
                gateway_ip = true
            }
            gateway_ip ? edit_ip_gateway.value = r["ip"] : edit_ip_gateway.value = ""
            op.innerText = r["name"]
            edit_ip_gateway_select.appendChild(op)
        });
    }

    function editNetworkForm(res){
        if(res.status){
            edit_network_name.value = res.network[0].name
            edit_ip_range_from.value = res.network[0].from
            edit_ip_range_to.value = res.network[0].to
            edit_ip_subnet.value = res.network[0].subnet
            edit_network_btn.setAttribute("nid",res.network[0].id)
            delete_network_name.innerText = res.network[0].name
            delete_network_btn.setAttribute("nid",res.network[0].id)
            edit_network_modal.show()
        }else{
            ss.toast(res.message,res.type,null,null,"#212529")
        }
    }

    function usernameSupport(username){
        if(username != "-"){
            return username
        }else{
            return ""
        }
    }

    function setUnassignBtn(e){
        if(e["status"] == "ASSIGNED"){
            return " <button id=\"unassign_ip_"+ e["id"] +"\" i-id=\""+ e["id"] +"\" class=\"unassign_ip_row btn btn-sm btn-danger\"><i i-id=\""+ e["id"] +"\" class=\"unassign_ip_row fa fa-ban\"></i></button>"
        }
        return ""
    }
    function passwordSupport(password){
        if(password != "-"){
            if (password.length <= 2) return password;
            return password.charAt(0) + '*'.repeat(password.length - 2) + password.charAt(password.length - 1);
        }else{
            return ""
        }
    }
    function loadIP(res){
        ipTable.clear();
        var ip_count = [0,0];
        var used_ip = document.getElementById("used_ip");
        var available_ip = document.getElementById("available_ip");

        res.ip.forEach(e => {
            e["status"] == "ASSIGNED" ? ip_count[0]++ : ip_count[1]++
            ipTable.row.add([
                e["id"],
                e["ip"],
                e["hostname"] != "-" ? e["hostname"] : "",
                e["site"] != "-" ? e["site"] : "",
                e["server"] != "-" ? e["server"] : "",
                e["status"] != "UNASSIGNED" ? "A - USED" : "B - AVAILABLE",
                e["state"] == "DOWN" ? "<div class=\"red-circle mx-auto\"></div>" : "<div class=\"green-circle mx-auto\"></div>",
                e["webmgmtpt"] != "-" ? e["webmgmtpt"] : "",
                e["username"] != "-" || e["password"] != "-" ? "<div class=\"f-10\"><b>Username: </b>" + usernameSupport(e["username"]) + " <br> " + "<b>Password: </b>" + passwordSupport(e["password"]) + "</div>": "",
                " <button id=\"edit_ip_"+ e["id"] +"\" i-id=\""+ e["id"] +"\" class=\"edit_ip_row btn btn-sm btn-secondary\"><i i-id=\""+ e["id"] +"\" class=\"edit_ip_row fa fa-edit\"></i></button>"
                + setUnassignBtn(e)
            ])
        });
        ipTable.draw();
        used_ip.innerText = "Used IP: " + ip_count[0]
        available_ip.innerText = "Available IP: " + ip_count[1]
    }

    document.querySelector('#network_table').addEventListener("click", e=>{
        let tr = "";
        if(e.target.tagName == "I"){
            tr = e.target.parentNode.parentNode.parentNode.children
        }
        if(e.target.tagName == "BUTTON"){
            tr = e.target.parentNode.parentNode.children    
        }
        if(e.target.classList.contains('edit_ip_row')) {
            edit_ip_title.innerText = "Edit IP: " + tr[0].innerText
            edit_ip_btn.setAttribute("i-id",e.target.getAttribute("i-id"))
            sole.post("../../controllers/ipaddress/find_ip.php",{
                id: e.target.getAttribute("i-id")
            }).then(res => editForm(res))
        }
        if(e.target.classList.contains('unassign_ip_row')){
            unassign_ip_name.innerText = "Unassign IP: " + tr[0].innerText
            unassign_ip_btn.setAttribute("i-id",e.target.getAttribute("i-id"))
            unassign_ip_modal.show()
        }
    })

    function editFormClear(){
        hostname.value = ""
        site.value = ""
        server.value = ""
        webmgmtpt.value = ""
        username.value = ""
        password.value = ""
        remarks.value = ""
    }
    
    function editForm(res){
        res.ip[0].status == "ASSIGNED" ? edit_ip_btn.innerHTML = "<span class=\"fa fa-save\"></span> Update" : edit_ip_btn.innerHTML = "<span class=\"fa fa-save\"></span> Save"
        res.ip[0].hostname != "-" ? hostname.value = res.ip[0].hostname : hostname.value = ""
        res.ip[0].site != "-" ? site.value = res.ip[0].site : site.value = ""
        res.ip[0].server != "-" ? server.value = res.ip[0].server : server.value = ""
        res.ip[0].webmgmtpt != "-" ? webmgmtpt.value = res.ip[0].webmgmtpt : webmgmtpt.value = ""
        res.ip[0].username != "-" ? username.value = res.ip[0].username : username.value = ""
        state.value = res.ip[0].state
        res.ip[0].password != "-" ? password.value = res.ip[0].password : password.value = ""
        res.ip[0].remarks != "-" ? remarks.value = res.ip[0].remarks : remarks.value = ""
        edit_ip_modal.show()
    }

    function validateResponse(res, func){
        if(func == "add_network"){
            sole.get("../../controllers/ipaddress/get_network.php").then(res => loadNetwork(res))
            ready_state.style = ""
            saving_state.style = "display: none;"
            ip_gateway.value = ""
            ip_gateway_select.innerHTML = ""
            var op = document.createElement("option")
            op.setAttribute("disabled","true")
            op.setAttribute("selected","true")
            op.value = "-"
            op.innerText = "-- Select Router --"
            ip_gateway_select.appendChild(op)
            add_network_modal.hide();
        }
        if(func == "edit_network"){
            edit_ready_state.style = ""
            edit_saving_state.style = "display: none;"
            edit_ip_gateway.value = ""
            var op = document.createElement("option")
            op.setAttribute("disabled","true")
            op.setAttribute("selected","true")
            op.value = "-"
            op.innerText = "-- Select Router --"
            edit_ip_gateway_select.appendChild(op)

        }
        if(func == "delete_network"){
            delete_ready_state.style = ""
            delete_saving_state.style = "display: none;"
        }
        if(func == "ip_import"){
            import_modal.hide()
            ip_import.removeAttribute("disabled")
            ip_import_input.value = ""
        }
        if(res.status){
            if(func == "add_network"){
                network_name.value = ""
                ip_range_from.value = ""
                ip_range_to.value = ""
                ip_subnet.value = ""
                sole.get("../../controllers/ipaddress/get_network.php").then(res => loadNetwork(res))
            }
            if(func == "edit_network"){
                if(edit_network_name_temp == localStorage.getItem("selected_network")){
                    network_dropdown_toggle.innerText = edit_network_name.value
                    localStorage.setItem("selected_network", edit_network_name.value);
                    localStorage.setItem("selected_network_id", edit_network_btn.getAttribute("nid"));
                }
                edit_network_modal.hide();
                sole.get("../../controllers/ipaddress/get_network.php").then(res => loadNetwork(res))
            }
            if(func == "delete_network"){
                if(delete_network_name.innerText == localStorage.getItem("selected_network")){
                    network_dropdown_toggle.innerText = "-- Select Network --"
                    localStorage.removeItem("selected_network");
                    localStorage.removeItem("selected_network_id");
                }
                delete_network_modal.hide();
                sole.get("../../controllers/ipaddress/get_network.php").then(res => loadNetwork(res))
            }
            if(func == "ip_import"){
                ip_import.removeAttribute("disabled")
                import_message.innerHTML = ""
                import_modal.hide()
                sole.get("../../controllers/ipaddress/get_network.php").then(res => loadNetwork(res))
            }
            if(func == "edit_ip"){
                editFormClear()
                edit_ip_modal.hide()
                sole.post("../../controllers/ipaddress/get_ip.php", {
                    nid: localStorage.getItem("selected_network_id")
                }).then(res => loadIP(res))
            }
            if(func == "unassign_ip"){
                unassign_ip_modal.hide()
                sole.post("../../controllers/ipaddress/get_ip.php", {
                    nid: localStorage.getItem("selected_network_id")
                }).then(res => loadIP(res))
            }
            ss.toast(res.message,res.type,null,null,"#212529")
        }else{
            ss.toast(res.message,res.type,null,null,"#212529")
        }
        
    }

    function loadNetwork(res){
        if(network_dropdown_toggle.innerText == "-- Select Network --"){
            if (localStorage.getItem("selected_network") && res.networks.length){
                network_dropdown_toggle.innerText = localStorage.getItem("selected_network")
            }else{
                if(res.networks.length){
                    network_dropdown_toggle.innerText = res.networks[0]["name"]
                    localStorage.setItem("selected_network", res.networks[0]["name"]);
                    localStorage.setItem("selected_network_id", res.networks[0]["id"]);
                }else{
                    localStorage.removeItem("selected_network");
                    localStorage.removeItem("selected_network_id");
                }
            }
        }
        if(localStorage.getItem("selected_network")){
            sole.post("../../controllers/ipaddress/get_ip.php", {
                nid: localStorage.getItem("selected_network_id")
            }).then(res => loadIP(res))
        }
        network_dropdown.innerHTML = ""
        res.networks.forEach(net => {
            network_dropdown.innerHTML += "<li><a href=\"#\" class=\"dropdown-item\" id=\""+ net["id"] +"\" >"+ net["name"] +"</a></li>"
        });
    }

    document.getElementById("ip_range_from").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9.]/g, "");
    });
    
    document.getElementById("ip_range_to").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9.]/g, "");
    });
    
    document.getElementById("ip_subnet").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9.]/g, "");
    });
    
    document.getElementById("edit_ip_subnet").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9.]/g, "");
    });

    document.getElementById("ip_export").addEventListener("click",function(){
        if(localStorage.getItem("selected_network")){
            const baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/') + '/';
            sole.post("../../controllers/ipaddress/ip_export.php",{
                id: localStorage.getItem("selected_network_id")
            }).then(res => downloadFile(baseUrl + res[0], res[1]))    
        }else{
            ss.toast("Nothing to export.","warning",null,null,"#212529")
        }
    })

    ip_import.addEventListener("click",function(){
        ip_import_input.click()
    })
    ip_import_input.addEventListener("change",function(){
        if(this.files.length > 0){
            ip_import.setAttribute("disabled","")
            let imp = true;
            if(this.files[0].name.split('.').pop().toLowerCase() != "xlsx" && this.files[0].name.split('.').pop().toLowerCase() != "xls"){
                ss.toast("Invalid file.","warning",null,null,"#212529")
                ip_import.removeAttribute("disabled")
                imp = false
            }
            if(this.files[0].size > 102400){
                ss.toast("File exceed the maximum file size of 100KB only.","warning",null,null,"#212529")
                ip_import.removeAttribute("disabled")
                imp = false
            }
            if(imp){
                import_message.innerHTML = "<span class=\"spinner-border spinner-border-sm spinner-primary\" role=\"status\" aria-hidden=\"true\"></span> Importing: " + this.files[0].name
                import_modal.show()
                const formData = new FormData();
                formData.append("file",this.files[0])
                sole.file("../../controllers/ipaddress/ip_import.php",formData)
                .then(res => validateResponse(res,"ip_import"))
            }    
        }
    })

    function downloadFile(url, filename) {
        fetch(url)
        .then(response => response.blob()) // Convert response to a Blob
        .then(blob => {
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = filename; // Set the filename
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        })
        .catch(error => ss.toast("Export Failed: " + error,"error",null,null,"#212529"));

        setTimeout(() => {
            sole.post("../../controllers/clear_temp.php").then(res => console.log(res));
        }, 5000);
    }
        function ipToInt(ip) {
        return ip.split('.').reduce((acc, octet) => {
            return (acc << 8) + parseInt(octet, 10);
        }, 0) >>> 0;
    }

    function intToIp(int) {
        return [
            (int >>> 24) & 255,
            (int >>> 16) & 255,
            (int >>> 8) & 255,
            int & 255
        ].join('.');
    }

    function getUsableRange(ip, prefix) {
        const ipInt = ipToInt(ip);

        const mask = prefix === 0 
            ? 0 
            : (~0 << (32 - prefix)) >>> 0;

        const network = ipInt & mask;
        const broadcast = network | (~mask >>> 0);

        let firstUsable = network + 1;
        let lastUsable = broadcast - 1;

        // Handle /31 and /32
        if (prefix >= 31) {
            firstUsable = false;
            lastUsable = false;
        } else {
            firstUsable = intToIp(firstUsable);
            lastUsable = intToIp(lastUsable);
        }

        return {
            subnetMask: intToIp(mask),
            network: intToIp(network),
            broadcast: intToIp(broadcast),
            firstUsable,
            lastUsable
        };
    }

    document.querySelector('#network_table tbody').addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;

        // Remove selected from all rows
        document.querySelectorAll('#network_table tbody tr').forEach(r => {
            r.classList.remove('selected');
        });

        // Add selected to clicked row
        row.classList.add('selected');
    });
}