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
        <link rel="stylesheet" href="../assets/css/file-browser.css">
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body class="light">
        <div id="ff_login" class="w-100 p-3">
            <div class="d-flex justify-content-between w-100 mt-4">
                <div class="d-flex">
                    <img id="browser_icon" class="wd-60 ht-60 me-2" style="margin-top: -23px;" src="../../assets/img/labubu/labubu-folder-icons/labubu-folder-icon-6.ico" alt="browser-icon" srcset="">
                    <h5 class="text-secondary mb-3 ms-2 d-flex"><div></div>
                        <span class="" style="margin-top: -10px;">
                            <h5 class="m-0 p-0 fw-bold"><span class="fa fa-group wd-20"></span> <span id="browser_name"></span></h5>
                            <h5 class="f-i m-0 p-0 f-15"><span class="fa fa-cubes wd-20"></span> File Browser</h5>
                        </span>
                    </h5>    
                </div>
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
                <div id="ff_option_copy" class="ff-option-disabled"><span class="fa fa-copy"></span> Copy</div>
                <div id="ff_option_move" class=" ff-option-disabled"><span class="fa fa-scissors"></span> Move</div>
                <div id="ff_option_rename" class="ff-option-disabled"><span class="fa fa-i-cursor"></span> Rename</div>
                <div id="ff_option_delete" class="ff-option-disabled"><span class="fa fa-trash"></span> Delete</div>
                <div id="ff_option_download" class="ff-option-disabled">
                    <span id="ff_option_download_download" class="fa fa-download"></span>
                    <h6 hidden id="ff_option_download_spinner" class="wd-15 ht-15 me-1 mt-0 mb-0 spinner-border text-secondary" role="status">
                    </h6> Download</div>
            </div>
            <div hidden id="ff_options_copy" class="ff-options-copy">
                <div id="ff_option_copy_cancel" class=""><span class="fa fa-remove"></span> Cancel</div>
                <div id="ff_option_copy_paste" class="">
                    <span id="ff_option_copy_paste_clipboard" class="fa fa-clipboard"></span>
                    <h6 hidden id="ff_option_copy_paste_spinner" class="wd-15 ht-15 me-1 mt-0 mb-0 spinner-border text-secondary" role="status">
                    </h6> Copy Here
                </div>
            </div>
            <div hidden id="ff_options_move" class="ff-options-move">
                <div id="ff_option_move_cancel" class=""><span class="fa fa-remove"></span> Cancel</div>
                <div id="ff_option_move_paste" class="">
                    <span id="ff_option_move_paste_clipboard" class="fa fa-clipboard"></span>
                    <h6 hidden id="ff_option_move_paste_spinner" class="wd-15 ht-15 me-1 mt-0 mb-0 spinner-border text-secondary" role="status">
                    </h6> Move Here
                </div>
            </div>
        <?php include("modals/file-browser.php"); ?>

        </div>
        <div hidden id="ff_login_card" class="ff-login-card">
            <div class="modal-header pt-3 pb-2">
                <h6>LOGIN TO YOUR ACCOUNT</h6>
            </div>
            <div class="modal-body">
                <input id="ff_login_userid" type="text" name="" id="" class="form-control mb-2" placeholder="User ID">
                <input id="ff_login_password" type="password" name="" id="" class="form-control" placeholder="Password">
            </div>
            <div class="modal-footer">
                <button id="ff_login_btn" class="btn btn-secondary w-100"><span class="fa fa-sign-in"></span> LOGIN</button>
            </div>
            <h6 class="text-center mb-0 mt-2 f-12 f-i fw-bold">Intergrated with Inventory System Credentials</h6>
            <p class="text-center mt-0 f-12 f-i">File Browser v.1</p>
        </div>
        <div hidden  id="ff_user_container" class="position-fixed bottom-0 ms-1 bg-dark text-light pt-2 ps-3 pe-3" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <h6 class="fw-bolder" id="ff_user">User: -------------------------</h6>
        </div>

        <style>
            .modal-content{
                border-radius: 15px !important;
                padding: 5px;
                padding-top: 15px;
                background-color: #ffffffd7 !important;
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
        <script src="../assets/js/file-browser.js"></script>
    </body>
</html>
