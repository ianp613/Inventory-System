class Sole_Swaler{
    toast(title = null,icon = null,text = null,confirmButtonText = "OK"){
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: confirmButtonText
        })    
    }
}

var ss = new Sole_Swaler;
