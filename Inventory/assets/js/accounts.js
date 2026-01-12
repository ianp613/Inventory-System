if(document.getElementById("accounts")){
    let accounts_table = new DataTable('#accounts_table',{
        order: [[1, 'asc']],
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
                visible: getPrivilege(),
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

    function getPrivilege(){
        if(localStorage.getItem("privileges") == "User"){
            return false;    
        }else{
            return true;
        }
        
    }

    loadPage()
    function loadPage(){
        sole.get("../../controllers/administrator/get_accounts.php").then(res => loadAccounts(res))
        sole.get("../../controllers/administrator/get_group.php").then(res => loadGroup(res))
    }

    const edit_account_modal = new bootstrap.Modal(document.getElementById('edit_account'),unclose);
    const delete_account_modal = new bootstrap.Modal(document.getElementById('delete_account'),unclose);
    const add_group_modal = new bootstrap.Modal(document.getElementById('add_group'),unclose);
    const edit_group_modal = new bootstrap.Modal(document.getElementById('edit_group'),unclose);

    var group_dropdown = document.getElementById("group_dropdown")

    var add_group = document.getElementById("add_group")
    var group_name = document.getElementById("group_name")
    var group_type = document.getElementById("group_type")
    var group_supervisor = document.getElementById("group_supervisor")
    var group_user = document.getElementById("group_user")
    var add_group_btn = document.getElementById("add_group_btn")

    var user_container = document.getElementById("user_container")
    var supervisor_container = document.getElementById("supervisor_container")

    var user_container_temp = []
    var supervisor_container_temp = []


    var edit_group = document.getElementById("edit_group")
    var edit_group_name = document.getElementById("edit_group_name")
    var edit_group_type = document.getElementById("edit_group_type")
    var edit_group_supervisor = document.getElementById("edit_group_supervisor")
    var edit_group_user = document.getElementById("edit_group_user")
    var edit_group_btn = document.getElementById("edit_group_btn")

    var edit_user_container = document.getElementById("edit_user_container")
    var edit_supervisor_container = document.getElementById("edit_supervisor_container")

    var edit_user_container_temp = []
    var edit_supervisor_container_temp = []
    var edit_supervisor_temp = []
    var edit_user_temp = []

    var add_account = document.getElementById("add_account")
    var add_account_btn = document.getElementById("add_account_btn")
    var add_name = document.getElementById("add_name")
    var add_email = document.getElementById("add_email")
    var add_username = document.getElementById("add_username")
    var add_password = document.getElementById("add_password")
    var add_privilege = document.getElementById("add_privilege")

    var edit_account = document.getElementById("edit_account")
    var edit_account_btn = document.getElementById("edit_account_btn")
    var edit_account_name = document.getElementById("edit_account_name")
    var edit_email = document.getElementById("edit_email")
    var edit_username = document.getElementById("edit_username")
    var edit_password = document.getElementById("edit_password")
    var edit_privilege = document.getElementById("edit_privilege")
    var edit_account_title = document.getElementById("edit_account_title")

    var delete_account_btn = document.getElementById("delete_account_btn")
    var delete_account_name = document.getElementById("delete_account_name")

    add_group.addEventListener('shown.bs.modal', function () {
        sole.get("../../controllers/administrator/get_accounts_dropdown.php")
        .then(res => {
            group_supervisor.innerHTML = "<option value=\"\" selected disabled>Select User</option>"

            res["supervisor"].forEach(su => {
                var op = document.createElement("option")
                op.value = su["username"] + " - " + su["name"]
                op.innerText = su["username"] + " - " + su["name"]
                group_supervisor.appendChild(op)
            });

            group_user.innerHTML = "<option value=\"\" selected disabled>Select User</option>"

            res["user"].forEach(us => {
                var op = document.createElement("option")
                op.value = us["username"] + " - " + us["name"]
                op.innerText = us["username"] + " - " + us["name"]
                group_user.appendChild(op)
            });
        })
        group_name.focus()
    })

    edit_group.addEventListener('shown.bs.modal', function () {
        sole.get("../../controllers/administrator/get_accounts_dropdown.php")
        .then(res => {
            edit_group_supervisor.innerHTML = "<option value=\"\" selected disabled>Select User</option>"

            console.log(res["supervisor"])
            res["supervisor"].forEach(su => {
                var op = document.createElement("option")
                op.value = su["username"] + " - " + su["name"]
                op.innerText = su["username"] + " - " + su["name"]
                edit_group_supervisor.appendChild(op)
            });

            edit_group_user.innerHTML = "<option value=\"\" selected disabled>Select User</option>"

            res["user"].forEach(us => {
                var op = document.createElement("option")
                op.value = us["username"] + " - " + us["name"]
                op.innerText = us["username"] + " - " + us["name"]
                edit_group_user.appendChild(op)
            });
        })

        setTimeout(() => {
            edit_supervisor_temp.forEach(est => {
                var op = document.createElement("option")
                op.value = est["username"] + " - " + est["name"]
                op.innerText = est["username"] + " - " + est["name"]
                edit_group_supervisor.appendChild(op)
            });

            edit_user_temp.forEach(eut => {
                var op = document.createElement("option")
                op.value = eut["username"] + " - " + eut["name"]
                op.innerText = eut["username"] + " - " + eut["name"]
                edit_group_user.appendChild(op)
            });
            edit_supervisor_temp = []
            edit_user_temp = []
        }, 100);
        edit_group_name.focus()
    })

    group_supervisor.addEventListener("change",function(){
        if(!supervisor_container_temp.includes(this.value)){
            var wrapper = document.createElement("div")
            var name = document.createElement("div")
            var button = document.createElement("div")
            var button_span = document.createElement("span")
            wrapper.setAttribute("class","i-block rounded user-list alert-success p-1 ps-2 pe-2 mt-1 ms-1")
            name.setAttribute("class","i-block user-name ft-13")
            button.setAttribute("class","i-block user-remove ms-2")
            button_span.setAttribute("class","fa fa-remove")

            button.addEventListener("click",function(){
                var text_temp = this.parentNode.children[0].innerText
                if(supervisor_container_temp.includes(text_temp)){
                    supervisor_container_temp = supervisor_container_temp.filter(value => value !== text_temp)
                }
                this.parentNode.remove()
                group_supervisor.value = ""
            })
            name.innerText = this.value

            button.appendChild(button_span)
            wrapper.appendChild(name)
            wrapper.appendChild(button)
            supervisor_container.appendChild(wrapper)
            supervisor_container_temp.push(this.value)
            this.value = ""
        }
    })

    group_user.addEventListener("change",function(){
        if(!user_container_temp.includes(this.value)){
            var wrapper = document.createElement("div")
            var name = document.createElement("div")
            var button = document.createElement("div")
            var button_span = document.createElement("span")
            wrapper.setAttribute("class","i-block rounded user-list alert-success p-1 ps-2 pe-2 mt-1 ms-1")
            name.setAttribute("class","i-block user-name ft-13")
            button.setAttribute("class","i-block user-remove ms-2")
            button_span.setAttribute("class","fa fa-remove")

            button.addEventListener("click",function(){
                var text_temp = this.parentNode.children[0].innerText
                if(user_container_temp.includes(text_temp)){
                    user_container_temp = user_container_temp.filter(value => value !== text_temp)
                }
                this.parentNode.remove()
                group_user.value = ""
            })
            name.innerText = this.value

            button.appendChild(button_span)
            wrapper.appendChild(name)
            wrapper.appendChild(button)
            user_container.appendChild(wrapper)
            user_container_temp.push(this.value) 
            this.value = ""
        }
    })

    edit_group_supervisor.addEventListener("change",function(){
        if(!edit_supervisor_container_temp.includes(this.value)){
            var wrapper = document.createElement("div")
            var name = document.createElement("div")
            var button = document.createElement("div")
            var button_span = document.createElement("span")
            wrapper.setAttribute("class","i-block rounded user-list alert-success p-1 ps-2 pe-2 mt-1 ms-1")
            name.setAttribute("class","i-block user-name ft-13")
            button.setAttribute("class","i-block user-remove ms-2")
            button_span.setAttribute("class","fa fa-remove")

            button.addEventListener("click",function(){
                var text_temp = this.parentNode.children[0].innerText
                if(edit_supervisor_container_temp.includes(text_temp)){
                    edit_supervisor_container_temp = edit_supervisor_container_temp.filter(value => value !== text_temp)
                }
                this.parentNode.remove()
                edit_group_supervisor.value = ""
            })
            name.innerText = this.value

            button.appendChild(button_span)
            wrapper.appendChild(name)
            wrapper.appendChild(button)
            edit_supervisor_container.appendChild(wrapper)
            edit_supervisor_container_temp.push(this.value)
            this.value = ""
        }
    })

    edit_group_user.addEventListener("change",function(){
        if(!edit_user_container_temp.includes(this.value)){
            var wrapper = document.createElement("div")
            var name = document.createElement("div")
            var button = document.createElement("div")
            var button_span = document.createElement("span")
            wrapper.setAttribute("class","i-block rounded user-list alert-success p-1 ps-2 pe-2 mt-1 ms-1")
            name.setAttribute("class","i-block user-name ft-13")
            button.setAttribute("class","i-block user-remove ms-2")
            button_span.setAttribute("class","fa fa-remove")

            button.addEventListener("click",function(){
                var text_temp = this.parentNode.children[0].innerText
                if(edit_user_container_temp.includes(text_temp)){
                    edit_user_container_temp = edit_user_container_temp.filter(value => value !== text_temp)
                }
                this.parentNode.remove()
                edit_group_user.value = ""
            })
            name.innerText = this.value

            button.appendChild(button_span)
            wrapper.appendChild(name)
            wrapper.appendChild(button)
            edit_user_container.appendChild(wrapper)
            edit_user_container_temp.push(this.value) 
            this.value = ""
        }
    })

    add_group_btn.addEventListener("click",function(){
        if(group_name.value){
            sole.post("../../controllers/administrator/add_group.php",{
                group_name : group_name.value,
                type : group_type.value,
                supervisor : supervisor_container_temp,
                user : user_container_temp
            }).then(res => validateResponse(res,"add_group"))
        }else{
            bs5.toast("warning","Please input group name.")
        }
    })

    edit_group_btn.addEventListener("click",function(){
        if(edit_group_name.value){
            sole.post("../../controllers/administrator/edit_group.php",{
                id : this.getAttribute("g-id"),
                group_name : edit_group_name.value,
                type : edit_group_type.value,
                supervisor : edit_supervisor_container_temp,
                user : edit_user_container_temp
            }).then(res => validateResponse(res,"edit_group"))
        }else{
            bs5.toast("warning","Please input group name.")
        }
    })

    add_account.addEventListener('shown.bs.modal', function () {
        add_name.focus()
    })

    add_account_btn.addEventListener("click",function(){
        var message = "";
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        !add_username.value ? message = "Please input user ID." : null
        !add_name.value ? message = "Please input name." : null

        if(add_email.value){
            var bol = regex.test(add_email.value)
            if(!bol){
                message = "Please input a valid email."
            }
        }
        if(!message){
            sole.post("../../controllers/administrator/create_account.php",{
                name: add_name.value,
                email: add_email.value,
                username: add_username.value,
                password: add_password.value,
                privilege: add_privilege.value
            })
            .then(res => validateResponse(res,"create_account"))    
        }else{
            bs5.toast("warning",message)    
        }
    })

    edit_account.addEventListener('shown.bs.modal', function () {
        edit_account_name.focus()
    })

    edit_account_btn.addEventListener("click",function(){
        var message = "";
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        !edit_account_name.value ? message = "Please input name." : null
        !edit_username.value ? message = "Please input user ID." : null
        !edit_password.value ? alert("Password field is empty, it will be set to default password \"12345\".") : null

        if(edit_email.value){
            var bol = regex.test(edit_email.value)
            if(!bol){
                message = "Please input a valid email."
            }
        }
        
        if(!message){
            sole.post("../../controllers/administrator/edit_account.php",{
                id: this.getAttribute("u-id"),
                name: edit_account_name.value,
                email: edit_email.value,
                username: edit_username.value,
                password: edit_password.value,
                privilege: edit_privilege.value
            }).then(res => validateResponse(res,"edit_account"))    
        }else{
            bs5.toast("warning",message);
        }
        
    })

    delete_account_btn.addEventListener("click",function(){
        sole.post("../../controllers/administrator/delete_account.php",{
            id: this.getAttribute("u-id")
        }).then(res => validateResponse(res,"delete_account"))
    })

    function loadGroup(res){
        if(document.getElementById("group_dropdown")){
            group_dropdown.innerHTML = ""
            res.groups.forEach(group => {
                group_dropdown.innerHTML += "<li><a href=\"#\" class=\"dropdown-item\" id=\""+ group["id"] +"\" >"+ group["group_name"] +"</a></li>"
            });    
        }
    }

    if(localStorage.getItem("privileges") == "Administrator" && document.getElementById("group_dropdown")){
        group_dropdown.addEventListener("contextmenu", e=>{
            if(e.target.classList.contains("dropdown-item")){
                sole.post("../../controllers/administrator/find_group.php",{
                    id: e.target.getAttribute("id")
                }).then(res => editGroupForm(res))
            }    
        })
        group_dropdown.addEventListener("click", e=>{
            if(e.target.classList.contains("dropdown-item")){
                sole.post("../../controllers/administrator/find_group.php",{
                    id: e.target.getAttribute("id")
                }).then(res => editGroupForm(res))
            }    
        })    
    }
    

    function editGroupForm(res) {
        edit_group_btn.setAttribute("g-id",res.group[0]["id"])
        edit_group_name.value = res.group[0]["group_name"]
        edit_group_type.value = res.group[0]["type"]

        var supervisors = res.group[0]["supervisors"].split("|")
        var users = res.group[0]["users"].split("|")

        edit_supervisor_container.innerHTML = ""
        edit_user_container.innerHTML = ""

        supervisors.forEach(sup => {
            res.users.forEach(user => {
                if(user["id"] == sup){
                    edit_supervisor_temp.push(user)
                    var wrapper = document.createElement("div")
                    var name = document.createElement("div")
                    var button = document.createElement("div")
                    var button_span = document.createElement("span")
                    wrapper.setAttribute("class","i-block rounded user-list alert-success p-1 ps-2 pe-2 mt-1 ms-1")
                    name.setAttribute("class","i-block user-name ft-13")
                    button.setAttribute("class","i-block user-remove ms-2")
                    button_span.setAttribute("class","fa fa-remove")

                    button.addEventListener("click",function(){
                        var text_temp = this.parentNode.children[0].innerText
                        if(edit_supervisor_container_temp.includes(text_temp)){
                            edit_supervisor_container_temp = edit_supervisor_container_temp.filter(value => value !== text_temp)
                        }
                        this.parentNode.remove()
                        edit_group_supervisor.value = ""
                    })

                    name.innerText = user["username"] + " - " + user["name"]

                    button.appendChild(button_span)
                    wrapper.appendChild(name)
                    wrapper.appendChild(button)
                    edit_supervisor_container.appendChild(wrapper)
                    edit_supervisor_container_temp.push(user["username"] + " - " + user["name"])
                }
            });
        });

        users.forEach(use => {
            res.users.forEach(user => {
                if(user["id"] == use){
                    edit_user_temp.push(user)
                    var wrapper = document.createElement("div")
                    var name = document.createElement("div")
                    var button = document.createElement("div")
                    var button_span = document.createElement("span")
                    wrapper.setAttribute("class","i-block rounded user-list alert-success p-1 ps-2 pe-2 mt-1 ms-1")
                    name.setAttribute("class","i-block user-name ft-13")
                    button.setAttribute("class","i-block user-remove ms-2")
                    button_span.setAttribute("class","fa fa-remove")

                    button.addEventListener("click",function(){
                        var text_temp = this.parentNode.children[0].innerText
                        if(edit_user_container_temp.includes(text_temp)){
                            edit_user_container_temp = edit_user_container_temp.filter(value => value !== text_temp)
                        }
                        this.parentNode.remove()
                        edit_group_user.value = ""
                    console.log(edit_user_container_temp)

                    })

                    name.innerText = user["username"] + " - " + user["name"]

                    button.appendChild(button_span)
                    wrapper.appendChild(name)
                    wrapper.appendChild(button)
                    edit_user_container.appendChild(wrapper)
                    edit_user_container_temp.push(user["username"] + " - " + user["name"])
                }
            });
        });
        edit_group_modal.show()
    }

    function loadAccounts(res){
        console.log(res)
        accounts_table.clear().draw();
        res.user.forEach(e => {
            accounts_table.row.add([
                e["id"],
                e["name"],
                e["email"] != "-" ? e["email"] : "",
                e["username"],
                e["privileges"] == "Administrator" ? "<div class=\"text-primary\">"+e["privileges"]+"</div>" : e["privileges"] == "Supervisor" ? "<div class=\"text-success\">"+e["privileges"]+"</div>" : e["privileges"],
                e["id"] != localStorage.getItem("userid") && e["username"] != "703F_administrator"? "<button id=\"edit_account_"+ e["id"] +"\" u-id=\""+ e["id"] +"\" class=\"edit_account_row btn btn-sm btn-secondary\"><i u-id=\""+ e["id"] +"\" class=\"edit_account_row fa fa-edit\"></i></button>" +
                "<button id=\"delete_account_"+ e["id"] +"\" u-id=\""+ e["id"] +"\" class=\"delete_account_row btn btn-sm btn-danger ms-1\"><i u-id=\""+ e["id"] +"\" class=\"delete_account_row fa fa-trash\"></i></button>" : ""
            ]).draw(false)   
        });
        document.querySelector('#accounts_table').addEventListener("click", e=>{
            let tr = "";
            if(e.target.tagName == "I"){
                tr = e.target.parentNode.parentNode.parentNode.children
            }
            if(e.target.tagName == "BUTTON"){
                tr = e.target.parentNode.parentNode.children    
            }
            if(e.target.classList.contains('edit_account_row')) {
                edit_account_title.innerHTML = "<span class=\"fa fa-user\"></span> Edit Account: " + tr[0].innerText
                edit_account_btn.setAttribute("u-id",e.target.getAttribute("u-id"))
                sole.post("../../controllers/administrator/find_account.php",{
                    id: e.target.getAttribute("u-id")
                }).then(res => {
                    edit_account_name.value = res.user[0].name
                    edit_email.value = res.user[0].email != "-" ? res.user[0].email : ""
                    edit_username.value = res.user[0].username
                    edit_password.value = res.user[0].password
                    edit_privilege.value = res.user[0].privileges
                    edit_account_modal.show()
                })
            }
            if(e.target.classList.contains('delete_account_row')) {
                delete_account_name.innerText = tr[0].innerText
                delete_account_btn.setAttribute("u-id",e.target.getAttribute("u-id"))
                delete_account_modal.show()
            }
        })
    }

    function validateResponse(res,func){
        if(res.status){
            if(func == "create_account"){
                add_name.value = ""
                add_email.value = ""
                add_username.value = ""
                add_privilege.value = "User"
                sole.get("../../controllers/administrator/get_accounts.php").then(res => loadAccounts(res))
            }
            if(func == "edit_account"){
                edit_account_modal.hide()
                sole.get("../../controllers/administrator/get_accounts.php").then(res => loadAccounts(res))
            }
            if(func == "delete_account"){
                delete_account_modal.hide()
                sole.get("../../controllers/administrator/get_accounts.php").then(res => loadAccounts(res))
            }
            if(func == "add_group"){
                supervisor_container.innerHTML = ""
                supervisor_container_temp = []
                user_container.innerHTML = ""
                user_container_temp = []
                group_name.value = ""
                group_type.value = "NON-IT"
                group_supervisor.value = ""
                group_user.value = ""
                add_group_modal.hide()
                loadPage()
            }
            if(func == "edit_group"){
                edit_group_supervisor.innerHTML = "<option value=\"\" selected disabled>Select User</option>"
                edit_group_user.innerHTML = "<option value=\"\" selected disabled>Select User</option>"
                edit_supervisor_container.innerHTML = ""
                edit_supervisor_container_temp = []
                edit_user_container.innerHTML = ""
                edit_user_container_temp = []

                edit_user_temp = []
                edit_supervisor_temp = []
                
                edit_group_name.value = ""
                edit_group_supervisor.value = ""
                edit_group_user.value = ""
                edit_group_modal.hide()
                loadPage()
            }
            bs5.toast(res.type,res.message,res.size)
        }else{
            bs5.toast(res.type,res.message,res.size)
        }
    }
}