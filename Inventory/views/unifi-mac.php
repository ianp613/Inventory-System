<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UNIFI MAC</title>
        <link rel="shortcut icon" href="../assets/img/logo-ico.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/datatables/datatables.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/colorpicker.css">
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <div class="w-100 d-flex justify-content-center">
            <div class="wd-580 p-4">
                <div class="d-flex w-100 mt-4">
                    <img class="wd-60 ht-60 me-2 rounded-circle" style="margin-top: -23px;" src="../../assets/img/unifi.jpg" alt="" srcset="">
                    <h5 class="text-secondary mb-3 ms-2 d-flex"><div id="g_name_display"></div><span class="fa fa-wifi mt-1"></span> <span class="ms-2">WIFI TEAM | UNIFI MAC</span></h5>
                </div>
                <div class="modal-body">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="mac_address" class="mb-2">MAC Address</label>
                            <input required type="text" name="" id="mac_address" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="mac_ssid" class="mb-2">Wifi SSID</label>
                            <select name="" id="mac_ssid" class="form-control">
                                <option disabled selected value="">-- Select Wifi SSID --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="mac_name" class="mb-2">Name</label>
                            <input required type="text" name="" id="mac_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="mac_device" class="mb-2">Device</label>
                            <select name="" id="mac_device" class="form-control">
                                <option disabled selected value="">-- Select Device --</option>
                                <option value="Cellphone">Cellphone</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Desktop">Desktop</option>
                                <option value="Smart TV / Android TV">Smart TV / Android TV</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="mac_project" class="mb-2">Project / Office</label>
                            <input required type="text" name="" id="mac_project" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="mac_location" class="mb-2">Location</label>
                            <input required type="text" name="" id="mac_location" class="form-control">
                        </div>
                    </div>
                    <label for="mac_remarks" class="mb-2 mt-2">Remarks</label>
                    <textarea maxlength="1000" rows="5" name="" id="mac_remarks" class="form-control" placeholder="Aa"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button id="clear_btn" type="button" class="btn btn-secondary btn-sm rounded-pill wd-90"><span class="fa fa-remove"></span> Clear</button>
                    <button id="register_mac" type="button" class="btn btn-success btn-sm rounded-pill wd-90"><span class="fa fa-save"></span> Register</button>
                    <button hidden disabled id="loading_mac" type="button" class="btn btn-success btn-sm rounded-pill"><div class="spinner-border wd-15 ht-15" role="status"></div> Loading</button>
                </div>
                <div class="ps-3 pe-3" id="mac_message">
                    <!-- Message Here -->
                </div>
                
        </div>
        <h6 class="copyright f-10 text-secondary"></h6>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
        <script src="../assets/js/unifi-mac.js"></script>
    </body>
</html>
