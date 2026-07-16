<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DDC LEYTE ICORE</title>
        <link rel="shortcut icon" href="../assets/img/logo-icore-ico.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/datatables/datatables.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/colorpicker.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/theme/Default.css">
    </head>
    <body>
        <div class="w-100 d-flex justify-content-center">
            <div class="theme-card theme-card-dark wd-550 p-4 mt-4">
                <div class="d-flex w-100 mt-4">
                    <img class="wd-90 ht-70 me-3 mb-2" style="margin-top: -20px;" src="../../assets/img/LEYTE-ICORE.png" alt="" srcset="">

                    <div>
                        <h5 class="text-secondary mb-0 d-flex fw-bolder" id="g_name_display"></h5>
                        <h5 class="text-secondary mb-3 d-flex f-14 f-i"><span class="fa fa-cubes wd-30"></span> Request Form</h5>
                    </div>
                    
                </div>
                <div class="w-100 btn-group">
                    <input type="text" name="" id="g_search" class="form-control" placeholder="Search code or description" list="consumables_">
                    <datalist id="consumables_">
                        <!-- Data List here -->
                    </datalist>
                </div>
                <div id="search_results" class="mt-2"></div>
                <div id="log_consumable_info">
                    <label>Code: <b id="log_consumables_code"></b></label><br>
                    <label>Description: <span id="log_consumables_description"></span></label><br>
                    <label>Remaining Stock: <span id="log_consumables_stock"></span></label>
                    <br>
                    <span>Status: </span>
                    <span id="log_consumable_badge_danger" hidden class="badge bg-danger">Out of Stock</span>
                    <span id="log_consumable_badge_warning" hidden class="badge bg-warning">Low Stock</span>
                    <span id="log_consumable_badge_success" hidden class="badge bg-success">In Stock</span>
                </div>
                <hr>

                <label for="requested_quantity" class="mb-2">Quantity</label>
                <input type="number" name="" id="requested_quantity" class="form-control" min="0" value="0">
                <div class="row mt-2 mb-2">
                    <div class="col-6">
                        <label for="date_today" class="mb-2">Date</label>
                        <input type="date" name="" id="date_today" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="time_today" class="mb-2">Time</label>
                        <input type="time" name="" id="time_today" class="form-control">
                    </div>
                </div>
                <label for="remarks" class="mb-2">Remarks</label>
                <textarea name="" id="remarks" rows="5" class="form-control" placeholder="Aa"></textarea>
                <div class="row mt-2">
                    <div class="col-6">
                        <label for="user_id" class="mb-2">User ID</label>
                        <input type="text" name="" id="user_id" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="passkey" class="mb-2">Passkey</label>
                        <input type="password" type="number" name="" id="passkey" class="form-control passkey">
                    </div>
                </div>
                <div class="w-100 d-flex justify-content-end">
                    <button id="cancel_btn" class="btn btn-secondary mt-3"><span class="fa fa-remove"></span> CANCEL</button>
                    <button id="submit_btn" class="btn btn-primary mt-3 ms-1"><span class="fa fa-save"></span> SUBMIT</button>    
                </div>
                <h6 class="f-i text-danger alert-danger p-3 rounded-3 mb-0 mt-4">*Note: If your request is not approved after an hour, please send a message to wherever you sent your request.</h6>
            </div>
        </div>
        <h6 class="copyright f-10 text-secondary"></h6>
        <script src="../assets/js/sole.js?v=<?= filemtime('../assets/js/sole.js') ?>"></script>
        <script src="../assets/js/ipf.js?v=<?= filemtime('../assets/js/ipf.js') ?>"></script>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/popper/popper.min.js"></script>
        <script src="../assets/js/datatables/datatables.min.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.splash/splash.js?v=<?= filemtime('../assets/js/sole.splash/splash.js') ?>"></script>
        <script src="../assets/js/quagga/quagga.min.js"></script>
        <script src="../assets/js/script.js?v=<?= filemtime('../assets/js/script.js') ?>"></script>
        <script src="../assets/js/consumables_log.js?v=<?= filemtime('../assets/js/consumables_log.js') ?>"></script>
        <script src="../assets/js/modal_alert.js?v=<?= filemtime('../assets/js/modal_alert.js') ?>"></script>
    </body>
</html>
