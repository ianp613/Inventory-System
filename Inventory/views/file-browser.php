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
                    <span class="fa fa-ellipsis-v me-2 btn btn-sm"></span> 
                </div>
                

            </div>
            <hr class="mt-1 mb-1">
            <div id="file_folder_container" class="row p-2">
                <!-- Files and Folders Here -->
            </div>
        </div>
        <h6 class="copyright f-10 text-secondary"></h6>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
        <script src="../assets/js/file-browser.js"></script>
    </body>
</html>
