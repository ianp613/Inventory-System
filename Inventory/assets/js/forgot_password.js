if(document.getElementById("forgot_password")){
    var userid = document.getElementById("userid")
    var getcode_btn = document.getElementById("getcode_btn")
    var code = document.getElementById("code")
    var new_password = document.getElementById("new_password")
    var confirm_password = document.getElementById("confirm_password")
    var login_btn = document.getElementById("login_btn")

    var sent_to = document.getElementById("sent_to")
    var confirm_code = document.getElementById("confirm_code")
    var code_message = document.getElementById("code_message")

    var ready_state = document.getElementById("ready_state")
    var sending_state = document.getElementById("sending_state")
    

    var user_id = null;

    userid.focus()

    getcode_btn.addEventListener("click",function(){
        if(userid.value){
            userid.setAttribute("readonly","true")

            ready_state.setAttribute("hidden","true")
            sending_state.removeAttribute("hidden")

            sole.post("../../controllers/generate_code.php",{
                userid: userid.value
            }).then(res => validateResponse(res,"get_code"))
        }else{
            bs5.toast("warning","Please input user ID or Email.")
        }
    })

    login_btn.addEventListener("click",function(){
        if(code.value){
            if(new_password.value && confirm_password.value){
                if(new_password.value == confirm_password.value){
                    sole.post("../../controllers/change_password.php",{
                        id: user_id,
                        code: code.value,
                        password: new_password.value
                    }).then(
                        res => {
                            if(res.status){
                                code.value = ""
                                new_password.value = ""
                                confirm_password.value = ""

                                confirm_code.setAttribute("hidden","true")
                                sent_to.removeAttribute("hidden")

                                sending_state.setAttribute("hidden","true")
                                ready_state.removeAttribute("hidden")

                                bs5.toast(res.type,res.message,res.size) 
                                setTimeout(() => {
                                    window.location.replace("login.php");
                                }, 2000);  
                            }else{
                                bs5.toast("error","Code is invalid.")    
                            }
                        }
                    )
                }else{
                    bs5.toast("warning","Passwords didn't match.")
                }
            }else{
                bs5.toast("warning","Please input passwords.")
            }
        }else{
            bs5.toast("warning","Code can't be empty.")
        }
    })

    function validateResponse(res,func){
        if(res.status){
            if(func == "get_code"){
                userid.value = ""
                userid.removeAttribute("readonly")
                sent_to.setAttribute("hidden","true")
                confirm_code.removeAttribute("hidden")
                
                code_message.innerText = res.message
                user_id = res.user[0]["id"]
                code.focus()
            }
        }else{
            userid.removeAttribute("readonly")
            sending_state.setAttribute("hidden","true")
            ready_state.removeAttribute("hidden")
            
            bs5.toast(res.type,res.message,res.size)
        }
    }


}