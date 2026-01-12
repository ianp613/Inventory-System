
class Sole{
    speechsynthesis = new SpeechSynthesisUtterance();
    file(url,value){
        var request = fetch(url, {
            method: "POST",
            body: value
        })
        return request.then(response => response.json())
    }
    get(url) {
        var request = fetch(url)
        return request.then(response => response.json())
    }
    post(url,value){
        var request = fetch(url,{
            method: 'POST', // Use POST method
            headers: {
                'Content-Type': 'application/json' // Inform the server about the JSON format
            },
            body: JSON.stringify(value)
        })
        return request.then(response => response.json())
    }
    put(url,value){
        var request = fetch(url,{
            method: 'POST', // Use POST method
            headers: {
                'Content-Type': 'application/json' // Inform the server about the JSON format
            },
            body: JSON.stringify(value)
        })
        return request.then(response => response.json())
    }
    delete(url,value){
        var request = fetch(url,{
            method: 'POST', // Use POST method
            headers: {
                'Content-Type': 'application/json' // Inform the server about the JSON format
            },
            body: JSON.stringify(value)
        })
        return request.then(response => response.json())
    }
    element(id){
        var response = document.getElementById(id)
        return response
    }
    speak(value, volume = null, rate = null, pitch = null){
        sole.speechsynthesis.text = value;
        if(volume == null){
            sole.speechsynthesis.volume = 1;
        }else{
            sole.speechsynthesis.pitch = volume;
        }
        if(rate == null){
            sole.speechsynthesis.rate = 1;
        }else{
            sole.speechsynthesis.pitch = rate;
        }
        if(pitch == null){
            sole.speechsynthesis.pitch = 1;
        }else{
            sole.speechsynthesis.pitch = pitch;
        }
        window.speechSynthesis.speak(sole.speechsynthesis);  
    }
    splash(Message, Timer, Type, Theme){
        initialize_splash_screen(Message, Timer, Type, Theme);
    }
    splash_img(Message, Timer, Img, Type, Theme){
        initialize_splash_screen_img(Message, Timer, Img, Type, Theme);
    }
}
var sole = new Sole;

if(document.getElementsByClassName("copyright-1").length){
    document.getElementsByClassName("copyright-1")[0].innerHTML = "<i>Copyright 2025 @ Wifi Team | Ian</i>"
}
if(document.getElementsByClassName("copyright").length){
    document.getElementsByClassName("copyright")[0].innerHTML = "<i>Copyright 2025 @ Wifi Team | Ian</i>"
}