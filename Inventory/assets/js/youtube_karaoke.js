if(document.querySelector("#youtube_karaoke_")){

    var room_creation               = document.querySelector("#room_creation")
    var yk_room                     = document.querySelector("#yk_room")
    var yk_reserve                  = document.querySelector("#yk_reserve")

    var yk_create_room              = document.querySelector("#yk_create_room")
    var yk_room_name                = document.querySelector("#yk_room_name")
    var yk_generated_key            = document.querySelector("#yk_generated_key")
    var yk_enter_room               = document.querySelector("#yk_enter_room")
    var yk_reservation_control      = document.querySelector("#yk_reservation_control")

    var yk_next                     = document.querySelector("#yk_next")
    var yk_reload                   = document.querySelector("#yk_reload")
    var yk_room_id                  = document.querySelector("#yk_room_id")

    const room_id_modal = new bootstrap.Modal(document.getElementById('room_id_modal'),unclose);
    
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
        window.location.href = "?yk=room"
    })
















    yk_room_id.addEventListener("click", e => {
        room_id_modal.show()
    })



























    var params = new URLSearchParams(window.location.search)
    if(params.has('yk')){
        if(params.get('yk') == "room"){
            room_creation.hidden    = true
            room_creation.classList.remove("d-flex")
            yk_room.hidden          = false
            yk_reserve.hidden       = true
        }else if(params.get("yk") == "reserve"){
            room_creation.hidden    = true
            room_creation.classList.remove("d-flex")
            yk_room.hidden          = true
            yk_reserve.hidden       = false
        }else{
            room_creation.hidden    = false
            room_creation.classList.add("d-flex")
            yk_room.hidden          = true
            yk_reserve.hidden       = true
        }
    }else{
        room_creation.hidden    = false
        room_creation.classList.add("d-flex")
        yk_room.hidden          = true
        yk_reserve.hidden       = true
    }

}