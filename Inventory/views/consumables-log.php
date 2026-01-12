<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory System</title>
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
            <div class="wd-550 p-4">
                <h5 class="text-secondary mb-3 d-flex"><div id="g_name_display"></div><span class="fa fa-cubes"></span> Consumables</h5>
                <div class="w-100 btn-group">
                    <input type="text" name="" id="g_search" class="form-control" placeholder="Search code or description">
                    <button class="btn btn-sm alert-dark btn-dark wd-40"><span class="fa fa-search"></span></button>
                </div>
                <div id="search_results" class="mt-2"></div>
                <div id="log_consumable_info">
                    <label>Code: <b id="log_consumables_code"></b></label><br>
                    <label>Description: <span id="log_consumables_description"></span></label><br>
                    <label>Remaining Stock: <span id="log_consumables_stock"></span></label>
                    <br>
                    <span>Status: </span>
                    <span id="log_consumable_badge_danger" hidden class="badge bg-danger">Low Stock</span>
                    <span id="log_consumable_badge_success" hidden class="badge bg-success">In Stock</span>
                </div>
                <hr>

                <label for="quantity_deduction" class="mb-2">Quantity</label>
                <input type="number" name="" id="quantity_deduction" class="form-control" min="0" value="0">
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
                        <input type="number" name="" id="passkey" class="form-control passkey">
                    </div>
                </div>
                <div class="w-100 d-flex justify-content-end">
                    <button id="cancel_btn" class="btn btn-secondary mt-3"><span class="fa fa-remove"></span> CANCEL</button>
                    <button id="submit_btn" class="btn btn-primary mt-3 ms-1"><span class="fa fa-save"></span> SUBMIT</button>    
                </div>
            </div>
        </div>
        <h6 class="copyright f-10 text-secondary"></h6>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/popper/popper.min.js"></script>
        <script src="../assets/js/datatables/datatables.min.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.splash/splash.js"></script>
        <script src="../assets/js/quagga/quagga.min.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/script.js"></script>
        <script src="../assets/js/consumables_log.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
    </body>
</html>
