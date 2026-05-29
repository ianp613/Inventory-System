if(document.querySelector("#youtube_karaoke_")){

    var room_creation               = document.querySelector("#room_creation")
    var yk_room                     = document.querySelector("#yk_room")
    var yk_reserve                  = document.querySelector("#yk_reserve")

    var yk_create_room              = document.querySelector("#yk_create_room")
    var yk_room_name                = document.querySelector("#yk_room_name")
    var yk_generated_key            = document.querySelector("#yk_generated_key")
    var yk_enter_room               = document.querySelector("#yk_enter_room")
    var yk_reservation_control      = document.querySelector("#yk_reservation_control")

    var params = new URLSearchParams(window.location.search)
    if(params.has('yk')){
        if(params.get('yk') == "room"){
            room_creation.hidden    = true
            room_creation.classList.remove("d-flex")
            yk_room.hidden          = false
            yk_reserve.classList.remove("d-flex")
            yk_reserve.hidden       = true
        }else if(params.get("yk") == "reserve"){
            room_creation.hidden    = true
            room_creation.classList.remove("d-flex")
            yk_room.hidden          = true
            yk_reserve.classList.add("d-flex")
            yk_reserve.hidden       = false
        }else{
            room_creation.hidden    = false
            room_creation.classList.add("d-flex")
            yk_room.hidden          = true
            yk_reserve.classList.remove("d-flex")
            yk_reserve.hidden       = true
        }
    }else{
        room_creation.hidden    = false
        room_creation.classList.add("d-flex")
        yk_room.hidden          = true
        yk_reserve.classList.remove("d-flex")
        yk_reserve.hidden       = true
    }
    
    yk_create_room.addEventListener("click", e => {
        if(!yk_room_name.value){
            bs5.toast("warning","Please input room name.")
            return;
        }
        sole.post("../controllers/youtube_karaoke/generate_key.php",{
            yk_room_name : yk_room_name.value
        }).then(res => {
            if(res.status){
                yk_generated_key.classList.add("text-light")
                yk_generated_key.innerText = res.key
                yk_room_name.value = ""
            }
            bs5.toast(res.type,res.message,res.size)
        })
    })

    yk_enter_room.addEventListener("click", e => {
        window.open("?yk=room", "_blank");
    });

    yk_reservation_control.addEventListener("click", e => {
        console.log("ok")
        window.open("?yk=reserve", "_blank");
    });





























    // ✅ Load YouTube API
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    document.head.appendChild(tag);

    function onYouTubeIframeAPIReady() {
        console.log("✅ YouTube API Ready");
    }

    var yk_next                     = document.querySelector("#yk_next")
    var yk_reload                   = document.querySelector("#yk_reload")
    var yk_room_id                  = document.querySelector("#yk_room_id")
    var yk_room_id_display          = document.querySelector("#yk_room_id_display")

    var yk_proceed                  = document.querySelector("#yk_proceed")
    var yk_room_id_input            = document.querySelector("#yk_room_id_input")

    var yk_room_name_display        = document.querySelector("#yk_room_name_display")
    var yk_room_name_container      = document.querySelector("#yk_room_name_container")

    var yk_np_title                 = document.querySelector("#yk_np_title")
    var yk_np_singer                = document.querySelector("#yk_np_singer")

    var yk_ns_title                 = document.querySelector("#yk_ns_title")
    var yk_ns_singer                = document.querySelector("#yk_ns_singer")

    var yk_iFrame                   = document.querySelector("#yk_iFrame")

    var intervalID;
    var currentSong                 = null;
    var currentSong_singer          = null;
    var currentSong_id              = null;
    var nextSong                    = null;
    var nextSong_singer             = null;
    var nextSong_id                 = null;
    var playing                     = false;
    var player;                     // ✅ Add YouTube player instance

    const room_id_modal = new bootstrap.Modal(document.getElementById('room_id_modal'),unclose);

    if(localStorage.getItem("room_id") == null && yk_room.hidden == false){
        room_id_modal.show();
    }else{
        yk_room_id_display.innerText = localStorage.getItem("room_id")
        yk_room_name_display.innerText = localStorage.getItem("room_name")
        yk_room_name_container.hidden = false
        requestSong()
    }

    yk_next.addEventListener("click", e => {
        if(currentSong_id != null){
            sole.post("../controllers/youtube_karaoke/delete_song.php",{
                id : currentSong_id
            }).then(res => {
                yk_iFrame.innerHTML = ""
                playing = false
                if (player) player.stopVideo()  // ✅ Stop current video
                console.log(res)
            })
            requestSong()
        }
    })

    yk_reload.addEventListener("click", e => {
        playing = false
        if (player) player.stopVideo()  // ✅ Stop current video
        requestSong()
    })

    yk_room_id.addEventListener("click", e => {
        room_id_modal.show()
    })

    yk_proceed.addEventListener("click", e => {
        sole.post("../controllers/youtube_karaoke/validate_id.php", {
            id : yk_room_id_input.value
        }).then(res => {
            if(res.status){
                console.log(res)
                yk_room_id_display.innerText = yk_room_id_input.value.toUpperCase()
                localStorage.setItem("room_id",yk_room_id_input.value.toUpperCase())
                localStorage.setItem("room_name",res.name)
                yk_room_name_display.innerText  = res.name
                yk_room_name_container.hidden   = false
                yk_room_id_input.value          = ""
                room_id_modal.hide()
                playing                         = false
                yk_iFrame.innerHTML             = ""
                if (player) player.stopVideo()  // ✅ Stop current video
                requestSong()
            }else{
                bs5.toast(res.type,res.message)
            }
        })
    })

    // ✅ Detect when video ends
    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.ENDED) {
            yk_next.click();
        }
    }

    function requestSong(){
        if(yk_room.hidden == true){
            return
        }
        requestSong_Stop();
        intervalID = setInterval(() => {
            console.log("Requesting")
            sole.post("../controllers/youtube_karaoke/request_song.php",{
                id : localStorage.getItem("room_id")
            }).then(res => {
                if(res.length){
                    currentSong             = res[0].yt_link
                    currentSong_singer      = res[0].yk_singer
                    currentSong_id          = res[0].id
                    yk_np_singer.innerText  = res[0].yk_singer
                    if(!playing){
                        loadYT_Song(res[0].yt_link).then(result => {
                            if (result.success) {
                                yk_np_title.innerText   = result.title
                                yk_iFrame.innerHTML     = result.iframe;
                                console.log("Author:", result.author);
                                playing = true

                                // ✅ Initialize player after iframe loads
                                setTimeout(() => {
                                    player = new YT.Player('youtube-player', {
                                        events: {
                                            'onStateChange': onPlayerStateChange
                                        }
                                    });
                                }, 500);
                            } else {
                                playing = false
                                yk_np_title.innerText   = "⚠️ Video not found or is private."
                                yk_iFrame.innerHTML     = "";
                                console.error("Error:", result.error);
                            }
                        })
                    }
                }else{
                    currentSong                 = null
                    yk_np_title.innerText       = "No song yet. Use the reservation control to add song."
                    yk_np_singer.innerText      = "---"
                    yk_iFrame.innerHTML         = ""
                }
                if(res.length > 1){
                    nextSong                = res[1].yt_link
                    nextSong_singer         = res[1].yk_singer
                    nextSong_id             = res[1].id
                    yk_ns_singer.innerText  = res[1].yk_singer
                    yk_ns.hidden            = false
                    yk_next.disabled        = false
                    loadYT_Song(res[1].yt_link).then(result => {
                        if (result.success) {
                            yk_ns_title.innerText   = result.title
                            console.log("Author:", result.author);

                        } else {
                            yk_ns_title.innerText   = "⚠️ Video not found or is private."
                            console.error("Error:", result.error);
                        }
                    })
                    requestSong_Stop()
                }else{
                    nextSong                = null
                    yk_ns.hidden            = true
                    yk_next.disabled        = true
                    yk_ns_title.innerText   = "---"
                    yk_ns_singer.innerText  = "---"
                }
            })
        }, 2000);
    }

    function requestSong_Stop(){
        if (intervalID !== null) {
            clearInterval(intervalID);
            intervalID = null;
        }
    }

    async function loadYT_Song(link) {
        const videoId = extractVideoId(link);
        
        if (!videoId) {
            return {
                success: false,
                error: "Invalid YouTube URL"
            };
        }

        try {
            // Fetch video title from YouTube oEmbed API (no API key needed)
            const cleanUrl = `https://www.youtube.com/watch?v=${videoId}`;
            const response = await fetch(`https://www.youtube.com/oembed?url=${encodeURIComponent(cleanUrl)}&format=json`);
            
            if (!response.ok) {
                throw new Error("Video not found or is private");
            }

            const data = await response.json();

            // ✅ Add id="youtube-player" and enablejsapi=1 for API control
            const iframe = `<iframe id="youtube-player"
                    src="https://www.youtube.com/embed/${videoId}?enablejsapi=1&autoplay=1&mute=1" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
            </iframe>`;

            // ✅ Return both iframe and title
            return {
                success: true,
                videoId: videoId,
                title: data.title,
                author: data.author_name,
                thumbnail: data.thumbnail_url,
                iframe: iframe
            };

        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    function extractVideoId(url) {
        const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const match = url.match(regex);
        return match ? match[1] : null;
    }




















    var yk_reserve_title                = document.querySelector("#yk_reserve_title")
    var yk_reserve_link                 = document.querySelector("#yk_reserve_link")
    var yk_reserve_singer               = document.querySelector("#yk_reserve_singer")
    var yk_reserve_                     = document.querySelector("#yk_reserve_")
    var yk_reserve_room_id              = document.querySelector("#yk_reserve_room_id")
    var yk_reserve_set_id               = document.querySelector("#yk_reserve_set_id")

    if(sessionStorage.getItem("room_id") !== null){
        yk_reserve_room_id.value        = sessionStorage.getItem("room_id")
        yk_reserve_title.innerText      = sessionStorage.getItem("room_name")
    }

    if(sessionStorage.getItem("singer_name") !== null){
        yk_reserve_singer.value        = sessionStorage.getItem("singer_name")
    }

    yk_reserve_set_id.addEventListener("click", e => {
        if(!yk_reserve_room_id.value){
            bs5.toast("warning","Please input Room ID.")
            return
        }
        sole.post("../controllers/youtube_karaoke/validate_id.php", {
            id : yk_reserve_room_id.value.toUpperCase()
        }).then(res => {
            if(res.status){
                yk_reserve_title.innerText = res.name
                sessionStorage.setItem("room_id",yk_reserve_room_id.value.toUpperCase())
                sessionStorage.setItem("room_name",res.name)
                bs5.toast("success","You can now reserve a song.")
            }else{
                bs5.toast(res.type,res.message)
            }
        })
    })

    yk_reserve_.addEventListener("click", e => {
        if(sessionStorage.getItem("room_id") == null){
            bs5.toast("warning","Please set Room ID.")
            return
        }

        if(!yk_reserve_singer.value){
            bs5.toast("warning","Please input singer name.")
            return
        }
        sessionStorage.setItem("singer_name",yk_reserve_singer.value)

        let link = validateYouTubeLink(yk_reserve_link.value)

        if(link.valid){
            sole.post("../controllers/youtube_karaoke/reserve_song.php",{
                id : sessionStorage.getItem("room_id"),
                link : yk_reserve_link.value,
                singer : sessionStorage.getItem("singer_name")
            }).then(res => {
                if(res.status){
                    yk_reserve_link.value = ""
                }
                bs5.toast(res.type,res.message)
            })
        }else{
            bs5.toast("warning",link.message)
            return
        }
    })



    function validateYouTubeLink(link) {
        // ✅ Trim whitespace
        const trimmedLink = link.trim();

        // ✅ Check if empty
        if (!trimmedLink) {
            return {
                valid: false,
                message: "Please enter a YouTube link",
                videoId: null
            };
        }

        // ✅ Extract video ID using regex
        const videoId = extractVideoId(trimmedLink);

        if (!videoId) {
            return {
                valid: false,
                message: "Invalid YouTube link format. Try: https://www.youtube.com/watch?v=VIDEO_ID or youtu.be/VIDEO_ID",
                videoId: null
            };
        }

        // ✅ Validate video ID (should be 11 characters)
        if (videoId.length !== 11) {
            return {
                valid: false,
                message: "Invalid YouTube video ID. Video ID should be 11 characters.",
                videoId: null
            };
        }

        // ✅ Check for valid characters in video ID (alphanumeric, dash, underscore)
        if (!/^[a-zA-Z0-9_-]{11}$/.test(videoId)) {
            return {
                valid: false,
                message: "Invalid YouTube video ID format",
                videoId: null
            };
        }

        return {
            valid: true,
            message: "✅ Valid YouTube link",
            videoId: videoId
        };
    }

    function extractVideoId(url) {
        const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const match = url.match(regex);
        return match ? match[1] : null;
    }
}


