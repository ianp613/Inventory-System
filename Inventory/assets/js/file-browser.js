var browser_name                        = document.getElementById("browser_name");
var file_folder_container               = document.getElementById("file_folder_container");
var navigation_container                = document.getElementById("navigation_container");
var root_folder                         = document.getElementById("root_folder");
var browser_icon                        = document.getElementById("browser_icon");
var ellipsis_btn                        = document.getElementById("ellipsis_btn");
var ff_select_btn                       = document.getElementById("ff_select_btn");
var ff_new_folder_btn                   = document.getElementById("ff_new_folder_btn");
var ff_logout_btn                       = document.getElementById("ff_logout_btn");
var ff_select_cancel                    = document.getElementById("ff_select_cancel");
var menu_ribbon                         = document.getElementById("menu_ribbon");

var ff_options                          = document.getElementById("ff_options");
var ff_option_copy                      = document.getElementById("ff_option_copy");
var ff_option_move                      = document.getElementById("ff_option_move");
var ff_option_rename                    = document.getElementById("ff_option_rename");
var ff_option_delete                    = document.getElementById("ff_option_delete");
var ff_option_download                  = document.getElementById("ff_option_download");
var ff_rename_input                     = document.getElementById("ff_rename_input");
var ff_rename_cancel                    = document.getElementById("ff_rename_cancel");
var ff_rename_save                      = document.getElementById("ff_rename_save");

var ff_options_copy                     = document.getElementById("ff_options_copy");
var ff_option_copy_cancel               = document.getElementById("ff_option_copy_cancel");
var ff_option_copy_paste                = document.getElementById("ff_option_copy_paste");

var ff_options_move                     = document.getElementById("ff_options_move");
var ff_option_move_cancel               = document.getElementById("ff_option_move_cancel");
var ff_option_move_paste                = document.getElementById("ff_option_move_paste");

var ff_option_copy_paste_clipboard      = document.getElementById("ff_option_copy_paste_clipboard");
var ff_option_copy_paste_spinner        = document.getElementById("ff_option_copy_paste_spinner");

var ff_option_move_paste_clipboard      = document.getElementById("ff_option_move_paste_clipboard");
var ff_option_move_paste_spinner        = document.getElementById("ff_option_move_paste_spinner");

var ff_option_download_download         = document.getElementById("ff_option_download_download");
var ff_option_download_spinner          = document.getElementById("ff_option_download_spinner");

var ff_new_folder_input                 = document.getElementById("ff_new_folder_input");
var ff_new_folder_cancel                = document.getElementById("ff_new_folder_cancel");
var ff_new_folder_create                = document.getElementById("ff_new_folder_create");

var ff_delete_cancel                    = document.getElementById("ff_delete_cancel")
var ff_delete_proceed                   = document.getElementById("ff_delete_proceed")
var item_count                          = document.getElementById("item_count")

var ff_login                            = document.getElementById("ff_login")
var ff_login_card                       = document.getElementById("ff_login_card")
var ff_login_userid                     = document.getElementById("ff_login_userid")
var ff_login_password                   = document.getElementById("ff_login_password")
var ff_login_btn                        = document.getElementById("ff_login_btn")

var ff_user                             = document.getElementById("ff_user")
var ff_user_container                   = document.getElementById("ff_user_container")

var selections                          = [];
var ext                                 = "";
var selections_                         = false;

var source                              = "";
var copy                                = false;
var move                                = false;

const randomNumber                      = Math.floor(Math.random() * 6) + 1;
const favicon                           = document.querySelector("link[rel='shortcut icon']");
favicon.href                            = `../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-${randomNumber}.ico`;
browser_icon.src                        = `../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-${randomNumber}.ico`;

const ellipsis_menu                     = new bootstrap.Modal(document.getElementById('ellipsis_menu'));
const ff_rename                         = new bootstrap.Modal(document.getElementById('ff_rename'),unclose);
const ff_new_folder                     = new bootstrap.Modal(document.getElementById('ff_new_folder'));
const ff_delete                         = new bootstrap.Modal(document.getElementById('ff_delete'),unclose);

const ff_rename_modal                   = document.getElementById('ff_rename');
const ff_new_folder_modal               = document.getElementById('ff_new_folder');

function scanFolder(){
    selections_ = false
    if(localStorage.getItem("folder") == null){
        localStorage.setItem("folder","/")
    }
    sole.post("../../controllers/file-browser/get-files.php",{
        folder : localStorage.getItem("folder")
    }).then(res => {
        browser_name.innerText = res[1]["browser_name"]
        root_folder.innerText = res[1]["root_name"].split(" ").join("_")
        file_folder_container.innerHTML = ""
        navigation_container.innerHTML  = ""

        var folders = localStorage.getItem("folder").split("/")
        folders.shift()
        folders.pop()
        for (let i = 0; i < folders.length; i++) {
            if(folders[i]){
                navigation_container.insertAdjacentHTML("beforeend",
                    `<h6 class="separator mt-1">\\</h6>`+
                    `<a f-index="${i+1}" href="#" class="navigate-folder ${i+1 == folders.length ? "disabled" : "" } btn btn-sm fw-bolder text-${i+1 == folders.length ? "secondary" : "primary" }">${folders[i]}</a>`
                )
            }
        }
        
        ff_options.hidden = true

        if(res[0].length){
            ff_select_btn.hidden = false
            cancelSelection()
        }else{
            ff_select_btn.hidden = true
        }

        ellipsis_btn.hidden = false
        ff_select_cancel.hidden = true

        res[0].forEach(ff => {
            if(ff[1] == "dir"){
                file_folder_container.insertAdjacentHTML("beforeend",
                    `<div fname="${ff[0]}" class="${selections.includes(ff[0]) && localStorage.getItem("folder") == source ? "selected-disabled" : ""} folder folder-parent ff-content d-flex justify-content-between alert-dark pt-2 pb-1 mb-1">`+
                        `<div class="folder folder-subparent d-flex text-left mt-2">`+
                            `<input hidden ff-type="${ff[1]}" ff-name="${ff[0]}"type="checkbox" class="wd-15 me-2 mb-2 ff-select"></input>`+
                            `<span class="folder folder-child fa fa-folder f-20 me-2"></span>`+
                            `<h6 class="folder folder-child">${ff[0]}</h6>`+
                        `</div>`+
                        `<p class="folder folder-subparent btn btn-sm btn-secondary border-light mt-1 mb-1">${ff[2]}</p>`+
                    `</div>`
                )
            }
            if(ff[1] == "file"){
                file_folder_container.insertAdjacentHTML("beforeend",
                    `<div class="${selections.includes(ff[0]) && localStorage.getItem("folder") == source ? "selected-disabled" : ""} ff-content d-flex justify-content-between alert-dark pt-2 pb-1 mb-1">`+
                        `<div class="d-flex text-left mt-2">`+
                            `<input hidden ff-type="${ff[1]}" ff-name="${ff[0]}" type="checkbox" class="wd-15 me-2 mb-2 ff-select"></input>`+
                            // `<span class="fa fa-file f-20 me-2"></span>`+
                            `<img class="wd-30 ht-30" style="margin-top: -5px; margin-left: -5px; margin-right: 3px;" src="../../assets/img/labubu/labubu-file-icons/labubu-file-icon-${randomNumber}.ico">`+
                            `<h6>${ff[0]}</h6>`+
                        `</div>`+
                        `<p class="btn btn-sm btn-secondary border-light mt-1 mb-1">${ff[2]}</p>`+
                    `</div>`
                )
            }
        });
    })
}

root_folder.addEventListener("click", e => {
    localStorage.removeItem("folder")
    scanFolder()
})

file_folder_container.addEventListener("dblclick", e => {
    var folder = "";
    if(e.target.classList.contains("folder")){
        if(e.target.classList.contains("folder-parent")){
            folder = e.target.getAttribute("fname")
        }
        if(e.target.classList.contains("folder-subparent")){
            folder = e.target.parentNode.getAttribute("fname")
        }
        if(e.target.classList.contains("folder-child")){
            folder = e.target.parentNode.parentNode.getAttribute("fname")
        }
        if(folder){
            localStorage.setItem("folder",localStorage.getItem("folder")+folder+"/")
            scanFolder()
        }
    }
})

file_folder_container.addEventListener("click", e => {
    if(e.target.classList.contains("ff-select")){
        var ff_name = e.target.getAttribute("ff-name")
        if(e.target.checked){
            if(!selections.includes(ff_name)){
                selections.push(ff_name)
            }
        }else{
            if(selections.includes(ff_name)){
                selections = selections.filter(selection => selection !== ff_name);
            }
        }
        
        if(selections.length){
            if(selections.length == 1){
                ff_option_copy.classList.remove("ff-option-disabled")
                ff_option_move.classList.remove("ff-option-disabled")
                ff_option_rename.classList.remove("ff-option-disabled")
                ff_option_delete.classList.remove("ff-option-disabled")
                ff_option_download.classList.remove("ff-option-disabled")
            }else{
                ff_option_rename.classList.add("ff-option-disabled")
            }
        }else{
            ff_option_copy.classList.add("ff-option-disabled")
            ff_option_move.classList.add("ff-option-disabled")
            ff_option_rename.classList.add("ff-option-disabled")
            ff_option_delete.classList.add("ff-option-disabled")
            ff_option_download.classList.add("ff-option-disabled")
        }
    }
})

navigation_container.addEventListener("click", e => {
    if(e.target.classList.contains("navigate-folder")){
        var folders = localStorage.getItem("folder").split("/")
        folders.shift()
        folders.pop()

        var index = parseInt(e.target.getAttribute("f-index"))

        folders = folders.splice(0,index)
        folders.unshift("")
        folders.push("")
        localStorage.setItem("folder",folders.join("/"))
        scanFolder()
    }
})

ellipsis_btn.addEventListener("click", e => {
    ellipsis_menu.show()
})

ff_select_btn.addEventListener("click", e => {
    selections_ = true
    var ff_content = document.getElementsByClassName("ff-select");
    for (let i = 0; i < ff_content.length; i++) {
        ff_content[i].hidden = false
    }
    ellipsis_btn.hidden = true
    ff_select_cancel.hidden = false
    ff_options.hidden = false
    ellipsis_menu.hide()
})

ff_new_folder_btn.addEventListener("click", e => {
    ellipsis_menu.hide()
    ff_new_folder.show()
    ff_new_folder_input.value = ""
})

ff_select_cancel.addEventListener("click", e => {
    selections_ = false
    var ff_content = document.getElementsByClassName("ff-select");
    for (let i = 0; i < ff_content.length; i++) {
        ff_content[i].hidden = true
    }
    ellipsis_btn.hidden = false
    ff_select_cancel.hidden = true
    ff_options.hidden = true
    cancelSelection()
})

ff_option_rename.addEventListener("click", e => {
    if(selections.length == 1){
        var selection_temp = selections[0].split(".")
        if(selection_temp.length > 1){
            ext = selection_temp.pop()
        }else{
            ext = ""
        }
        
        ff_rename_input.value = selection_temp.join(".")
        ff_rename.show()
    }else{
        bs5.toast("error","Something went wrong, please try again.","lg",false)
    }
})

ff_option_delete.addEventListener("click", e => {
    item_count.innerText = selections.length > 1 ? "items" : "item";
    ellipsis_menu.hide()
    ff_delete.show()
})

ff_option_download.addEventListener("click", e => {
    ff_option_download_download.hidden = true
    ff_option_download_spinner.hidden = false
    ff_option_download.classList.add("ff-option-disabled")
    fetch("../../controllers/file-browser/download-files.php", {
        method: "POST",
        body: JSON.stringify({
            folder: localStorage.getItem("folder"),
            targets: selections
        })
    })
    .then(res => res.blob())
    .then(blob => {
        ff_option_download_download.hidden = false
        ff_option_download_spinner.hidden = true
        ff_option_download.classList.remove("ff-option-disabled")
        cancelSelection()
        scanFolder()
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "Wifi Files.zip";
        a.click();

        URL.revokeObjectURL(url);
    });
})

ff_option_copy.addEventListener("click", e => {
    source = localStorage.getItem("folder")
    scanFolder()
    copy = true
    ff_options_copy.hidden = false
    ff_options.hidden = true
})

ff_option_move.addEventListener("click", e => {
    source = localStorage.getItem("folder")
    scanFolder()
    move = true
    ff_options_move.hidden = false
    ff_options.hidden = true
})

ff_option_copy_cancel.addEventListener("click", e => {
    copy = false
    ff_options_copy.hidden = true
    cancelSelection()
    scanFolder()
})

ff_option_move_cancel.addEventListener("click", e => {
    move = false
    ff_options_move.hidden = true
    cancelSelection()
    scanFolder()
})

ff_option_copy_paste.addEventListener("click", e => {
    ff_option_copy_paste.classList.add("ff-option-disabled")
    ff_option_copy_cancel.classList.add("ff-option-disabled")
    ff_option_copy_paste_clipboard.hidden = true
    ff_option_copy_paste_spinner.hidden = false
    sole.post("../../controllers/file-browser/copy-files.php",{
        source : source,
        destination : localStorage.getItem("folder"),
        targets : selections,
    }).then(res => {
        if(res.status){
            copy = false
            ff_options_copy.hidden = true
            ff_option_copy_paste_clipboard.hidden = false
            ff_option_copy_paste_spinner.hidden = true
            ff_option_copy_paste.classList.remove("ff-option-disabled")
            ff_option_copy_cancel.classList.remove("ff-option-disabled")
            cancelSelection()
            scanFolder()
        }
    })
})

ff_option_move_paste.addEventListener("click", e => {
    if(source == localStorage.getItem("folder")){
        ff_option_move_cancel.click()
        return
    }
    ff_option_move_paste.classList.add("ff-option-disabled")
    ff_option_move_cancel.classList.add("ff-option-disabled")
    ff_option_move_paste_clipboard.hidden = true
    ff_option_move_paste_spinner.hidden = false
    sole.post("../../controllers/file-browser/move-files.php",{
        source : source,
        destination : localStorage.getItem("folder"),
        targets : selections,
    }).then(res => {
        if(res.status){
            move = false
            ff_options_move.hidden = true
            ff_option_move_paste_clipboard.hidden = false
            ff_option_move_paste_spinner.hidden = true
            ff_option_move_paste.classList.remove("ff-option-disabled")
            ff_option_move_cancel.classList.remove("ff-option-disabled")
            cancelSelection()
            scanFolder()
        }
    })
})

const bannedRegex = /[\/\\<>*:"?|]/g;
ff_rename_input.addEventListener("input", function () {
    this.value = this.value.replace(bannedRegex, '');
});

ff_new_folder_input.addEventListener("input", function () {
    this.value = this.value.replace(bannedRegex, '');
});

ff_rename_modal.addEventListener('shown.bs.modal', function () {
    ff_rename_input.focus()
})

ff_new_folder_modal.addEventListener('shown.bs.modal', function () {
    ff_new_folder_input.focus()
})

ff_rename_cancel.addEventListener("click", e => {
    ff_select_cancel.click()
    cancelSelection()
    ff_rename_input.value = ""
    ff_rename.hide()
})

ff_new_folder_cancel.addEventListener("click", e => {
    ff_new_folder_input.value = ""
    ff_new_folder.hide()
})

ff_delete_cancel.addEventListener("click", e => {
    ff_select_cancel.click()
    cancelSelection()
    ff_delete.hide()
})

ff_rename_save.addEventListener("click", e => {
    var ff_content = document.getElementsByClassName("ff-select");
    var ff_type = "dir";
    for (let i = 0; i < ff_content.length; i++) {
        if(ff_content[i].getAttribute("ff-name") == selections[0]){
            ff_type = ff_content[i].getAttribute("ff-type")
        }
    }
    
    ff_rename_input.value = ff_rename_input.value.replace(/\.+$/, "");

    if(selections[0] == ff_rename_input.value + (ff_type == "dir" ? "" : ".") + ext){
        ff_rename.hide()
        scanFolder()
        return
    }

    sole.post("../../controllers/file-browser/rename-files.php",{
        folder : localStorage.getItem("folder"),
        type : ff_type,
        old : selections[0],
        new : ff_rename_input.value + (ff_type == "dir" ? "" : ".") + ext
    }).then(res => {
        if(res.status){
            ff_rename.hide()
            scanFolder()
        }else{
            bs5.toast(res.type,res.message)
        }
    })
})

ff_new_folder_create.addEventListener("click", e => {
    if(!ff_new_folder_input.value){
        ff_new_folder.hide()
        return
    }
    ff_new_folder_input.value = ff_new_folder_input.value.replace(/\.+$/, "");
    sole.post("../../controllers/file-browser/new-folder.php",{
        folder : localStorage.getItem("folder"),
        name : ff_new_folder_input.value
    }).then(res => {
        if(res.status){
            ff_new_folder.hide()
            ff_new_folder_input.value = ""
            scanFolder()
        }else{
            bs5.toast(res.type,res.message)
        }
    })
})

ff_delete_proceed.addEventListener("click", e => {
    sole.post("../../controllers/file-browser/delete-files.php",{
        folder : localStorage.getItem("folder"),
        targets : selections
    }).then(res => {
        if(res.status){
            ff_delete.hide()
            scanFolder()
        }else{
            bs5.toast(res.type,res.message)
        }
    })
})

function cancelSelection(){
    if(!copy && !move){
        selections = []
    }
    var ff_content = document.getElementsByClassName("ff-select");
    for (let i = 0; i < ff_content.length; i++) {
        ff_content[i].checked = false
    }
    ff_option_copy.classList.add("ff-option-disabled")
    ff_option_move.classList.add("ff-option-disabled")
    ff_option_rename.classList.add("ff-option-disabled")
    ff_option_delete.classList.add("ff-option-disabled")
    ff_option_download.classList.add("ff-option-disabled")
}

function ellipsisPreventDefaultContextMenu(event){
    event.preventDefault();
    if(!selections_){
        ellipsis_btn.click();
    }
}

function loginContextMenu(){
    document.addEventListener('contextmenu', ellipsisPreventDefaultContextMenu)
}

function logoutContextMenu(){
    document.removeEventListener('contextmenu', ellipsisPreventDefaultContextMenu);
}

ff_login_btn.addEventListener("click", e => {
    if(!ff_login_userid.value || !ff_login_password.value){
        bs5.toast("warning","Please input User ID and Password.")
        ff_login_userid.focus()
        return
    }

    sole.post("../../controllers/file-browser/login.php", {
        userid : ff_login_userid.value,
        password : ff_login_password.value
    }).then(res => {
        localStorage.setItem("ff_user",res.user[0]["name"])
        if(res.status){
            bs5.toast(res.type,res.message + " " + res.user[0]["name"])
            ff_login_userid.value = ""
            ff_login_password.value = ""
            ff_login_card.hidden = true
            ff_login.classList.remove("ff-login")
            checkAuthentication()
        }else{
            bs5.toast(res.type,res.message)
        }
    })
})

ff_logout_btn.addEventListener("click", e => {
    ff_select_cancel.click()
    file_folder_container.innerHTML = ""
    navigation_container.innerHTML  = ""
    ellipsis_menu.hide()
    sole.get("../../controllers/file-browser/logout.php").then(res => {
        checkAuthentication()
    })
})

function checkAuthentication(){
    if(localStorage.getItem("ff_user") !== null){
        ff_user.innerText = "User: " + localStorage.getItem("ff_user")
    }
    sole.get("../../controllers/file-browser/authenticate.php").then(res => {
        if(res){
            ff_user_container.hidden = false
            scanFolder()
            loginContextMenu()
        }else{
            ff_login_card.hidden = false
            ff_login.classList.add("ff-login")
            ff_user_container.hidden = true
            logoutContextMenu()
        }
    })
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        ff_select_cancel.click()
        ff_rename_cancel.click()
        ellipsis_menu.hide()
    }
});

setTimeout(() => {
    ff_login_userid.focus()
}, 50);

checkAuthentication()