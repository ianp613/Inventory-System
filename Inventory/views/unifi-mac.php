<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UNIFI MAC</title>
        <link rel="shortcut icon" href="../assets/img/logo-icore-ico.png" type="image/x-icon">

        <!-- Type system: Space Grotesk (display) / Inter (body) / JetBrains Mono (technical data) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/style-unifi-mac.css">
    </head>
    <body class="dark">

        <div id="um_login" class="w-100 d-flex justify-content-center um-login">
            <div class="wd-580 p-4">

                <div class="umx-header">
                    <div class="umx-mark">
                        <img src="../../assets/img/unifi.png" alt="">
                    </div>
                    <div class="umx-title-block">
                        <p class="umx-eyebrow"><span class="umx-pulse"></span> WIFI TEAM &middot; DDC LEYTE ICORE</p>
                        <h5 class="umx-title mb-0">Unifi MAC <span>Console</span></h5>
                    </div>
                </div>

                <div class="umx-card">

                    <div class="modal-body pb-0 umx-tabbar">
                        <div class="btn-group w-100">
                            <button id="register_form_btn" class="btn btn-light border"><span class="fa fa-plus-circle me-1"></span>Register MAC</button>
                            <button id="delete_form_btn" class="btn btn-secondary border"><span class="fa fa-trash-o me-1"></span>Delete MAC</button>
                            <button id="password_form_btn" class="btn btn-secondary border"><span class="fa fa-signal me-1"></span>Wifi Status</button>
                            <button id="voucher_form_btn" class="btn btn-secondary border"><span class="fa fa-ticket me-1"></span>Voucher Code</button>
                        </div>
                    </div>

                    <!-- REGISTER FORM START -->
                    <div id="register_form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 umx-field">
                                    <label for="mac_address" class="mb-2">MAC Address</label>
                                    <input required type="text" name="" id="mac_address" class="form-control" placeholder="aa:bb:cc:dd:ee:ff" list="mac_address_">
                                    <datalist id="mac_address_">
                                        <!-- Data List here -->
                                    </datalist>
                                </div>
                                <div class="col-md-6 umx-field">
                                    <label for="mac_ssid" class="mb-2">Wifi SSID</label>
                                    <select name="" id="mac_ssid" class="form-control">
                                        <option disabled selected value="">-- Select Wifi SSID --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 umx-field">
                                    <label for="mac_name" class="mb-2">Name</label>
                                    <input required type="text" name="" id="mac_name" class="form-control" placeholder="Full name">
                                </div>
                                <div class="col-md-6 umx-field">
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
                                <div class="col-md-6 umx-field">
                                    <label for="mac_location" class="mb-2">Site / Location</label>
                                    <select name="" id="mac_location" class="form-control">
                                        <option selected disabled value="">-- Select Site / Location --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 umx-field">
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
                            <div class="umx-field mt-2">
                                <label for="mac_remarks" class="mb-2">Remarks</label>
                                <textarea maxlength="1000" rows="5" name="" id="mac_remarks" class="form-control" placeholder="Aa"></textarea>
                            </div>
                        </div>
                        <div class="modal-header border-0 pt-0 umx-actionbar">
                            <div class="input-group">
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
                                <div class="col-md-6 umx-field">
                                    <label for="delete_mac_address" class="mb-2">MAC Address</label>
                                    <input required type="text" name="" id="delete_mac_address" class="form-control" placeholder="aa:bb:cc:dd:ee:ff">
                                </div>
                                <div class="col-md-6 umx-field">
                                    <label for="delete_mac_ssid" class="mb-2">Wifi SSID</label>
                                    <select name="" id="delete_mac_ssid" class="form-control">
                                        <option disabled selected value="">-- Select Wifi SSID --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-header border-0 pt-0 umx-actionbar">
                            <div></div>
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
                                <div class="col-md-12 umx-field">
                                    <label for="password_mac_ssid" class="mb-2">Wifi SSID</label>
                                    <select name="" id="password_mac_ssid" class="form-control">
                                        <option disabled selected value="">-- Select Wifi SSID --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-header border-0 pt-0 umx-actionbar">
                            <div></div>
                            <div class="d-flex wd-500 justify-content-end">
                                <button id="password_mac" type="button" class="btn btn-primary btn-sm rounded-pill wd-90 me-1"><span class="fa fa-check"></span> Show</button>
                                <button hidden disabled id="password_loading_mac" type="button" class="btn btn-primary btn-sm rounded-pill"><div class="spinner-border wd-15 ht-15" role="status"></div> Loading</button>
                            </div>
                        </div>
                    </div>
                    <!-- PASSWORD FORM END -->


                    <!-- VOUCHER FORM START -->
                    <div hidden id="voucher_form">
                        <div class="umx-voucher-result">
                            <h6 class="text-warning" id="voucher_last_code"></h6>
                            <div class="umx-vr-code" id="voucher_code">— — — — —</div>
                            <div class="umx-vr-grid">
                                <div class="umx-vr-row"><span class="umx-vr-label">Duration</span><span id="voucher_duration"></span></div>
                                <div class="umx-vr-row"><span class="umx-vr-label">Status</span><span id="voucher_status"></span></div>
                                <div class="umx-vr-row"><span class="umx-vr-label">Down</span><span id="voucher_down"></span></div>
                                <div class="umx-vr-row"><span class="umx-vr-label">Up</span><span id="voucher_up"></span></div>
                                <div class="umx-vr-row"><span class="umx-vr-label">Quota</span><span id="voucher_qouta"></span></div>
                                <div class="umx-vr-row"><span class="umx-vr-label">Used</span><span id="voucher_used"></span></div>
                            </div>
                            <div class="umx-vr-row mt-2" style="border-bottom:none;"><span class="umx-vr-label">Note</span><span id="voucher_note"></span></div>
                        </div>
                        <div class="modal-body pt-0">
                            <div class="row mt-2">
                                <div class="col umx-field">
                                    <label for="voucher_site" class="mb-2">Wifi Network <span class="f-i f-12">(Check the device obtained IP Address)</span></label>
                                    <select name="" id="voucher_site" class="form-control">
                                        <option value="" selected disabled>-- Select Wifi Network --</option>
                                        <option value="z1qqymab">Network 10 | Free Wifi Planning</option>
                                        <option value="5n1pz4m9">Network 07 | Annex Building 1st Floor</option>
                                        <option value="dqevxwsa">Network 07 | Annex Building 2nd Floor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 umx-field">
                                    <label for="voucher_name" class="mb-2">Name</label>
                                    <input required type="text" name="" id="voucher_name" class="form-control" placeholder="Full name">
                                </div>
                                <div class="col-md-6 umx-field">
                                    <label for="voucher_device" class="mb-2">Device</label>
                                    <select name="" id="voucher_device" class="form-control">
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
                                <div class="col-md-6 umx-field">
                                    <label for="voucher_location" class="mb-2">Site / Location</label>
                                    <select name="" id="voucher_location" class="form-control">
                                        <option selected disabled value="">-- Select Site / Location --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 umx-field">
                                    <label for="voucher_project" class="mb-2">Project / Office</label>
                                    <select name="" id="voucher_project" class="form-control">
                                        <option selected disabled value="">-- Select Project / Office --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-header border-0 pt-0 umx-actionbar">
                            <div></div>
                            <div class="d-flex wd-500 justify-content-end">
                                <button id="voucher_clear" type="button" class="btn btn-secondary btn-sm rounded-pill wd-120 me-1"><span class="fa fa-remove"></span> Clear</button>
                                <button id="voucher_get" type="button" class="btn btn-success btn-sm rounded-pill wd-120 me-1"><span class="fa fa-key"></span> Get Code</button>
                                <button hidden disabled id="voucher_get_loading" type="button" class="btn btn-success btn-sm rounded-pill"><div class="spinner-border wd-15 ht-15" role="status"></div> Loading</button>
                            </div>
                        </div>
                    </div>
                    <!-- VOUCHER FORM END -->

                    <div class="ps-3 pe-3" id="mac_message">
                        <!-- Message Here -->
                    </div>

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
            <h6 class="text-center mb-0 mt-2 f-12 f-i fw-bold">Intergrated with DDC LEYTE ICORE Credentials</h6>
            <p class="text-center mt-0 f-12 f-i">Unifi MAC v.1.2</p>
        </div>

        <h6 class="copyright f-10 text-secondary"></h6>

        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.js?v=<?= filemtime('../assets/js/sole.js') ?>"></script>
        <script src="../assets/js/modal_alert.js?v=<?= filemtime('../assets/js/modal_alert.js') ?>"></script>
        <script src="../assets/js/unifi-mac.js?v=<?= filemtime('../assets/js/unifi-mac.js') ?>"></script>
    </body>
</html>