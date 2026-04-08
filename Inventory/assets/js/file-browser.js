var file_folder_container   = document.getElementById("file_folder_container");
var navigation_container    = document.getElementById("navigation_container");
var root_folder             = document.getElementById("root_folder");
var browser_icon            = document.getElementById("browser_icon");
var ellipsis_btn            = document.getElementById("ellipsis_btn");
var ff_select_btn           = document.getElementById("ff_select_btn");
var ff_select_cancel        = document.getElementById("ff_select_cancel");
var menu_ribbon             = document.getElementById("menu_ribbon");

var ff_options              = document.getElementById("ff_options");
var ff_option_copy          = document.getElementById("ff_option_copy");
var ff_option_move          = document.getElementById("ff_option_move");
var ff_option_rename        = document.getElementById("ff_option_rename");
var ff_option_delete        = document.getElementById("ff_option_delete");
var ff_rename_input         = document.getElementById("ff_rename_input");
var ff_rename_cancel        = document.getElementById("ff_rename_cancel");
var ff_rename_save          = document.getElementById("ff_rename_save");

var selections              = [];
var ext                     = "";

const randomNumber          = Math.floor(Math.random() * 6) + 1;
const favicon               = document.querySelector("link[rel='shortcut icon']");
favicon.href                = `../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-${randomNumber}.ico`;
browser_icon.src            = `../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-${randomNumber}.ico`;

const ellipsis_menu         = new bootstrap.Modal(document.getElementById('ellipsis_menu'));
const ff_rename             = new bootstrap.Modal(document.getElementById('ff_rename'),unclose);

const ff_rename_modal       = document.getElementById('ff_rename');


scanFolder()
function scanFolder(){
    if(localStorage.getItem("folder") == null){
        localStorage.setItem("folder","/")
    }
    sole.post("../../controllers/file-browser/get-files.php",{
        folder : localStorage.getItem("folder")
    }).then(res => {
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

        if(res.length){
            ff_select_btn.hidden = false
            cancelSelection()
        }else{
            ff_select_btn.hidden = true
        }

        ellipsis_btn.hidden = false
        ff_select_cancel.hidden = true

        res.forEach(ff => {
            if(ff[1] == "dir"){
                file_folder_container.insertAdjacentHTML("beforeend",
                    `<div fname="${ff[0]}" class="folder folder-parent ff-content d-flex justify-content-between alert-dark pt-2 pb-1 mb-1">`+
                        `<div class="folder folder-subparent d-flex text-left mt-2">`+
                            `<input hidden ff-name="${ff[0]}"type="checkbox" class="wd-15 me-2 mb-2 ff-select"></input>`+
                            `<span class="folder folder-child fa fa-folder f-20 me-2"></span>`+
                            `<h6 class="folder folder-child">${ff[0]}</h6>`+
                        `</div>`+
                        `<p class="folder folder-subparent btn btn-sm btn-secondary border-light mt-1 mb-1">${ff[2]}</p>`+
                    `</div>`
                )
            }
            if(ff[1] == "file"){
                file_folder_container.insertAdjacentHTML("beforeend",
                    `<div class="ff-content d-flex justify-content-between alert-dark pt-2 pb-1 mb-1">`+
                        `<div class="d-flex text-left mt-2">`+
                            `<input hidden ff-name="${ff[0]}" type="checkbox" class="wd-15 me-2 mb-2 ff-select"></input>`+
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
            }else{
                ff_option_rename.classList.add("ff-option-disabled")
            }
        }else{
            ff_option_copy.classList.add("ff-option-disabled")
            ff_option_move.classList.add("ff-option-disabled")
            ff_option_rename.classList.add("ff-option-disabled")
            ff_option_delete.classList.add("ff-option-disabled")
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
    var ff_content = document.getElementsByClassName("ff-select");
    for (let i = 0; i < ff_content.length; i++) {
        ff_content[i].hidden = false
    }
    ellipsis_btn.hidden = true
    ff_select_cancel.hidden = false
    ff_options.hidden = false
    ellipsis_menu.hide()
})

ff_select_cancel.addEventListener("click", e => {
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

const safeRegex = /[^a-zA-Z0-9._-]/g;

ff_rename_input.addEventListener("input", function () {
    this.value = this.value.replace(safeRegex, '');
});

ff_rename_modal.addEventListener('shown.bs.modal', function () {
    ff_rename_input.focus()
})

ff_rename_cancel.addEventListener("click", e => {
    ff_select_cancel.click()
    cancelSelection()
    ff_rename_input.value = ""
    ff_rename.hide()
})

ff_rename_save.addEventListener("click", e => {
    sole.post("../../controllers/file-browser/rename-files.php",{
        folder : localStorage.getItem("folder"),
        old : selections[0],
        new : ff_rename_input.value + "." + ext
    }).then(res => {
        if(res.status){
            ff_rename.hide()
            scanFolder()
        }else{
            bs5.toast(res.type,res.message)
        }
    })
})

function cancelSelection(){
    selections = []
    var ff_content = document.getElementsByClassName("ff-select");
    for (let i = 0; i < ff_content.length; i++) {
        ff_content[i].checked = false
    }
    ff_option_copy.classList.add("ff-option-disabled")
    ff_option_move.classList.add("ff-option-disabled")
    ff_option_rename.classList.add("ff-option-disabled")
    ff_option_delete.classList.add("ff-option-disabled")
}

document.addEventListener('contextmenu', event => {
    // event.preventDefault();
});