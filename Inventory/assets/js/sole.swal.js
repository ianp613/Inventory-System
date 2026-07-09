class Sole_Swaler{
    toast(title = null,icon = null,text = null,confirmButtonText,confirmButtonColor = null){
        if(!confirmButtonText){
            confirmButtonText = "OK"
        }
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: confirmButtonText,
            confirmButtonColor: confirmButtonColor,
        })    
    }
}

var ss = new Sole_Swaler;
