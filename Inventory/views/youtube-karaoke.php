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
    </head>
    <body id="youtube_karaoke_" class="alert-dark">
        <div hidden id="room_creation" class="w-100 justify-content-center">
            <div class="bg-dark p-4 rounded-3 text-secondary card_yk" style="position: absolute; top: 50%; transform: translateY(-50%); width: auto;">
                <div class="d-flex">
                    <div class="bg-light p-2" style="border-radius: 100px;">
                        <img  class="wd-25" src="../assets/img/artisanry/youtube-karaoke.png" alt="">
                    </div>
                    <h5 class="text-danger mt-2 ms-2 fw-bolder">YOUTUBE <span class="text-light">KARAOKE</span></h5>
                </div>
                <hr>
                <input id="yk_room_name" type="text" name="" id="" class="form-control mb-3" placeholder="Input Room Name">
                <div class="d-flex justify-content-between">
                    <h6 class="mt-3 fw-bolder">ROOM ID: <span id="yk_generated_key">Auto-generated</span></h6>                    
                    <button id="yk_create_room">Generate ID</button>
                </div>
                <hr>
                <button class="w-100 mb-2" id="yk_enter_room"><span class="fa fa-desktop"></span> Enter Karaoke Room</button>
                <button class="w-100" id="yk_reservation_control"><span class="fa fa-book"></span> Song Reservation Control</button>
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
                    <div class="video-wrapper mb-2" id="yk_iFrame">
                        <!-- <iframe src="https://www.youtube.com/embed/Y3akxv_O_KE" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                        </iframe> -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div id="yk_room_name_container" hidden class="card_yk text-danger mb-3">
                        <h5 id="yk_room_name_display">---</h5>
                    </div>
                    <div class="card_yk">
                        <h6 style="color: #d3d3d3">NOW PLAYING</h6>
                        <h6 id="yk_np_title">No song yet. Use the reservation control to add song.</h6>
                        <h6 class="text-danger">Singer: <span style="color: #d3d3d3" id="yk_np_singer">---</span></h6>     
                        <div hidden id="yk_ns">
                            <h6 class="mt-4" style="color: #d3d3d3">NEXT SONG</h6>
                            <h6 id="yk_ns_title">---</h6>
                            <h6 class="text-success">Singer: <span style="color: #d3d3d3" id="yk_ns_singer">---</span></h6>                               
                        </div>                   
                    </div>
                    <div class="card_yk mt-3 d-flex gap-2">
                        <button disabled id="yk_next">Next <span class="fw-bolder fa fa-step-forward"></span></button>
                        <button id="yk_reload">Reload <span class="fw-bolder fa fa-refresh"></span></button>
                        <button id="yk_room_id">Room ID <span class="fw-bolder fa fa-desktop"></span></button>
                        <h3 class="text-danger mt-1" id="yk_room_id_display">-----</h3>
                    </div>
                    <div class="card_yk mt-3">
                        <div class="d-flex justify-content-between">
                            <h6>Visualizer</h6>
                            <span class="fa fa-refresh" id="vrefresh"></span>
                        </div>
                        <div class="w-100" id="vcont"></div>
                    </div>
                </div>
            </div>
        </div>

        <div hidden id="yk_reserve" class="w-100 justify-content-center">
            <div class="bg-dark p-4 rounded-3 text-secondary card_yk" style="position: absolute; top: 50%; transform: translateY(-50%); width: auto; min-width: 350px;">
                <div class="d-flex">
                    <div class="bg-light p-2" style="border-radius: 100px;">
                        <img  class="wd-25" src="../assets/img/artisanry/youtube-karaoke.png" alt="">
                    </div>
                    <h5 class="text-danger mt-2 ms-2 fw-bolder">YOUTUBE <span class="text-light">KARAOKE</span></h5>
                </div>
                <hr>
                <h5 class="text-danger" id="yk_reserve_title">⚠️ Room ID is not yet set.</h5>
                <h6>Input Youtube Link</h6>
                <textarea name="" id="yk_reserve_link" class="form-control mb-2" placeholder="https://www.youtube.com/watch?v="></textarea>
                <h6>Singer Name</h6>
                <input type="text" name="" id="yk_reserve_singer" class="form-control mb-2 fw-bold" placeholder="Input Your Name">
                <button class="w-100 fw-bold" id="yk_reserve_">Reserve Song</button>
                <hr>
                <div class="input-group">
                    <input type="text" name="" id="yk_reserve_room_id" class="form-control room-id-inp" placeholder="Room ID">
                    <button class="fw-bold" id="yk_reserve_set_id">SET ID</button>
                </div>
                <hr>
                <p class="w-100 text-center f-13 text-light f-i mt-4 mb-0">Youtube Karaoke v1.0</p>
            </div>
        </div>

        <div class="modal fade" id="room_id_modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content p-0 bg-dark">
                    <div class="card_yk m-0">
                        <h5 class="text-danger">ENTER ROOM ID</h5>
                        <input type="text" class="form-control mb-3 room-id-inp" id="yk_room_id_input">
                        <div class="d-flex justify-content-end gap-2">
                            <button data-bs-dismiss="modal" class="w-50"><span class="fa fa-remove"></span> Cancel</button>
                            <button class="w-50" id="yk_proceed"><span class="fa fa-check"></span> Proceed</button>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .card_yk{
                padding: 20px;
                border: solid 1px #677079;
                background-color: #30363b;
                border-radius: 15px;
                overflow: auto;
            }
            .modal-content{
                border-radius: 15px;
            }
            .card_yk::-webkit-scrollbar, textarea::-webkit-scrollbar{
                width: 0px;
                display: none;
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
            .card_yk button:focus{
                outline: solid 2px #ff5656;
                /* color: #ff2020; */
                background-color: #30363b;
            }
            .card_yk button:disabled{
                cursor: not-allowed;
                border: solid 1px #4d5a66;
                background-color: #30363b;
                color: #4d5a66;
            }
            .video-wrapper iframe {
                width: 100%;
                height: auto;
                aspect-ratio: 16 / 9;
                border-radius: 8px;
            }
            .room-id-inp{
                font-weight: bolder !important;
                text-transform: uppercase;
            }
            input, textarea{
                border-radius: 8px !important;
                background-color: #5a636b !important;
                color: #ffffff !important;
            }
            input::placeholder, textarea::placeholder{
                color: #b2c5d6 !important;
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
        <script src="../assets/js/visualizer.js"></script>
        <script src="../assets/js/youtube_karaoke.js"></script>
        <script src="../assets/js/script.js"></script>
        <script src="../assets/js/modal_alert.js"></script>
    </body>
</html>
