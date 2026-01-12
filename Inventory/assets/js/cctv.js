if(document.getElementById("cctv")){
    localStorage.setItem("cameraTitle",null)
    localStorage.setItem("log_delete","")
    let cameraTable = new DataTable('#camera_table',{
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
           search: "<button id=\"add_camera_btn\" class=\"btn btn-sm btn-dark me-3\"><span class=\"fa fa-plus\"></span> Add Camera</button> Search: "
        }
    });

    let cameraListTable = new DataTable('#camera_list_table',{
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        columnDefs: [
            { 
                className: 'dt-left', 
                targets: '_all' 
            },
            { 
                className: 'dt-center', 
                target: 4
            },
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "Search: ",
        },
        searching: true,
        paging: false,
        info: false,
        emptyTable: false
    });

    const canvas = document.getElementById("cctvCanvas");
    const ctx = canvas.getContext("2d");
    const add_cctv_map_modal = new bootstrap.Modal(document.getElementById('add_cctv_map'),unclose);
    const edit_cctv_map_modal = new bootstrap.Modal(document.getElementById('edit_cctv_map'),unclose);
    const delete_cctv_map_modal = new bootstrap.Modal(document.getElementById('delete_cctv_map'),unclose); 
    const manage_camera_modal = new bootstrap.Modal(document.getElementById('manage_camera'),unclose);
    const camera_list_modal = new bootstrap.Modal(document.getElementById('camera_list'),unclose);

    var save_map_btn = document.getElementById("save_map_btn")
    var add_site_btn = document.getElementById("add_site_btn")
    var map_location = document.getElementById("map_location")
    var floorplan = document.getElementById("floorplan")
    var map_remarks = document.getElementById("map_remarks")
    var manage_camera_title = document.getElementById("manage_camera_title")

    var edit_site_btn = document.getElementById("edit_site_btn")
    var edit_map_location = document.getElementById("edit_map_location")
    var edit_floorplan = document.getElementById("edit_floorplan")
    var edit_map_remarks = document.getElementById("edit_map_remarks")

    var delete_cctv_map_btn = document.getElementById("delete_cctv_map_btn");
    var delete_cctv_map_little = document.getElementById("delete_cctv_map_little");
    var delete_cctv_map_name = document.getElementById("delete_cctv_map_name");
    var delete_cctv_map_btn_proceed = document.getElementById("delete_cctv_map_btn_proceed");

    var manage_camera_btn = document.getElementById("manage_camera_btn")

    var add_camera_btn = document.getElementById("add_camera_btn")
    var camera_form_control = document.getElementById("camera_form_control")
    var cancel_camera_form_btn = document.getElementById("cancel_camera_form_btn")
    var save_camera_form_btn = document.getElementById("save_camera_form_btn")
    var update_camera_form_btn = document.getElementById("update_camera_form_btn")
    var camera_menu = document.getElementById("camera_menu")
    var camera_form = document.getElementById("camera_form")
    var camera_subtype_form = document.getElementById("camera_subtype_form")

    var camera_size = document.getElementById("camera_size")
    var save_size_btn = document.getElementById("save_size_btn")

    var camera_id = document.getElementById("camera_id")
    var camera_type = document.getElementById("camera_type")
    var camera_subtype = document.getElementById("camera_subtype")
    var camera_ip_address = document.getElementById("camera_ip_address")
    var camera_port_no = document.getElementById("camera_port_no")
    var camera_username = document.getElementById("camera_username")
    var camera_password = document.getElementById("camera_password")
    var camera_angle = document.getElementById("camera_angle")
    var camera_preview = document.getElementById("camera_preview")
    var camera_location = document.getElementById("camera_location")
    var camera_brand = document.getElementById("camera_brand")
    var camera_model_no = document.getElementById("camera_model_no")
    var camera_barcode = document.getElementById("camera_barcode")
    var camera_status = document.getElementById("camera_status")
    var camera_remarks = document.getElementById("camera_remarks")

    var cameraList = null;
    var camera_list_title = document.getElementById("camera_list_title")

    sole.get("../../controllers/cctv/get_site.php").then(res => loadSite(res))

    add_site_btn.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            if(map_location.value){
                if(floorplan.files.length > 0){
                    const formData = new FormData();
                    formData.append("uid",localStorage.getItem("userid"))
                    formData.append("file",floorplan.files[0])
                    formData.append("map_location",map_location.value)
                    formData.append("map_remarks",map_remarks.value)
                    sole.file("../../controllers/cctv/add_site.php",formData)
                    .then(res => {
                        add_cctv_map_modal.hide()
                        bs5.toast(res.type,res.message,res.size)
                        map_location.value = ""
                        floorplan.value = ""
                        map_remarks.value = ""
                        loadSite(res)
                    })    
                }else{
                    bs5.toast("warning","Please select floor plan.")
                }
            }else{
                bs5.toast("warning","Please input map location.")
            }    
        }else{
            bs5.toast("info","Please operate as group member.")
        }
        

        
    })

    edit_site_btn.addEventListener("click",function(){
        !edit_map_location.value ? bs5.toast("warning","Please input map location.") : null
        // edit_floorplan.files.length > 0 ? null : bs5.toast("warning","Please select floor plan.")

        const formData = new FormData();
        formData.append("id",this.getAttribute("lid"))
        formData.append("file",edit_floorplan.files[0])
        formData.append("map_location",edit_map_location.value)
        formData.append("map_remarks",edit_map_remarks.value)
        sole.file("../../controllers/cctv/edit_site.php",formData)

        .then(res => {
            edit_cctv_map_modal.hide()
            bs5.toast(res.type,res.message,res.size)
            if(cctv_dropdown_toggle.getAttribute("lid") == this.getAttribute("lid")){
                cctv_dropdown_toggle.innerText = edit_map_location.value 
                manage_camera_title.innerHTML = "<span class=\"fa fa-video-camera\"></span> " + edit_map_location.value 
                camera_list_title.innerHTML = "<span class=\"fa fa-video-camera\"></span> <b>Camera List:</b> " + edit_map_location.value 
            }
            edit_map_location.value = ""
            edit_floorplan.value = ""
            edit_map_remarks.value = ""
            loadSite(res)
            if(cctv_dropdown_toggle.innerText != "-- Select Map --"){
                loadMAP_CAMERA()
                saveCanvas()    
            }
        })
    })

    delete_cctv_map_btn.addEventListener("click",function(){
        edit_cctv_map_modal.hide();
        delete_cctv_map_modal.show();
    })

    delete_cctv_map_btn_proceed.addEventListener("click",function(){
        sole.post("../../controllers/cctv/delete_site.php",{
            id: this.getAttribute("lid")
        }).then(res => {
            bs5.toast(res.type,res.message,res.size)
            if(cctv_dropdown_toggle.getAttribute("lid") == this.getAttribute("lid")){
                cctv_dropdown_toggle.innerText = "-- Select Map --"
            }
            ctx.clearRect(0, 0, 0, 0);
            canvas.setAttribute("hidden","true")
            save_map_btn.setAttribute("hidden","true")
            loadSite(res)
        })
    })

    function loadSite(res){
        console.log(res)
        cctv_dropdown.innerHTML = ""
        res.cctvs.forEach(cctv => {
            cctv_dropdown.innerHTML += "<li><a href=\"#\" class=\"dropdown-item\" id=\""+ cctv["id"] +"\" size=\""+ cctv["camera_size"] +"\">"+ cctv["map_location"] +"</a></li>"
        });
    }

    // SELECT SITE
    cctv_dropdown.addEventListener("click", e=>{
        if(e.target.classList.contains("dropdown-item")){
            localStorage.setItem("cameraTitle",e.target.innerText)
            manage_camera_title.innerHTML = "<span class=\"fa fa-video-camera\"></span> " + e.target.innerText
            camera_list_title.innerHTML = "<span class=\"fa fa-video-camera\"></span> <b>Camera List:</b> " + e.target.innerText
            manage_camera_title.setAttribute("s-id",e.target.getAttribute("id"))
            cctv_dropdown_toggle.innerText = e.target.innerText
            cctv_dropdown_toggle.setAttribute("lid",e.target.getAttribute("id"))
            camera_size.value = e.target.getAttribute("size")
            // localStorage.setItem("selected_cctv", e.target.innerText);
            // localStorage.setItem("selected_cctv_id", e.target.getAttribute("id"));
            save_map_btn.removeAttribute("hidden")
            loadMAP_CAMERA()
            saveCanvas()
        }
    })

    cctv_dropdown.addEventListener("contextmenu",function(e){
        if(JSON.parse(localStorage.getItem("g_member"))){
            if(e.target.classList.contains("dropdown-item")){
                sole.post("../../controllers/cctv/find_site.php",{
                    id: e.target.getAttribute("id")
                }).then(res => {
                    edit_map_location.value = res.site[0].map_location
                    edit_map_remarks.value = res.site[0].remarks != "-" ? res.site[0].remarks : null
                    edit_cctv_map_modal.show()
                    delete_cctv_map_name.innerText = e.target.innerText;
                    delete_cctv_map_btn_proceed.setAttribute("lid",e.target.getAttribute("id"))
                    edit_site_btn.setAttribute("lid",e.target.getAttribute("id"))
                })
            }    
        }else{
            bs5.toast("info","Please operate as group member.")
        }
        
    })

    manage_camera_btn.addEventListener("click",function(){
        if(JSON.parse(localStorage.getItem("g_member"))){
            if(cctv_dropdown_toggle.innerText == "-- Select Map --"){
                bs5.toast("warning","Please select map first.")
            }else{
                manage_camera_modal.show()
            }    
        }else{
            bs5.toast("info","Please operate as group member.")
        }
        
    })

    function loadCCTVList(res){
        sole.post("../../controllers/cctv/get_camera.php",{
            lid : res.cctv[0]["id"]
        }).then(res => {
            cameraList = res
            cameraTable.clear().draw();
            res.camera.forEach(e => {
                cameraTable.row.add([
                    e["id"],
                    e["camera_id"],
                    e["camera_type"] != "-" ? e["camera_type"] : "",
                    e["camera_subtype"] != "-" ? e["camera_subtype"] : "",
                    "<button id=\"edit_camera"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"edit_camera_row btn btn-sm btn-secondary\"><i c-id=\""+ e["id"] +"\" class=\"edit_camera_row fa fa-edit\"></i></button>" +
                    "<button id=\"delete_camera_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"delete_camera_row btn btn-sm btn-danger ms-1\"><i c-id=\""+ e["id"] +"\" class=\"delete_camera_row fa fa-trash\"></i></button>" 
                ]).draw(false)   
            });
            document.querySelector('#camera_table').addEventListener("click", e=>{
                let tr = "";
                if(e.target.tagName == "I"){
                    tr = e.target.parentNode.parentNode.parentNode.children
                }
                if(e.target.tagName == "BUTTON"){
                    tr = e.target.parentNode.parentNode.children    
                }
                if(e.target.classList.contains('delete_camera_row')){
                    if(localStorage.getItem("log_delete") != tr[0].innerText){
                        localStorage.setItem("log_delete",tr[0].innerText)
                        if(confirm("You're going to delete "+tr[0].innerText+". This can't be undone, do you wish to proceed?")){
                            sole.post("../../controllers/cctv/delete_camera.php",{
                                id: e.target.getAttribute("c-id")
                            }).then(res => {
                                if(res.status){
                                    alert(res.message)
                                    loadMAP_CAMERA()
                                    saveCanvas()
                                }
                            })
                        }
                    }
                }
                if(e.target.classList.contains('edit_camera_row')){

                        sole.post("../../controllers/cctv/get_current_camera.php",{
                            id: e.target.getAttribute("c-id")
                        }).then(res => {
                            if(res.status){
                                update_camera_form_btn.setAttribute("c-id",e.target.getAttribute("c-id"))
                                camera_menu.setAttribute("hidden","")
                                camera_form.removeAttribute("hidden")
                                camera_form_control.removeAttribute("hidden")
                                update_camera_form_btn.removeAttribute("hidden")
                                save_camera_form_btn.setAttribute("hidden","")
                                camera_id.value = res.camera[0].camera_id
                                camera_type.value = res.camera[0].camera_type
                                camera_subtype.value = res.camera[0].camera_subtype
                                camera_ip_address.value = res.camera[0].camera_ip_address != "-" ? res.camera[0].camera_ip_address : null
                                camera_port_no.value = res.camera[0].camera_port_no != "-" ? res.camera[0].camera_port_no : null
                                camera_username.value = res.camera[0].camera_username != "-" ? res.camera[0].camera_username : null
                                camera_password.value = res.camera[0].camera_password != "-" ? res.camera[0].camera_password : null
                                camera_angle.value = res.camera[0].camera_angle
                                camera_preview.setAttribute("style","transform: rotate("+res.camera[0].camera_angle+"deg);")
                                camera_location.value = res.camera[0].camera_location != "-" ? res.camera[0].camera_location : null
                                camera_brand.value = res.camera[0].camera_brand != "-" ? res.camera[0].camera_brand : null
                                camera_model_no.value = res.camera[0].camera_model_no != "-" ? res.camera[0].camera_model_no : null
                                camera_barcode.value = res.camera[0].camera_barcode != "-" ? res.camera[0].camera_barcode : null
                                camera_status.value = res.camera[0].camera_status != "-" ? res.camera[0].camera_status : null
                                camera_remarks.value = res.camera[0].camera_remarks != "-" ? res.camera[0].camera_remarks : null
                                res.camera[0].camera_subtype != "Coaxial Camera" && res.camera[0].camera_subtype != "-" ? camera_subtype_form.removeAttribute("hidden") : camera_subtype_form.setAttribute("hidden","")
                            }
                        })
                    
                }
            })


            cameraListTable.clear().draw();
            res.camera.forEach(e => {
                // if(e.cx == "-" && e.cy == "-"){
                    cameraListTable.row.add([
                        "<img id=\""+e["id"]+"\" style=\"transform: rotate("+e["camera_angle"]+"deg);\" src=\"../../assets/img/camera/camera.png\" alt=\"camera_preview\" width=\"25px;\" height=\"25px\">",
                        e["camera_id"],
                        e["camera_type"] != "-" ? e["camera_type"] : "",
                        e["camera_subtype"] != "-" ? e["camera_subtype"] : "",
                        e.cx == "-" && e.cy == "-" ? "<button id=\"select_camera_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"select_camera_row btn btn-sm btn-primary mr-3 \"><i c-id=\""+ e["id"] +"\" class=\"select_camera_row fa fa-check\"></i></button>" : "<button id=\"remove_camera_"+ e["id"] +"\" c-id=\""+ e["id"] +"\" class=\"remove_camera_row btn btn-sm btn-danger mr-3 \"><i c-id=\""+ e["id"] +"\" class=\"remove_camera_row fa fa-ban\"></i></button>",
                    ]).draw(false)  
                // }
            });

            document.querySelector('#camera_list').addEventListener("click", e=>{
                if(e.target.classList.contains('remove_camera_row')){
                    sole.post("../../controllers/cctv/assign_camera.php",{
                        id: e.target.getAttribute("c-id"),
                        type: "unassign"
                    }).then(res => {
                        if(res.status){
                            // alert(res.message)
                            loadMAP_CAMERA()
                            saveCanvas()
                            camera_list_modal.hide()
                        }
                    })
                }
                if(e.target.classList.contains('select_camera_row')) {
                    sole.post("../../controllers/cctv/assign_camera.php",{
                        id: e.target.getAttribute("c-id"),
                        cx: canvas.dataset.clickX,
                        cy: canvas.dataset.clickY,
                        type: "assign"
                    }).then(res => {
                        if(res.status){
                            // alert(res.message)
                            loadMAP_CAMERA()
                            saveCanvas()
                            camera_list_modal.hide()    
                        }
                    })
                }
            })
        })
    }

    function loadMAP_CAMERA(){
        sole.post("../../controllers/cctv/get_site_info.php", {
            id: manage_camera_title.getAttribute("s-id"),
        }).then(res => {
            document.getElementById("cctvCanvas").removeAttribute("hidden")
            loadCCTVList(res)
            setTimeout(() => {
                loadCCTVMap(res)
            }, 100);
        })
    }

    function loadCCTVMap(res) {
        const background = new Image();
        background.src = res.cctv[0]["floorplan"];
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        background.onload = function () {
            const targetWidth = 1050;
            const aspectRatio = background.height / background.width;
            const targetHeight = targetWidth * aspectRatio;

            canvas.width = targetWidth;
            canvas.height = targetHeight;

            // Draw the background
            ctx.drawImage(background, 0, 0, targetWidth, targetHeight);

            // After background is drawn, place the camera icons
            var tHeight = 14;
            if(res.cctv[0]["camera_size"] == "25"){
                tHeight = 14;
            }
            if(res.cctv[0]["camera_size"] == "30"){
                tHeight = 16;
            }
            if(res.cctv[0]["camera_size"] == "40"){
                tHeight = 18;
            }
            if(res.cctv[0]["camera_size"] == "50"){
                tHeight = 19;
            }
            if(res.cctv[0]["camera_size"] == "60"){
                tHeight = 20;
            }
            
            cameraList.camera.forEach(cam => {
                if (cam.cx != "-" && cam.cy != "-") {
                    const camImage = new Image();
                    camImage.src = "../../assets/img/camera/camera.png";

                    camImage.onload = function () {
                        const size = res.cctv[0]["camera_size"];
                        const centerX = parseFloat(cam.cx);
                        const centerY = parseFloat(cam.cy);
                        const angle = (parseFloat(cam.camera_angle) || 0) * Math.PI / 180;

                        ctx.save();
                        ctx.translate(centerX, centerY);
                        ctx.rotate(angle);
                        ctx.drawImage(camImage, -size/2, -size/2, size, size);
                        ctx.restore();

                        const label = cam.camera_id;
                        ctx.font = "bold "+tHeight+"px Arial";
                        ctx.textAlign = "center";
                        ctx.textBaseline = "top"; // Keeps text and background aligned

                        // Measure text size
                        const textWidth = ctx.measureText(label).width;
                        const textHeight = tHeight + 2; // Approx height for 14px font
                        const padding = 4;
                        const bgX = centerX - textWidth / 2 - padding;
                        const bgY = centerY + size / 2 + 5;
                        const bgWidth = textWidth + padding * 2;
                        const bgHeight = textHeight + padding * 1;

                        // Draw background
                        ctx.fillStyle = cam.camera_status == "UP" ? "rgb(0, 128, 0)" : "rgb(255, 0, 0)"; // Light translucent background
                        ctx.fillRect(bgX, bgY, bgWidth, bgHeight);

                        // Draw label text
                        ctx.fillStyle = "white";
                        ctx.fillText(label, centerX, bgY + padding);
                    };
                }
            });
        };
    }

    canvas.addEventListener("contextmenu", function (e) {
        if(JSON.parse(localStorage.getItem("g_member"))){
            e.preventDefault();

            const rect = canvas.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;

            // Store clicked position for modal to use later
            canvas.dataset.clickX = clickX;
            canvas.dataset.clickY = clickY;

            if(cameraList){
                camera_list_modal.show()
            }    
        }else{
            bs5.toast("info","Please operate as group member.")
        }
        
    });

    save_map_btn.addEventListener("click",function(){
        const dataUrl = canvas.toDataURL("image/png"); // You can use "image/jpeg" if preferred
        const link = document.createElement("a");
        link.href = dataUrl;
        link.download = localStorage.getItem("cameraTitle")+".png";
        link.click();
    })

    save_size_btn.addEventListener("click",function(){
        sole.post("../../controllers/cctv/set_camera_size.php",{
            id: manage_camera_title.getAttribute("s-id"),
            camera_size: camera_size.value
        }).then(res => {
            if(res.status){
                alert(res.message)
                sole.get("../../controllers/cctv/get_site.php").then(res => loadSite(res))
                loadMAP_CAMERA()   
                saveCanvas()
            }
        })
    })



    save_camera_form_btn.addEventListener("click",function(){
        if(camera_id.value){
            sole.post("../../controllers/cctv/add_camera.php",{
                uid: localStorage.getItem("userid"),
                camera_lid : manage_camera_title.getAttribute("s-id"),
                camera_id : camera_id.value,
                camera_type : camera_type.value,
                camera_subtype : camera_subtype.value,
                camera_ip_address : camera_ip_address.value,
                camera_port_no : camera_port_no.value,
                camera_username : camera_username.value,
                camera_password : camera_password.value,
                camera_angle : camera_angle.value,
                camera_location : camera_location.value,
                camera_brand : camera_brand.value,
                camera_model_no : camera_model_no.value,
                camera_barcode : camera_barcode.value,
                camera_status : camera_status.value,
                camera_remarks : camera_remarks.value
            }).then(res => {
                validateResponse(res,"add_camera")
            })
        }else{
            alert("Please provide camera alias or input DVR or NVR port no.")
        }
    })

    update_camera_form_btn.addEventListener("click",function(){
        if(camera_id.value){
            sole.post("../../controllers/cctv/update_camera.php",{
                id : this.getAttribute("c-id"),
                camera_id : camera_id.value,
                camera_type : camera_type.value,
                camera_subtype : camera_subtype.value,
                camera_ip_address : camera_ip_address.value,
                camera_port_no : camera_port_no.value,
                camera_username : camera_username.value,
                camera_password : camera_password.value,
                camera_angle : camera_angle.value,
                camera_location : camera_location.value,
                camera_brand : camera_brand.value,
                camera_model_no : camera_model_no.value,
                camera_barcode : camera_barcode.value,
                camera_status : camera_status.value,
                camera_remarks : camera_remarks.value
            }).then(res => {
                validateResponse(res,"update_camera")
            })
        }else{
            alert("Please provide camera alias or input DVR or NVR port no.")
        }
    })

    function validateResponse(res,func){
        if(res.status){
            if(func == "update_camera"){
                loadMAP_CAMERA()
                saveCanvas()
                clearForm()
            }
            if(func == "add_camera"){
                loadMAP_CAMERA()
                saveCanvas()
                clearForm()
            }
            alert(res.message)
        }else{
            alert(res.message)
        }
    }



    add_camera_btn.addEventListener("click",function(){
        camera_menu.setAttribute("hidden","")
        camera_form.removeAttribute("hidden")
        camera_form_control.removeAttribute("hidden")

        update_camera_form_btn.setAttribute("hidden","")
        save_camera_form_btn.removeAttribute("hidden")
    })

    cancel_camera_form_btn.addEventListener("click",function(){
        clearForm();
    })

    function clearForm(){
        camera_menu.removeAttribute("hidden")
        camera_form.setAttribute("hidden","")
        camera_form_control.setAttribute("hidden","")
        camera_id.value = ""
        camera_type.value = "-"
        camera_subtype.value = "-"
        camera_ip_address.value = ""
        camera_port_no.value = ""
        camera_username.value = ""
        camera_password.value = ""
        camera_angle.value = 0
        camera_preview.setAttribute("style","transform: rotate(0deg);")
        camera_location.value = ""
        camera_brand.value = ""
        camera_model_no.value = ""
        camera_barcode.value = ""
        camera_status.value = "UP"
        camera_remarks.value = ""
        camera_subtype_form.setAttribute("hidden","")
    }

    camera_angle.addEventListener("input",function(){
        camera_preview.setAttribute("style","transform: rotate("+this.value+"deg);")
        if(this.value > 360){
            const result = this.value.substring(0, 2);
            this.value = result
        }
        if(!this.value){
            this.value = 0
        }else{
            this.value = parseInt(this.value)
        }
    })
    camera_subtype.addEventListener("change",function(){
        if(this.value != "Coaxial Camera"){
            camera_subtype_form.removeAttribute("hidden")
        }else{
            camera_ip_address.value = ""
            camera_port_no.value = ""
            camera_username.value = ""
            camera_password.value = ""
            camera_subtype_form.setAttribute("hidden","")
        }
    })

    function saveCanvas(){
        setTimeout(() => {
            const imageData = canvas.toDataURL('image/png');
            sole.post("../../controllers/cctv/save_canvas.php",{
                id: cctv_dropdown_toggle.getAttribute("lid"),
                image: imageData,
                map_name: cctv_dropdown_toggle.innerText
            }).then(res => console.log(res))
        }, 1000);
        
    }
    // document.getElementById("sample_img").src = "../../assets/img/maps_output/" + encodeURIComponent("Sample.png")
}


// const canvas = document.getElementById("myCanvas");
// const ctx = canvas.getContext("2d");

// const background = new Image();
// background.src = "../../assets/img/floorplan.png"; // Replace with your actual image path

// background.onload = function () {
//     const targetWidth = 1050; // or canvas container width
//     const aspectRatio = background.height / background.width;
//     const targetHeight = targetWidth * aspectRatio;

//     canvas.width = targetWidth;
//     canvas.height = targetHeight;

//     ctx.drawImage(background, 0, 0, targetWidth, targetHeight);
// };






// USE THIS TO DISPLAY UPLOADED IMAGE, BECAUSE IT CANT BE DISPLAYED WHEN USING SRC ONLY

    // const sampleImage = new Image();
    // sampleImage.src = "../../assets/img/maps_output/Sample.png";

    // const div = document.getElementById("myDiv");
    // div.style.backgroundImage = `url('${sampleImage.src}')`;
    // div.style.backgroundSize = "cover";      // optional for full coverage
    // div.style.backgroundPosition = "center";

    // sampleImage.onload = () => {
    // div.style.backgroundImage = `url('${sampleImage.src}')`;
    // };


