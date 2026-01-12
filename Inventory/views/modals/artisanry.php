<div id="qr_generator_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">QR Code Generator</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="border rounded-3 p-3">
                            <label for="qr_type" class="mb-2 fwt-5">Select QR Code Type</label>
                            <select disabled name="qr_type" id="qr_type" class="form-control wd-150 text-secondary"  style="font-family: 'FontAwesome', sans-serif;">
                                <option value="0">&#xf15c Text</option>
                                <option disabled value="1">&#xf1eb Wifi</option>
                                <option disabled value="2">&#xf0e0 Email</option>
                                <option disabled value="3">&#xf0c1 URL</option>
                            </select>
                            <div id="qr_text_container" class="mt-3">
                                <textarea maxlength="200" rows="5" name="qr_text" id="qr_text" class="form-control text-secondary fw-bolder" placeholder="Enter your text"></textarea>
                                <p id="qr_text_counter" class="w-100 text-end text-secondary">0/200</p>
                            </div>
                            <div hidden id="qr_wifi_container" class="mt-3">
                                <label for="qr_ssid" class="mb-2">Network Name</label>
                                <div class="d-flex w-100">
                                    <input type="text" class="form-control wd-500" placeholder="SSID"> 
                                    <input class="ms-4" type="checkbox" value="" id="flexCheckDefault">
                                    <label style="margin-top: 6px !important; margin-left: 5px;" for="flexCheckDefault">Hidden</label>
                                </div>
                                <div class="row d-flex w-100">
                                    <div class="wd-180">
                                        <label for="qr_encryption" class="mb-2 mt-2">Encryption</label>
                                        <select name="qr_encryption" id="qr_encryption" class="form-control wd-150 text-secondary"  style="font-family: 'FontAwesome', sans-serif;">
                                            <option value="none">None</option>
                                            <option value="wpa/wpa2">WPA/WPA2</option>
                                            <option value="wep">WEP</option>
                                        </select>        
                                    </div>
                                    <div hidden id="qr_password_container" class="wd-500">
                                        <label for="qr_password" class="mb-2 mt-2">Password</label>
                                        <input id="qr_password" type="text" class="form-control">     
                                    </div>
                                </div>
                            </div>

                            <div class="accordion mt-3" id="accordionQR">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                            <span class="fa fa-paint-brush me-2"></span> <b>Colors</b>
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="bgColor" class="mb-2">Background Color</label>
                                                    <input class="colorpicker colorpicker-light" id="bgColor" type="text">
                                                    <!-- <div class="d-flex mt-2 mb-1">
                                                        <input type="checkbox" name="" id="bgTransparent">
                                                        <label for="bgTransparent" class="f-15 ms-2 text-secondary">Transparent Background</label>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="checkbox" name="" id="bgImg">
                                                        <label for="bgImg" class="f-15 ms-2 text-secondary">Image Background</label>
                                                    </div>
                                                    <input hidden type="file" accept="image/*" name="" id="bgImgFile" class="form-control mt-2"> -->
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="fgColor" class="mb-2">Foreground Color</label>
                                                    <input class="colorpicker colorpicker-dark" id="fgColor" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            <span class="fa fa-qrcode me-2"></span> <b>Design</b>
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                                        <div class="accordion-body">
                                            <h6>Pattern</h6>
                                            <div id="pattern" class="design-selector">
                                                <img value="S1" src="../../assets/img/artisanry/qr-pattern/S1.png" alt="">
                                                <img value="S2" src="../../assets/img/artisanry/qr-pattern/S2.png" alt="">
                                                <img value="S3" src="../../assets/img/artisanry/qr-pattern/S3.png" alt="">
                                                <img value="S4" src="../../assets/img/artisanry/qr-pattern/S4.png" alt="">
                                                <img value="S5" src="../../assets/img/artisanry/qr-pattern/S5.png" alt="">
                                                <img value="S6" src="../../assets/img/artisanry/qr-pattern/S6.png" alt="">
                                                <img value="S7" src="../../assets/img/artisanry/qr-pattern/S7.png" alt="">
                                                <img value="S8" src="../../assets/img/artisanry/qr-pattern/S8.png" alt="">    
                                                <img value="S9" src="../../assets/img/artisanry/qr-pattern/S9.png" alt="">    
                                            </div>
                                            <h6 class="mt-2">Marker Border</h6>
                                            <div id="marker" class="design-selector">
                                                <img class="p-2" value="M1" src="../../assets/img/artisanry/qr-marker/M1.png" alt="">
                                                <img class="p-2" value="M2" src="../../assets/img/artisanry/qr-marker/M2.png" alt="">
                                                <img class="p-2" value="M3" src="../../assets/img/artisanry/qr-marker/M3.png" alt="">
                                                <img class="p-2" value="M4" src="../../assets/img/artisanry/qr-marker/M4.png" alt="">
                                                <img class="p-2" value="M5" src="../../assets/img/artisanry/qr-marker/M5.png" alt="">
                                                <img class="p-2" value="M6" src="../../assets/img/artisanry/qr-marker/M6.png" alt="">
                                            </div>
                                            <h6 class="mt-2">Marker Cursor</h6>
                                            <div id="cursor" class="design-selector">
                                                <img class="p-3" value="C1" src="../../assets/img/artisanry/qr-cursor/C1.png" alt="">
                                                <img class="p-3" value="C2" src="../../assets/img/artisanry/qr-cursor/C2.png" alt="">
                                                <img class="p-3" value="C3" src="../../assets/img/artisanry/qr-cursor/C3.png" alt="">
                                                <img class="p-3" value="C4" src="../../assets/img/artisanry/qr-cursor/C4.png" alt="">
                                                <img class="p-3" value="C5" src="../../assets/img/artisanry/qr-cursor/C5.png" alt="">
                                                <img class="p-3" value="C6" src="../../assets/img/artisanry/qr-cursor/C6.png" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            <span class="fa fa-certificate me-2"></span> <b>Logo</b>
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                                        <div class="accordion-body">
                                            <div id="qrLogo" class="design-selector">
                                                <h6 value="none" class="pb-2 pt-2"><span class="fa fa-remove"></span></h6>
                                                <h6 value="custom" class="pb-2 pt-2"><span class="fa fa-upload"></span></h6>
                                                <img value="../../assets/img/artisanry/qr-logo/fposi.png" class="p-1" src="../../assets/img/artisanry/qr-logo/fposi.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/ddc.png" class="p-1" src="../../assets/img/artisanry/qr-logo/ddc.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/link-circle.png" class="p-2" src="../../assets/img/artisanry/qr-logo/link-circle.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/link-square.png" class="p-2" src="../../assets/img/artisanry/qr-logo/link-square.png" alt="">
                                                <img value="../../assets/img/artisanry/qr-logo/email-circle.png" class="p-2" src="../../assets/img/artisanry/qr-logo/email-circle.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/email-square.png" class="p-2" src="../../assets/img/artisanry/qr-logo/email-square.png" alt="">
                                                <img value="../../assets/img/artisanry/qr-logo/location-circle.png" class="p-2" src="../../assets/img/artisanry/qr-logo/location-circle.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/location-square.png" class="p-2" src="../../assets/img/artisanry/qr-logo/location-square.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/phone-circle.png" class="p-2" src="../../assets/img/artisanry/qr-logo/phone-circle.png" alt="">
                                                <img value="../../assets/img/artisanry/qr-logo/wifi-circle.png" class="p-2" src="../../assets/img/artisanry/qr-logo/wifi-circle.png" alt="">  
                                                <img value="../../assets/img/artisanry/qr-logo/wifi-square.png" class="p-2" src="../../assets/img/artisanry/qr-logo/wifi-square.png" alt="">
                                            </div>
                                            <div hidden id="logoSizeContainer">
                                                <input type="range" class="form-range mt-2" value="100" min="30" max="100" id="logoSize">       
                                                <label id="logoSizeLabel" for="logoSize" class="form-label">Logo Size: 100%</label>    
                                            </div>
                                            <div hidden id="qrLogoContainer">
                                                <h6 class="mt-2">Upload your logo or select a watermark</h6>
                                                <input accept="image/*" class="form-control" type="file" name="" id="qrLogoFile">    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                            <span class="fa fa-th-list me-2"></span> <b>Frame</b>
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                                        <div class="accordion-body">
                                            <code>WORK IN PROGRESS</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                            <span class="fa fa-toggle-on me-2"></span> <b>Options</b>
                                        </button>
                                    </h2>
                                    <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="qrSize">Size</label>
                                                    <select name="qrSize" id="qrSize" class="form-control mt-2">
                                                        <option value="200">200</option>
                                                        <option value="300">300</option>
                                                        <option value="400">400</option>
                                                        <option value="500">500</option>
                                                        <option value="600">600</option>
                                                        <option value="700">700</option>
                                                        <option selected value="800">800</option>
                                                        <option value="900">900</option>
                                                        <option value="1000">1000</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="qrPrecision">Precision</label>
                                                    <select class="form-control mt-2" name="qrPrecision" id="qrPrecision">
                                                        <option value="Low">Low</option>
                                                        <option value="Medium">Medium</option>
                                                        <option value="Quartile">Quartile</option>
                                                        <option selected value="High">High</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button id="qr_generate_btn" class="btn btn-success alert-success rounded-pill fw-bolder mt-4 mb-2"><span class="fa fa-refresh"></span> GENERATE QR PREVIEW</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="rounded-3 p-1 text-center">
                            <div id="qr_preview" class="w-100 text-center image-wrapper pb-3">
                                <canvas class="shadow" id="qrPreviewCanvas"></canvas>
                            </div>
                            <img hidden id="qr_loading" src="../../assets/img/artisanry/qr-loading.svg" alt="sadsad" srcset="">
                        </div>
                        <label for="qr_filename">QR Filename <i class="f-13">(Optional)</i></label>
                        <input type="text" id="qr_filename" class="form-control mt-2">
                        <button id="downloadqr_btn" class="mt-4 ht-55 btn btn-primary w-100 d-flex justify-content-center fw-bolder"><span class="fa fa-download mt-2 f-20 me-2"></span><div>DOWNLOAD <br> <p class="f-13" style="margin-top: -4px; font-weight: 400;">PNG</p></div></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>