var name_           = document.getElementById("name")
var recipient_      = document.getElementById("recipient")
var message_        = document.getElementById("message")
var submit_btn      = document.getElementById("submit_btn")
var message_board   = document.getElementById("message_board")

const create_post = new bootstrap.Modal(document.getElementById('create_post'),unclose);

document.getElementById("create_post").addEventListener('shown.bs.modal',function (){
    name_.focus()
})

loadMessageBoard()

submit_btn.addEventListener("click", e => {
    if(!name_.value || !recipient_.value || !message_.value){
        alert("Please fill up all field.")
        return
    }
    sole.post("../../controllers/post-it/create_post.php", {
        name        : name_.value,
        recipient   : recipient_.value,
        message     : message_.value
    }).then(res => {
        if(res.status){
            name_.value         = ""
            recipient_.value    = ""
            message_.value      = ""
        }
        alert(res.message)
        create_post.hide()
        loadMessageBoard()
    })
})

function loadMessageBoard(){
    sole.get("../../controllers/post-it/get_post.php").then(res => {
        message_board.innerHTML = ""
        res.forEach(post => {
            message_board.insertAdjacentHTML("beforeend",
                `<div class="modal-body col-md-3">`+
                    `<div class="alert-primary p-3" style="border-radius: 10px;">`+
                        `<h6 class="f-15 mb-0 pb-0 mt-0 pt-0"><b>Sender:</b> ${post.name}</h6>`+
                        `<h6 class="f-15 mb-0 pb-0 mt-0 pt-0"><b>To:</b> ${post.recipient}</h6>`+
                        `<hr>`+
                        `<h6 class="f-15 mb-0 pb-0 mt-0 pt-0">${post.message}</h6>`+
                    `</div>`+
                `</div>`
            )
        });
    })
}