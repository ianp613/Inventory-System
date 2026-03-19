if(document.getElementById("login")){
    // localStorage.removeItem("email");
    // localStorage.removeItem("userid");
    // localStorage.removeItem("yourname");
    // localStorage.removeItem("username");
    // localStorage.removeItem("privileges");

    if(localStorage.getItem("inactivity")){
        bs5.toast("info","<div class=\"w-100 mb-2 text-primary\"><span class=\"fa fa-info\"></span></div>You've been logged out of your account due to inactivity. <br> Please log in again to resume your work.","lg", true, false)
        localStorage.removeItem("inactivity")
    }
    localStorage.clear();
    let userid = document.getElementById("userid")
    let password = document.getElementById("password")
    let login_btn = document.getElementById("login_btn")
    let login_alert = document.getElementById("login_alert")
    let login_sound = false
    let rem_user_ = false

    sole.post("../../controllers/settings.php")
    .then(res => {
        res["sound"] == "1" ? login_sound = true : login_sound = false;
    });
    login_alert.addEventListener("click", e => {
        login_alert.style = "display: none !important;";
        userid.value = ""
        password.value = ""
        userid.focus()
    })
    userid.focus()

    document.addEventListener("keydown", e=>{
        if(e.key == "ArrowUp"){
            if(tempText){
                chatbot_input.value = tempText
                tempText = ""
            }
        }
        if(e.key == "Enter" && document.activeElement === userid){
            login()
        }
        if(e.key == "Enter" && document.activeElement === password){
            login()
        }
        if(e.key == "Enter" && document.activeElement === chatbot_input){
            if (!e.shiftKey) {
                if(!chatbot_input.value){
                    return false
                }
                if(!send){
                    return false
                }
                chatbotSend()
            }
        }
        if (e.ctrlKey && (e.key === 'c' || e.key === 'C')) {
            i = 7000
            setTimeout(() => {
                chatbot_input.value = ""
                i = 0
            }, 10);
        }
    })

    login_btn.addEventListener("click", e => {
        login()
    })

    function login(){
        sole.post("../controllers/login.php",{
            "userid" : userid.value,
            "password" : password.value,
            "rem_user" : rem_user_
        }).then(res => {
            validateLogin(res)
        })
    }

    function validateLogin(res){
        if(res.status){
            if(res.g_member){
                bs5.toast(res.type,res.message + " " + res.user[0]["name"],res.size, true, false)    
                // login_sound ? audio.play() : null;
                login_btn.setAttribute("disabled","")
                setTimeout(() => {
                    window.location.replace("inventory.php?loc=dashboard");
                }, 3000);
            }else{
                bs5.toast(res.type,res.message,res.size, true, true)
            }
        }else{
            login_alert.style = "display: flex !important; height: 30px;"
            login_alert.innerText = res.message

            setTimeout(() => {
                login_alert.style = "display: none !important; height: 30px;"
            }, 2000);
        }
    }

    

    const chatbot = document.getElementById("chatbot")
    const bot_ico = document.getElementById("bot_ico")
    const chatbot_body = document.getElementById("chatbot_body")
    const chatbot_input = document.getElementById("chatbot_input")
    const chatbot_send = document.getElementById("chatbot_send")
    const chatbot_hide = document.getElementById("chatbot_hide")
    const chatbot_show = document.getElementById("chatbot_show")
    var user = ""
    var greet = true;
    var send = false;
    
    var speed = 30;
    var reply = []
    var reply_output = ""
    var file_inserted = true
    var tempText = ""

    scrollToBottom()

    chatbot_hide.addEventListener("click",function(){
        chatbot_show.removeAttribute("hidden")
        this.setAttribute("hidden","true")
        chatbot.classList.add("chatbot-hide")
        bot_ico.classList.add("bot-ico-hidden")
    })

    chatbot_show.addEventListener("click",function(){
        chatbot_hide.removeAttribute("hidden")
        this.setAttribute("hidden","true")
        chatbot.classList.remove("chatbot-hide")
        bot_ico.classList.remove("bot-ico-hidden")

        if(greet){
            reply = ["Good day, I am your BOT assistant. <br> To start please provide your User ID.","greetings"]
            createMessageBoxBOT(reply)
            botReply()
            chatbot_input.focus()
            greet = false
        }
    })

    chatbot_input.addEventListener('focus', scrollToBottom);
    chatbot_input.addEventListener('click', scrollToBottom);

    chatbot_send.addEventListener("click",function(){
        if(!chatbot_input.value){
            return false
        }
        if(!send){
            return false
        }
        chatbotSend()
    })

    function chatbotSend(){
        tempText = chatbot_input.value
        if(chatbot_input.value.toLowerCase() == "clear"){
            chatbot_body.innerHTML = ""
            user = ""
            reply = ["Good day, I am your BOT assistant. <br> To start please provide your User ID.","cleared"]
            speed = 40
            createMessageBoxBOT(reply)
            botReply()
            chatbot_input.focus()
            setTimeout(() => {
                chatbot_input.value = ""    
            }, 1);
            return true
        }
        var br = document.createElement("br")
        var wrapper = document.createElement("div")
        var chat_message_right = document.createElement("div")
        chat_message_right.setAttribute("class","chatbot-message-right")
        var chat_head = document.createElement("p")
        chat_head.innerText = "You"
        var chat_message = document.createElement("div")
        chat_message_right.appendChild(chat_head)
        chat_message_right.appendChild(chat_message)
        wrapper.appendChild(chat_message_right)
        chatbot_body.appendChild(wrapper)
        chatbot_body.appendChild(br)
        chat_message.innerText = chatbot_input.value

        if(user != ""){
            sole.post("../../controllers/chatbot/main.php",{
                message : chatbot_input.value
            }).then(res => {
                if(res){
                    reply = res
                    if(res[0]){
                        if (typeof reply[0] !== 'string'){
                            file_inserted = false
                        }
                        speed = 5
                        createMessageBoxBOT(res)
                        botReply()
                    }
                }
            })    
        }else{
            sole.post("../../controllers/chatbot/get_user.php",{
                userid : chatbot_input.value
            }).then(res => {
                if(res.length){
                    user = res[0]["name"]
                    reply = ["Hello " + res[0]["name"] + " how may I help you?",Math.floor(Math.random() * (10000 - 1 + 1)) + 1]
                    createMessageBoxBOT(reply)
                    botReply()
                }else{
                    reply = ["Please provide a valid User ID",Math.floor(Math.random() * (10000 - 1 + 1)) + 1]
                    createMessageBoxBOT(reply)
                    botReply()
                }
            })
        }

        
        scrollToBottom()
        setTimeout(() => {
            chatbot_input.value = ""
        }, 1);
    }

    function createMessageBoxBOT(res){
        var br = document.createElement("br")
        var wrapper = document.createElement("div")
        var chat_message_left = document.createElement("div")
        chat_message_left.setAttribute("class","chatbot-message-left")
        var chat_head = document.createElement("p")
        chat_head.innerText = "Inventory Bot"
        var chat_message = document.createElement("div")
        var tempText = ""
        chat_message.setAttribute("id",res[1])
        chat_message_left.appendChild(chat_head)
        chat_message_left.appendChild(chat_message)
        wrapper.appendChild(chat_message_left)
        chatbot_body.appendChild(wrapper)
        chatbot_body.appendChild(br)
    }


    function scrollToBottom() {
        chatbot_body.scrollTo({
            top: chatbot_body.scrollHeight,
            behavior: 'smooth'
        });
    }

    let i = 0;
    // let output = "";

    // typeWriter();
    
    
    function botReply(){
        var file = false
        var text = ""
        if (typeof reply[0] === 'string'){
            text = reply[0]
        }else{
            file = reply[0][0] == "file" ? true : false
            text = reply[0][1]
        }
        if (text.endsWith('<br>')) {
            text = text.slice(0, -4);
        }
        if (i < text.length) {
            send = false
            reply_output += text.charAt(i);

            document.getElementById(reply[1]).innerHTML = reply_output;
            i++;
            setTimeout(botReply, speed);
        }else{
            if(file && !file_inserted){
                var img = document.createElement("img")
                img.setAttribute("class","chatbot_file")
                img.src = reply[0][2]

                const link = document.createElement('a');
                link.href = reply[0][2];
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.appendChild(img)

                document.getElementById(reply[1]).insertAdjacentElement("afterend",link)
                file = false
                file_inserted = true
            }
            send = true
            reply_output = ""
            i = 0
        }
        scrollToBottom()
    }

    const pastelColors = [
        '#AEC6CF', // pastel blue
        '#FFB347', // pastel orange
        '#77DD77', // pastel green
        '#F49AC2', // pastel pink
        '#CFCFC4', // pastel gray
        '#B39EB5', // pastel purple
        '#FFD1DC', // baby pink
        '#CB99C9', // pastel lavender
        // '#FDFD96', // pastel yellow
        '#B5EAD7', // mint pastel
        '#C7CEEA'  // sky pastel
    ];

    function getUniquePastelColor() {
        return pastelColors[Math.floor(Math.random() * pastelColors.length)]
    }

    var remembered_user_container = document.getElementById("remembered_user_container")
    var remember_me = document.getElementById("remember_me")
    var users = []
    var initial_load = true
    if(sessionStorage.getItem("rem_user") !== null){
        users = sessionStorage.getItem("rem_user").split("+++")
        users.pop()
        users.forEach(user => {
            var data = user.split("|")
            var initials = data[3].split(" ")
            
            var h6 = document.createElement("h6")
            h6.innerHTML = initials[0][0] + initials[1][0] + "<span hidden class=\"remembered_user_notification_counter\">0</span>"
            h6.classList.add("remembered_user")
            h6.setAttribute("gid",data[0])
            h6.setAttribute("uid",data[1])
            h6.setAttribute("user",data[2])
            h6.setAttribute("title",data[3])
            h6.style.backgroundColor = getUniquePastelColor()
            remembered_user_container.appendChild(h6)
            remembered_user_container.hidden = false
        })
    }else{
        remembered_user_container.hidden = true
    }

    remembered_user_container.addEventListener("click", e => {
        if(e.target.tagName == "H6"){
            var rem_attemp = e.target.getAttribute("gid")+"|"+e.target.getAttribute("uid")+"|"+e.target.getAttribute("user")+"|"+e.target.getAttribute("title")
            
            if(users.includes(rem_attemp)){
                userid.value = e.target.getAttribute("user")
                rem_user_ = true
                login_btn.click()
            }else{
                bs5.toast("error","<b>System Error - Invalid Attemp - Do not try this again<b>","lg",false)
            }
        }
    })

    remember_me.addEventListener("change", e => {
        sessionStorage.setItem("remember_me",remember_me.checked)
    })

    const socket = new WebSocket("ws://localhost:8080");
    function getUpdates(){
        var msg = [
            "get"
        ]
        socket.send(msg);
    }
    socket.onmessage = function(event){
        const data = JSON.parse(event.data);
        var elements = remembered_user_container.children
        if(remembered_user_container.children.length){
            for (let i = 0; i < elements.length; i++) {
                var count = 0
                data.forEach(dat => {
                    if(dat["gid"] == elements[i].getAttribute("gid")){
                        count++
                    }
                })
                if(count){
                    if(count > parseInt(elements[i].children[0].innerText) && !initial_load){
                        bs5.toast("info","You have new Notification")
                        if(unlocked){
                            // audio.play()    
                        }
                    }
                    elements[i].children[0].innerText = count
                    elements[i].children[0].hidden = false
                }
            }
            initial_load = false
        }
    };
    setTimeout(() => {
        getUpdates()
    }, 2000);

    let unlocked = false;

    document.addEventListener("click", () => {
        unlocked = true;
    }, { once: true });
}