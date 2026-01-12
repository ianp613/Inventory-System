var add_target = document.getElementById("add_target")
var add_group = document.getElementById("add_group")
var delete_group = document.getElementById("delete_group")
var group = document.getElementById("group")
var ping_container = document.getElementById("ping_container")
var ping




add_group.addEventListener("click",function(){

})


sole.get("../../controllers/ping/get_group.php")
.then(res => {
    var sel = res.length ? true : false
    res.forEach(r => {
        var op = document.createElement("option")
        sel ? op.setAttribute("selected",true) : null
        sel ? sel = false : null
        op.value = r
        op.innerText = r
        group.appendChild(op)
    });
    if(group.value){
        set_ping()
        ping = setInterval(() => {
            get_ping()
        }, 1000);    
    }
})

group.addEventListener("change",function(){
    clearInterval(ping)
    ping_container.innerHTML = ""
    if(group.value){
        set_ping()
        ping = setInterval(() => {
            get_ping()
        }, 1000);    
    }
    
})
function set_ping(){
    sole.post("../../controllers/ping/find_group.php",{
        group: group.value
    }).then(res => {
        res.forEach(r => {
            var col = document.createElement("div")
            col.setAttribute("class","col-md-3")
            col.setAttribute("id",r)
            // col.innerText = r
            ping_container.appendChild(col)
        });
    })
}
function get_ping(){
    console.log("get_ping")
    sole.post("../../controllers/ping/get_ping.php",{
        group: group.value
    }).then(res => {
        var i = 0;
        res.target.forEach(t => {
            var tar = document.getElementById(t);
            console.log(res.reply[i][t])
            if(typeof res.reply[i][t] === "string"){
                tar.innerHTML += res.reply[i][t] + "<br>"  
            }else{
                console.log("OBJECT NA")
            }
            i++
        });
    })
}


