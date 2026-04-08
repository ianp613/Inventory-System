<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>File Browser</title>
        <link rel="shortcut icon" href="../assets/img/logo-ico.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/datatables/datatables.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/colorpicker.css">
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body class="light">
        <div class="w-100 p-3">
            <div class="d-flex w-100 mt-4">
                <img id="browser_icon" class="wd-60 ht-60 me-2" style="margin-top: -23px;" src="../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-6.ico" alt="browser-icon" srcset="">
                <h5 class="text-secondary mb-3 ms-2 d-flex"><div></div><span class="fa fa-folder mt-1"></span> <span class="ms-2">WIFI TEAM | FILE BROWSER</span></h5>
            </div>
            <hr class="mb-1">
            <div class="d-flex w-100">
                <span class="fa fa-desktop ft-bolder text-primary mt-2"></span>
                <a href="#" id="root_folder" class="btn btn-sm fw-bolder text-primary">Wifi_Files</a>

                <div class="w-100 d-flex justify-content-between">
                    <div id="navigation_container" class="d-flex flex-wrap">
                        <!-- Navigation Folder Here -->
                    </div>
                    <span hidden id="ellipsis_btn" class="fa fa-ellipsis-v me-2 mt-1 btn btn-sm"></span>
                    <span id="ff_select_cancel" class="fa fa-remove mt-2 me-2"></span>
                </div>
            </div>
            <hr class="mt-1 mb-1">
            <div id="file_folder_container" class="row p-2 mb-5">
                <!-- Files and Folders Here -->
            </div>
            <div hidden id="ff_options" class="ff-options">
                <div id="ff_option_copy" class="copy_btn ff-option-disabled"><span class="fa fa-copy"></span> Copy</div>
                <div id="ff_option_move" class="copy_btn ff-option-disabled"><span class="fa fa-scissors"></span> Move</div>
                <div id="ff_option_rename" class="copy_btn ff-option-disabled"><span class="fa fa-i-cursor"></span> Rename</div>
                <div id="ff_option_delete" class="copy_btn ff-option-disabled"><span class="fa fa-trash"></span> Delete</div>
            </div>
        </div>
        <h6 class="copyright f-10 text-secondary"></h6>
        <?php include("modals/file-browser.php"); ?>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
        <script src="../assets/js/file-browser.js"></script>
    </body>
</html>
