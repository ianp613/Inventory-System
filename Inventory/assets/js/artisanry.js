var artisanry = document.getElementById("artisanry");

artisanry.addEventListener("click",function(){
    window.location.href = "inventory.php?loc=artisanry"
})

if(document.getElementById("artisan")){
    // QR GENERATOR SECTION
    const qr_generator_modal = new bootstrap.Modal(document.getElementById('qr_generator_modal'),unclose);
    const qrcanvas = document.getElementById("qrPreviewCanvas");
    const qrctx = qrcanvas.getContext("2d");
    loadDefault()

    var qr_type = document.getElementById("qr_type");
    var qr_generate_btn = document.getElementById("qr_generate_btn")
    var qr_text_container = document.getElementById("qr_text_container")
    var qr_wifi_container = document.getElementById("qr_wifi_container")

    var qr_text = document.getElementById("qr_text")
    var qr_text_counter = document.getElementById("qr_text_counter")
    var qr_text_temp = qr_text.value;
    var qr_color = document.getElementById("qr_color")

    var qrLogo = document.getElementById("qrLogo")
    var qrLogoContainer = document.getElementById("qrLogoContainer")   
    var qrLogoFile = document.getElementById("qrLogoFile")
    var logoSize = document.getElementById("logoSize")
    var logoSizeContainer = document.getElementById("logoSizeContainer")
    var logoSizeLabel = document.getElementById("logoSizeLabel")

    var qrSize = document.getElementById("qrSize")
    var qrPrecision = document.getElementById("qrPrecision")

    var qr_encryption = document.getElementById("qr_encryption")
    var qr_password_container = document.getElementById("qr_password_container")
    var qr_password = document.getElementById("qr_password")

    var qr_preview = document.getElementById("qr_preview")
    var qr_loading = document.getElementById("qr_loading")

    var qr_filename  = document.getElementById("qr_filename")

    var downloadqr_btn = document.getElementById("downloadqr_btn")

    // COLORS
    var bgColor = document.getElementById("bgColor")
    var fgColor = document.getElementById("fgColor")
    var bgTransparent = document.getElementById("bgTransparent")
    var bgImg = document.getElementById("bgImg")
    var bgImgFile = document.getElementById("bgImgFile")

    var containers = [
        qr_text_container,
        qr_wifi_container
    ]

    qr_type.addEventListener("change",function(){
        containers[parseInt(this.value)].removeAttribute("hidden")
        for (let index = 0; index < containers.length; index++) {
            if(index != this.value){
                containers[index].setAttribute("hidden","true")
            }
        }
    })

    qr_encryption.addEventListener("change",function(){
        if(this.value != "none"){
            qr_password_container.removeAttribute("hidden")
        }else{
            qr_password.value = ""
            qr_password_container.setAttribute("hidden","true")
        }
    })

    downloadqr_btn.addEventListener("click",function(){
        generate_QR("download")
    })


    

    // bgTransparent.addEventListener("change",function(){
    //     if(this.checked){
    //         bgImg.checked = false
    //         bgColor.classList.add("bgTransparent")
    //         bgImgFile.value = ""
    //         bgImgFile.setAttribute("hidden","true")
    //         bgColor.setAttribute("disabled","true")
    //         bgColor.value = "rgba(0, 0, 0, 0)"
    //         bgColor.style.color = "#000000"
    //     }else{
    //         bgColor.value = "#ffffff"
    //         bgColor.style.color = "#000000"
    //         bgColor.style.backgroundColor = "#ffffff"
    //         bgColor.classList.remove("bgTransparent")
    //         bgColor.removeAttribute("disabled")
    //     }
    // })

    // bgImg.addEventListener("change",function(){
    //     if(this.checked){
    //         bgTransparent.checked = false
    //         bgImgFile.removeAttribute("hidden")
    //         bgColor.value = "#ffffff"
    //         bgColor.style.color = "#000000"
    //         bgColor.style.backgroundColor = "#ffffff"
    //         bgColor.classList.remove("bgTransparent")
    //         bgColor.removeAttribute("disabled")
    //     }else{
    //         bgImgFile.value = ""
    //         bgImgFile.setAttribute("hidden","true")
    //     }
    // })

    qr_text.addEventListener("input",function(){
        if(this.value.length <= 200){
            qr_text_counter.innerText = this.value.length + "/" + "200"
            qr_text_temp = this.value
        }else{
            this.value = qr_text_temp
            alert("You've reach the maximum number of characters.")
        }
    })

    qrLogo.addEventListener("click",function(){
        var type = get_designSelector("qrLogo");
        if(type == "none"){
            logoSizeContainer.setAttribute("hidden","true")
        }else{
            logoSizeContainer.removeAttribute("hidden")
        }
        if(type == "custom"){
            qrLogoContainer.removeAttribute("hidden")
            qrLogoFile.click()
        }else{
            qrLogoFile.value = ""
            qrLogoContainer.setAttribute("hidden","true")
        }
    })

    logoSize.addEventListener("input",function(){
        logoSizeLabel.innerText = "Logo Size: " + this.value + "%"
    })

    function loadDefault(){
        const background = new Image();
        background.src = "../../assets/img/artisanry/qr-hello.png";
        qrctx.clearRect(0, 0, qrcanvas.width, qrcanvas.height);

        background.onload = function () {
            const targetSize = 250;
            qrcanvas.width = targetSize;
            qrcanvas.height = targetSize;

            // Draw the background
            qrctx.drawImage(background, 0, 0, targetSize, targetSize);
        }
    }

    function loadGeneratedQR(res){
        if(res.status){
            qr_preview.removeAttribute("hidden","true")
            qr_loading.setAttribute("hidden","true")
            qr_preview.classList.remove("image-wrapper")
            const background = new Image();
            background.src = res.qr_data;
            qrctx.clearRect(0, 0, qrcanvas.width, qrcanvas.height);
            background.onload = function () {
                const targetSize = 250;
                qrcanvas.width = targetSize;
                qrcanvas.height = targetSize;

                // Draw the background
                qrctx.drawImage(background, 0, 0, targetSize, targetSize);
            }    
        }
    }

    qr_generate_btn.addEventListener("click",function(){
        generate_QR("preview")
    })

    function generate_QR(action){
        const formData = new FormData();
        formData.append("action",action)
        formData.append("qr_type", qr_type.value)
        formData.append("bgColor", bgColor.value)
        formData.append("fgColor", fgColor.value)
        formData.append("pattern", get_designSelector("pattern"))
        formData.append("marker", get_designSelector("marker"))
        formData.append("cursor", get_designSelector("cursor"))
        formData.append("logo", get_designSelector("qrLogo"))
        formData.append("logo_size", logoSize.value)
        formData.append("qrSize", qrSize.value)
        formData.append("qrPrecision", qrPrecision.value)
        var submit = true;

        if(get_designSelector("qrLogo") == "custom"){
            if(qrLogoFile.files.length > 0){
                formData.append("logo_file", qrLogoFile.files[0])
            }else{
                alert("Please select a logo.")
                submit = false
            }
        }
        // formData.append("bgTransparent", bgTransparent.checked ? "true" : "false")

        // if(bgImg.checked){
        //     if(bgImgFile.files.length > 0){
        //         submit = true;
        //         formData.append("bgImage", bgImgFile.files[0])
        //     }else{
        //         alert("Please select a background image.")
        //         submit = false;
        //     }
        // }

        if(qr_type.value = "0"){
           if(qr_text.value){
                formData.append("qr_text", qr_text.value); 
           }else{
                alert("Please input data.")
                submit = false
           }
        }

        if(submit){
            qr_preview.setAttribute("hidden","true")
            qr_loading.removeAttribute("hidden","true")
            if(bgColor.value != "rgba(0, 0, 0, 0)" && bgColor.value != "#ffffff"){
                qr_loading.style.backgroundColor = bgColor.value
            }else{
                qr_loading.style.backgroundColor = "#000000"
            }
            sole.file("../../controllers/artisanry/qr_generate.php",formData)
            .then(res => {
                console.log(res)
                loadGeneratedQR(res)
                if(action == "download"){
                    downloadQR(res.qr_file,qr_filename.value)
                }
            })    
        }
    }

    


    // var qr_generator = document.getElementById("qr_generator");

    // 
    // var qr_type_text = document.getElementById("qr_type_text");
    // var qr_text = document.getElementById("qr_text");
    // var qr_type_wifi = document.getElementById("qr_type_wifi");
    // var qr_ssid = document.getElementById("qr_ssid");
    // var qr_key = document.getElementById("qr_key");
    
    // var qr_logo_overlay = document.getElementById("qr_logo_overlay");
    // var qr_custom_logo_display = document.getElementById("qr_custom_logo_display");
    // var qr_custom_logo = document.getElementById("qr_custom_logo");

    // var qr_generate_btn = document.getElementById("qr_generate_btn");

    // qr_type.addEventListener("change",function(){
    //     this.value == "wifi" ? qr_type_wifi.removeAttribute("hidden") : qr_type_wifi.setAttribute("hidden","true")
    //     this.value == "text" ? qr_type_text.removeAttribute("hidden") : qr_type_text.setAttribute("hidden","true")
    // })

    // qr_logo_overlay.addEventListener("change",function(){
    //     this.value == "custom" ? qr_custom_logo_display.removeAttribute("hidden") : qr_custom_logo_display.setAttribute("hidden","true")
    // })

    qr_generator.addEventListener("click",function(){
        qr_generator_modal.show()
    })

    // qr_generate_btn.addEventListener("click",function(){
    //     var submit = true
    //     if(qr_type.value === "text") submit = qr_text.value ? true : (alert("Please input text."), false);
    //     if(qr_type.value === "wifi") submit = (qr_ssid.value && qr_key.value) ? true : (alert("Please input both SSID and key."), false);
    //     if(qr_logo_overlay.value === "custom") submit = qr_custom_logo.files.length ? true : (alert("Please select a logo file."), false);

    //     if(submit){
    //         const formData = new FormData();
    //         formData.append("type", qr_type.value);
    //         formData.append("text", qr_text.value);
    //         formData.append("ssid", qr_ssid.value);
    //         formData.append("key", qr_key.value);
    //         formData.append("overlay", qr_logo_overlay.value);

    //         if(qr_custom_logo.files.length){
    //             formData.append("logo", qr_custom_logo.files[0]);    
    //         }
            

    //         sole.file("../../controllers/artisanry/qr_generate.php",{
    //             "type" : qr_type.value,
    //         })
    //     }
    // })
    // sole.get("../../controllers/artisanry/artisanry.php")
    // .then(res => console.log(res))
    initialize_designSelector()
    function initialize_designSelector(){
        var selector = document.getElementsByClassName("design-selector")
        for (let i = 0; i < selector.length; i++) {
            var childrenTemp = selector[i].children
            selector[i].children[0].classList.add("design-selected")
            for (let j = 0; j < childrenTemp .length; j++) {
                childrenTemp[j].addEventListener("click",function(){
                    for (let k = 0; k < this.parentNode.children.length; k++) {
                        this.parentNode.children[k].classList.remove("design-selected")
                    }
                    this.classList.add("design-selected")
                })
            }
        }
    }
    
    function get_designSelector(id){
        var selector = document.getElementById(id)
        var val = false;
        if(selector){
            if(selector.classList.contains("design-selector")){
                for (let i = 0; i < selector.children.length; i++) {
                    if(selector.children[i].classList.contains("design-selected")){
                        val = selector.children[i].getAttribute("value")
                    }
                }
            }else{
                alert("Error getting value from \"" + id + "\", not a design selector.")
            }    
        }else{
            alert("Error getting value from \"" + id + "\", not a design selector.")
        }
        return val
    }

    function downloadQR(url, filename) {
        if(!filename){
            filename = url.split('/').pop()
        }else{
            if(filename.split('.').pop().toLowerCase() == "png"){
                const dotIndex = filename.lastIndexOf(".");
                filename = filename.slice(0, dotIndex) + filename.slice(dotIndex).toLowerCase();
            }else{
                filename += ".png"
            }
            
        }

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
        .catch(error => alert("error", "Download Failed: " + error));

        setTimeout(() => {
            sole.post("../../controllers/artisanry/qr_delete.php",{
                qr_file : url
            }).then(res => console.log(res));
        }, 5000);
    }
}
