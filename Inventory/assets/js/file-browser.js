var file_folder_container   = document.getElementById("file_folder_container");
var navigation_container    = document.getElementById("navigation_container");
var root_folder             = document.getElementById("root_folder");
var browser_icon            = document.getElementById("browser_icon");

const randomNumber          = Math.floor(Math.random() * 6) + 1;
const favicon               = document.querySelector("link[rel='shortcut icon']");
favicon.href                = `../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-${randomNumber}.ico`;
browser_icon.src            = `../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-${randomNumber}.ico`;



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

        res.forEach(ff => {
            if(ff[1] == "dir"){
                file_folder_container.insertAdjacentHTML("beforeend",
                    `<div fname="${ff[0]}" class="folder folder-parent ff-content d-flex justify-content-between alert-dark pt-2 pb-1 mb-1">`+
                        `<div class="folder folder-subparent d-flex text-left mt-2">`+
                            `<input  ff-name="${ff[0]}"type="checkbox" class="me-2 mb-2 ff-select"></input>`+
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
                            `<input ff-name="${ff[0]}" type="checkbox" class="me-2 mb-2 ff-select"></input>`+
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
    }
    if(folder){
        localStorage.setItem("folder",localStorage.getItem("folder")+folder+"/")
        scanFolder()
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

document.addEventListener('contextmenu', event => {
    event.preventDefault();
});