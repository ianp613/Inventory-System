var splashMessage = "Loading Content...";
var splashTimer = 1000;
var splashDisplay = true;
var splashImg = "../../public/assets/icons/favicon.ico";
var splashType = "beat";
var splashTheme = true;
var plateClass = "splash";
var splashIndex = 1000;
function initialize_splash_screen_img(Message = null, Timer = null, Img = null, Type = null, Theme = true){
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
    if(Img){
        splashImg = Img
    }
    create_splash_img()
    splash_img()
}
function create_splash_img(){
    if(splashTheme){
        plateClass = "splash"
    }else{
        plateClass = "splash splash-dark"
    }
    if(splashType == "progress"){
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-img\">"+
                        "<img class=\"splash-img-progress\" src=\"" + splashImg + "\" alt=\"splash-img-fade\">"+
                        "<div class=\"splash-img-progress-cover\" style=\"animation: imgprogress " + ((splashTimer - 1000) / 1000) + "s linear;\"></div>"+
                    "</div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        ) 
    }else{
        document.body.insertAdjacentHTML("afterend",
            "<div class=\"" + plateClass + "\">"+
                "<div class=\"splash-plate\">"+
                    "<div class=\"splash-img\">"+
                        "<img class=\"splash-img-fade\" src=\"" + splashImg + "\" alt=\"splash-img-fade\">"+
                    "</div>"+
                    "<h6 class=\"splash-message\">" + splashMessage + "</h6>"+
                "</div>"+
            "</div>"
        )    
    }
}
function increase_time_img(){
    if(splashIndex < (splashTimer - 1000)){
        splashIndex = splashIndex + 1000
    }else{
        splashDisplay = false
    }
}
function remove_splash_img(){
    var a = document.getElementsByClassName("splash")
    a[0].remove()
}
function splash_img(){
    if(splashDisplay){
        setTimeout(function onTick() {
            increase_time_img()
            splash_img()
        }, 1000);     
    }else{
        remove_splash_img()
    }
}