var splashMessage = "Loading Content...";
var splashTimer = 1000;
var splashDisplay = true;
var splashType = "box";
var splashTheme = true;
var plateClass = "splash";
var splashIndex = 1000;
function initialize_splash_screen(Message = null, Timer = null, Type = null, Theme = true){
    if(Message){
        splashMessage = Message
    }
    if(Timer){
        splashTimer = Timer
    }
    if(!Theme){
        splashTheme = Theme
    }
    if(Type){
        splashType = Type
    }
    create_splash()
    splash()
}
function create_splash(){
    if(splashTheme){
        plateClass = "splash"
    }else{
        plateClass = "splash splash-dark"
    }
    if(splashType == "marble"){
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-marbles\">"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                    "</div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        )
    }else if(splashType == "crystal"){
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-crystal\"></div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        ) 
    }else if(splashType == "progressbar"){
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-progress\">"+
                        "<div style=\"animation: progressbar " + ((splashTimer - 1000) / 1000) + "s linear infinite;\"></div>"+
                    "</div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        ) 
    }else if(splashType == "wave"){
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-waves\">"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                        "<div></div>"+
                    "</div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        ) 
    }else{
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-box\"></div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        )    
    }
}
function increase_time(){
    if(splashIndex < (splashTimer - 1000)){
        splashIndex = splashIndex + 1000
    }else{
        splashDisplay = false
    }
}
function remove_splash(){
    var a = document.getElementsByClassName("splash")
    a[0].remove()
}
function splash(){
    if(splashDisplay){
        setTimeout(function onTick() {
            increase_time()
            splash()
        }, 1000);     
    }else{
        remove_splash()
    }
}