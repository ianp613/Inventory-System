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
    <body class="dark">
        
        <div id="um_login" class="w-100 d-flex justify-content-center um-login">
            <div class="wd-580 p-4">
                <div class="d-flex w-100 mt-4">
                    <img class="wd-60 ht-60 me-2 rounded-circle" style="margin-top: -23px;" src="../../assets/img/unifi.jpg" alt="" srcset="">
                    <h5 class="text-secondary mb-3 ms-2 d-flex"><div></div><span class="fa fa-wifi mt-1"></span> <span class="ms-2">WIFI TEAM | UNIFI MAC</span></h5>
                </div>




                <div class="modal-body pb-0">
                    <div class="btn-group w-100">
                        <button id="register_form_btn" class="btn btn-light border">Register MAC</button>
                        <button id="delete_form_btn" class="btn btn-secondary border">Delete MAC</button>
                        <button id="password_form_btn" class="btn btn-secondary border">Wifi Status</button>
                    </div>
                </div>



                <!-- REGISTER FORM START -->
                <div id="register_form">
                    <div class="modal-body">
                        <div class="row">
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
                                <label for="mac_location" class="mb-2">Site / Location</label>
                                <select name="" id="mac_location" class="form-control">
                                    <option selected disabled value="">-- Select Site / Location --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="mac_project" class="mb-2">Project / Office</label>
                                <select name="" id="mac_project" class="form-control">
                                    <option selected disabled value="">-- Select Project / Office --</option>
                                </select>
                            </div>
                        </div>
                        <label hidden for="mac_register_by" class="mb-2 mt-2">Who registers this MAC Address?</label>
                        <select hidden name="" id="mac_register_by" class="form-control">
                            <option selected disabled value="">-- Registered By  --</option>
                        </select>
                        <label for="mac_remarks" class="mb-2 mt-2">Remarks</label>
                        <textarea maxlength="1000" rows="5" name="" id="mac_remarks" class="form-control" placeholder="Aa"></textarea>
                    </div>
                    <div class="modal-header border-0 pt-0">
                        <div class=" input-group">
                            <span class="input-group-text">Theme</span>
                            <select name="" id="theme">
                                <option value="light">Light</option>
                                <option selected value="dark">Dark</option>
                            </select>    
                        </div>
                        <div class="d-flex wd-500 justify-content-end">
                            <button id="clear_btn" type="button" class="btn btn-secondary btn-sm rounded-pill wd-90 me-1"><span class="fa fa-remove"></span> Clear</button>
                            <button id="register_mac" type="button" class="btn btn-success btn-sm rounded-pill wd-90"><span class="fa fa-save"></span> Register</button>
                            <button hidden disabled id="loading_mac" type="button" class="btn btn-success btn-sm rounded-pill"><div class="spinner-border wd-15 ht-15" role="status"></div> Loading</button>     
                        </div>
                    </div>
                </div>
                <!-- REGISTER FORM END -->

                









                <!-- DELETE FORM START -->
                <div hidden id="delete_form">
                    <div class="modal-body">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="delete_mac_address" class="mb-2">MAC Address</label>
                                <input required type="text" name="" id="delete_mac_address" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="delete_mac_ssid" class="mb-2">Wifi SSID</label>
                                <select name="" id="delete_mac_ssid" class="form-control">
                                    <option disabled selected value="">-- Select Wifi SSID --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-header border-0 pt-0">
                        <div class="d-flex wd-500 justify-content-end">
                            <button id="delete_clear_btn" type="button" class="btn btn-secondary btn-sm rounded-pill wd-90 me-1"><span class="fa fa-remove"></span> Clear</button>
                            <button id="delete_mac" type="button" class="btn btn-danger btn-sm rounded-pill wd-90 me-1"><span class="fa fa-trash"></span> Remove</button>
                            <button hidden disabled id="delete_loading_mac" type="button" class="btn btn-danger btn-sm rounded-pill"><div class="spinner-border wd-15 ht-15" role="status"></div> Loading</button>     
                        </div>
                    </div>
                </div>
                <!-- DELETE FORM END -->




                <!-- PASSWORD FORM START -->
                <div hidden id="password_form">
                    <div class="modal-body">
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="password_mac_ssid" class="mb-2">Wifi SSID</label>
                                <select name="" id="password_mac_ssid" class="form-control">
                                    <option disabled selected value="">-- Select Wifi SSID --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-header border-0 pt-0">
                        <div class="d-flex wd-500 justify-content-end">
                            <button id="password_mac" type="button" class="btn btn-primary btn-sm rounded-pill wd-90 me-1"><span class="fa fa-check"></span> Show</button>
                            <button hidden disabled id="password_loading_mac" type="button" class="btn btn-primary btn-sm rounded-pill"><div class="spinner-border wd-15 ht-15" role="status"></div> Loading</button>     
                        </div>
                    </div>
                </div>
                <!-- PASSWORD FORM END -->




















                <div class="ps-3 pe-3" id="mac_message">
                    <!-- Message Here -->
                </div>
            </div>
        </div>
        <div id="um_login_card" class="um-login-card">
            <div class="modal-header pt-3 pb-2">
                <h6>LOGIN TO YOUR ACCOUNT</h6>
            </div>
            <div class="modal-body">
                <input id="um_login_userid" type="text" name="" id="" class="form-control mb-2" placeholder="User ID">
                <input id="um_login_password" type="password" name="" id="" class="form-control" placeholder="Password">
            </div>
            <div class="modal-footer">
                <button id="um_login_btn" class="btn btn-secondary w-100"><span class="fa fa-sign-in"></span> LOGIN</button>
            </div>
            <h6 class="text-center mb-0 mt-2 f-12 f-i fw-bold">Intergrated with Inventory System Credentials</h6>
            <p class="text-center mt-0 f-12 f-i">Unifi MAC v.1.1</p>
        </div>
        <style>
            .modal-content{
                border-radius: 15px !important;
                padding: 5px;
                padding-top: 15px;
                background-color: #ffffffd7 !important;
                color: #000000;
            }
            .modal-content input, .modal-content select, .modal-content textarea, .modal-content button{
                border-radius: 20px;
            }

            .modal-content button{
                padding-left: 10px;
                padding-right: 10px;
            }
        </style>
        <h6 class="copyright f-10 text-secondary"></h6>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
        <script src="../assets/js/unifi-mac.js"></script>
    </body>
</html>
