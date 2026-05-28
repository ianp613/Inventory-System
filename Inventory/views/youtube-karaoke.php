<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Youtube Karaoke</title>
        <link rel="shortcut icon" href="../assets/img/artisanry/youtube-karaoke.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/datatables/datatables.min.css">
        <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sole.splash/splash.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/theme/Default.css">
    </head>
    <body id="youtube_karaoke_">
        <div hidden id="room_creation" class="w-100 justify-content-center">
            <div class="wd-500 bg-dark p-4 rounded-3 text-secondary" style="position: absolute; top: 50%; transform: translateY(-50%);">
                <div class="d-flex">
                    <div class="bg-light p-2" style="border-radius: 100px;">
                        <img  class="wd-30" src="../assets/img/artisanry/youtube-karaoke.png" alt="">
                    </div>
                    <h5 class="text-danger mt-2 ms-2 fw-bolder">YOUTUBE <span class="text-light">KARAOKE</span></h5>
                </div>
                <hr>
                <input id="yk_room_name" type="text" name="" id="" class="form-control mb-3" placeholder="Input Room Name">
                <div class="d-flex justify-content-between">
                    <h6 class="mt-3 fw-bolder">ROOM ID: <span id="yk_generated_key">Auto-generated</span></h6>                    
                    <button class="btn btn-danger" id="yk_create_room">Generate Room ID</button>
                </div>
                <hr>
                <button class="btn btn-dark w-100 mb-2"><span class="fa fa-desktop" id="yk_enter_room"></span> Enter Karaoke Room</button>
                <button class="btn btn-dark w-100"><span class="fa fa-book" id="yk_reservation_control"></span> Song Reservation Control</button>
                <p class="w-100 text-center f-13 text-light f-i mt-4 mb-0">Youtube Karaoke v1.0</p>
            </div>
        </div>

        <div hidden id="yk_room" class="w-100 vh-100 bg-dark text-secondary p-3">
            <div class="row">
                <div class="d-flex mb-3">
                    <div class="bg-light p-2" style="border-radius: 100px;">
                        <img  class="wd-30" src="../assets/img/artisanry/youtube-karaoke.png" alt="">
                    </div>
                    <h5 class="text-danger mt-2 ms-2 fw-bolder">YOUTUBE <span class="text-light">KARAOKE</span></h5>
                </div>
                <div class="col-md-9">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/Y3akxv_O_KE" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card_yk">
                        <h6 style="color: #d3d3d3">NOW PLAYING</h6>
                        <h6>You Won’t Believe This Place Exists in Samar, Philippines</h6>
                        <h6 class="text-danger">Singer: <span style="color: #d3d3d3">PAUL IAN DUMDUM</span></h6>                        
                        <h6 class="mt-4" style="color: #d3d3d3">NEXT SONG</h6>
                        <h6>You Won’t Believe This Place Exists in Samar, Philippines</h6>
                        <h6 class="text-success">Singer: <span style="color: #d3d3d3">JUAN DELA CRUZ</span></h6>                        
                    </div>
                    <div class="card_yk mt-3 d-flex gap-2">
                        <button>Next <span class="fw-bolder fa fa-step-forward"></span></button>
                        <button>Reload <span class="fw-bolder fa fa-refresh"></span></button>
                        <button>Room ID <span class="fw-bolder fa fa-desktop"></span></button>
                    </div>
                </div>
            </div>
        </div>

        <div hidden id="yk_reserve">

        </div>
















        <style>
            .card_yk{
                padding: 20px;
                border: solid 1px #677079;
                background-color: #30363b;
                border-radius: 15px;
            }
            .card_yk button{
                background-color: #41484e;
                color: #e9e9e9;
                border: solid 1px #e9e9e9;
                outline: 0px;
                padding: 10px;
                font-size: 14px;
                width: 100px;
                border-radius: 8px;
                transition: ease 0.3s;
            }
            .card_yk button:hover{
                border: solid 1px #ff2020;
                color: #ff2020;
                background-color: #30363b;

            }
            .video-wrapper iframe {
                width: 100%;
                height: auto;
                aspect-ratio: 16 / 9;
                border-radius: 8px;
            }
        </style>
        
        <h6 class="copyright f-10 text-secondary"></h6>
        <script src="../assets/js/jquery/jquery-3.7.1.js"></script>
        <script src="../assets/js/popper/popper.min.js"></script>
        <script src="../assets/js/datatables/datatables.min.js"></script>
        <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="../assets/js/sole.splash/splash.js"></script>
        <script src="../assets/js/quagga/quagga.min.js"></script>
        <script src="../assets/js/sole.js"></script>
        <script src="../assets/js/youtube_karaoke.js"></script>
        <script src="../assets/js/script.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
    </body>
</html>
