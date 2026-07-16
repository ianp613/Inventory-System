<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Post It Board</title>
        <link rel="shortcut icon" href="../assets/img/logo-icore-ico.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/datatables/datatables.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/colorpicker.css">
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
<body>
    <div style="border-radius: 10px;" class="m-2 p-3 w-10 alert-secondary sticky-top">
        <h5 class="mb-0 pb-0 fw-bolder">POST IT BOARD</h5>
        <h6 class="mb-0 pb-0 mt-0 pt-0 f-i f-15">Share your thoughts, express your feelings, and make someone smile today. Connect with the people who matter most in a simple and meaningful way.</h6>
        <h6 class="mt-0 pt-0 f-i fw-bolder f-15">NO HATE, JUST LOVE ❤️</h6>            
    </div>
    <img src="../assets/img/ddc.png" class="wd-60 p-2 alert-secondary position-fixed bottom-0 start-0" style="z-index: 1000; border: solid 1px #002c49; margin-left: -1px; margin-bottom: -1px; border-top-right-radius: 10px;" alt="">
    <div style="border-radius: 10px;" class="m-2 p-3 w-10 bg-light">
        <div class="w-100 d-flex justify-content-between">
            <h6 class="fw-bolder mt-1"><span class="fa fa-envelope mt-0 pt-0"></span> MESSAGE BOARD</h6>
            <button data-bs-toggle="modal" data-bs-target="#create_post" class="btn btn-sm btn-primary rounded-pill"><span class="fa fa-plus"></span> CREATE POST</button>
        </div>
        <hr>
        <div class="row" id="message_board">
            <!-- POST HERE -->
        </div>
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
    <div class="modal fade" id="create_post" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6><span class="fa fa-plus"></span> Create Post</h6>
                </div>
                <div class="modal-body">
                    <label for="name">Sender</label>
                    <input type="text" name="" id="name" class="form-control mt-1 mb-2" placeholder="Input your name or use code name">
                    <label for="recipient">Recipient</label>
                    <input type="text" name="" id="recipient" class="form-control mt-1 mb-2" placeholder="Input name of recipient">
                    <label for="message">Message</label>
                    <textarea class="form-control mt-1" rows="6" name="" id="message" placeholder="Aa"></textarea>
                </div>
                <div class="modal-footer">
                    <button data-bs-dismiss="modal" class="btn btn-secondary btn-sm"><span class="fa fa-remove"></span> Cancel</button>
                    <button id="submit_btn" class="btn btn-primary btn-sm"><span class="fa fa-send"></span> Post</button>
                </div>
            </div>
        </div>
    </div>
    <h6 class="copyright f-10 text-secondary"></h6>
    <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
    <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="../assets/js/sole.js?v=<?= filemtime('../assets/js/sole.js') ?>"></script>
    <script src="../assets/js/modal_alert.js?v=<?= filemtime('../assets/js/modal_alert.js') ?>"></script>
    <script src="../assets/js/post-it.js?v=<?= filemtime('../assets/js/post-it.js') ?>"></script>
    <script src="../assets/js/script.js?v=<?= filemtime('../assets/js/script.js') ?>"></script>
</body>
</html>