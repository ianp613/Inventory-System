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
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body class="bg-dark text-light">
        <div class="modal-header ht-50">
            <h6>PING</h6>
            <div class="row">
                <div class="input-group mb-1" style="width: 300px;">
                    <label for="target" class="col-sm-2 col-form-label col-form-label-sm">Target:</label>
                    <input class="form-control form-control-sm" type="text" name="" id="target">
                    <button id="asdd_target" class="btn btn-success btn-sm"><span class="fa fa-plus"></span></button> 
                </div>
                <div class="input-group" style="width: 300px;">
                    <label for="group" class="col-sm-2 col-form-label col-form-label-sm">Group:</label>
                    <select class="form-control form-control-sm" name="" id="group">
                        <option value="">-- Select Group --</option>
                    </select>
                    <button id="add_group" class="btn btn-success btn-sm"><span class="fa fa-plus"></span></button> 
                    <button isd="delete_group" class="btn btn-danger border-left btn-sm"><span class="fa fa-trash"></span></button> 
                </div>    
            </div>
        </div>

        <div class="row" id="ping_container">
            <!-- PING STATISTICS HERE -->
        </div>
        <h6 class="copyright f-10 text-secondary"><i>Copyright 2025 @ Wifi Team</i></h6>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/popper/popper.min.js"></script>
        <script src="../assets/js/datatables/datatables.min.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.splash/splash.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/ping.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
        <script src="../assets/js/script.js"></script>
    </body>
</html>
