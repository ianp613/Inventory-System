var activity_log = document.getElementById("activity_log");

activity_log.addEventListener("click",function(){
    window.location.href = "inventory.php?loc=logs"
})

if(document.getElementById("logs")){
    let logTable = new DataTable('#log_table',{
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
                className: 'dt-left f-13', 
                targets: '_all' 
            },
            { 
                className: 'dt-right', 
                target: '2' 
            }
        ],
        autoWidth: false,
        language: {
            sLengthMenu: "Show _MENU_entries",
            search : localStorage.getItem("privileges") == "Administrator" ? "<button id=\"clear_log_toggle\" data-bs-toggle=\"modal\" data-bs-target=\"#clear_log\" class=\"btn btn-sm btn-danger me-3\"><span class=\"fa fa-trash\"></span> Clear Logs</button> Search: " : "Search: "
        },
        searching: true,
        paging: false,
        info: false,
        emptyTable: false
    });

    // if(JSON.parse(localStorage.getItem("g_member"))){
    //     document.getElementById("clear_log_toggle").remove()
    // }
    
    var select_log = document.getElementById("select_log")

    loadLogs()

    if(localStorage.getItem("privileges") == "Administrator"){
        loadUsers()
        var clear_log_btn = document.getElementById("clear_log_btn")
        var clear_log_name = document.getElementById("clear_log_name")
        var clear_log_toggle = document.getElementById("clear_log_toggle")
        if(!JSON.parse(localStorage.getItem("g_member"))){
            select_log.addEventListener("change",function(){
                loadLogs(select_log.value)
            })
            clear_log_toggle.addEventListener("click",function(){
                if(select_log.value == "All"){
                    clear_log_name.innerHTML = "for all users"
                }else if(select_log.value == localStorage.getItem("userid")){
                    clear_log_name.innerHTML = "for your account"
                }else{
                    clear_log_name.innerHTML = "for user <b>\""+select_log.options[select_log.selectedIndex].text+"\"</b>"
                }
                clear_log_btn.setAttribute("uid",select_log.value)
            })   
        }
        const clear_log_modal = new bootstrap.Modal(document.getElementById('clear_log'),unclose);
        clear_log_btn.addEventListener("click",function(){
            sole.post("../../controllers/logs/delete_logs.php",{
                uid: this.getAttribute("uid")
            }).then(res => {
                bs5.toast("info","Logs has been cleared.")
                loadLogs(this.getAttribute("uid"))
            })
        })
    }
    
    function loadLogs(logs = "All"){
        sole.post("../../controllers/logs/get_log.php",{
            logs: logs
        }).then(res => {
            logTable.clear().draw();
            res.logs.forEach(e => {
                logTable.row.add([
                    e["id"],
                    replaceName(e["uid"],e["log"]),
                    e["created_at"],
                    localStorage.getItem("privileges") == "Administrator" ? "<button id=\"delete_log_"+ e["id"] +"\" l-id=\""+ e["id"] +"\" class=\"delete_log_row btn btn-sm btn-danger ms-1\"><i l-id=\""+ e["id"] +"\" class=\"delete_log_row fa fa-trash\"></i></button>" : ""
                ]).draw(false)   
            });
            document.querySelector('#log_table').addEventListener("click", e=>{
                let tr = "";
                if(e.target.tagName == "I"){
                    tr = e.target.parentNode.parentNode.parentNode.children
                }
                if(e.target.tagName == "BUTTON"){
                    tr = e.target.parentNode.parentNode.children    
                }
                if(e.target.classList.contains('delete_log_row')) {
                    sole.post("../../controllers/logs/delete_log.php",{
                        id: e.target.getAttribute("l-id")
                    }).then(res => bs5.toast(res.type,res.message))
                    loadLogs(select_log.value)
                }
            })
        })
    }

    function replaceName(id,log){
        id == localStorage.getItem("userid") ? log = log.replace(localStorage.getItem("yourname"), 'You') : null;
        id == localStorage.getItem("userid") ? log = log.replace('has', 'have') : null;
        return log
    }

    function loadUsers(){
        if(!JSON.parse(localStorage.getItem("g_member"))){
            sole.get("../../controllers/logs/get_users.php")
            .then(res => {
                res.forEach(e => {
                    var op = document.createElement("option")
                    op.value = e["id"]
                    op.innerText = e["id"] == localStorage.getItem("userid") ? "Your logs" : e["name"]
                    e["id"] == localStorage.getItem("userid") ? select_log.appendChild(op) : null
                });
                res.forEach(e => {
                    var op = document.createElement("option")
                    op.value = e["id"]
                    op.innerText = e["id"] == localStorage.getItem("userid") ? "Your logs" : e["name"]
                    e["id"] != localStorage.getItem("userid") ? select_log.appendChild(op) : null
                });
            })    
        }
    }
}









// NEED CHECK ID if LOG DIV EXIST TO USE