class BS5_Toaster {
    icon_span = document.createElement("span");
    message_div = document.createElement("div");
    close_button = document.createElement("button");
    wrap_1 = document.createElement("div");
    wrap_2 = document.createElement("div");
    wrap_3 = document.createElement("div");
    wrap_4 = document.createElement("div");
    wrap_5 = document.createElement("div");
    modal_button = null
    alert_modal = null
    constructor() {
        this.init_modal();
    }
    init_modal(){
        this.close_button.setAttribute("type","button")
        this.close_button.setAttribute("id","alert_button")
        this.close_button.setAttribute("style","width: 50px;")
        this.close_button.setAttribute("class","btn btn-secondary btn-sm mt-3 rounded-pill")
        this.close_button.setAttribute("data-bs-dismiss","modal")

        this.wrap_1.setAttribute("class","w-100")
        this.wrap_2.setAttribute("class","modal-header text-center")
        this.wrap_3.setAttribute("class","modal-content")
        this.wrap_5.setAttribute("class","modal")
        this.wrap_5.setAttribute("id","alert_modal")
        this.wrap_5.setAttribute("tabindex","-1")
    }
    close(){
        const alertModal = document.getElementById("alert_modal");
        if(alertModal) {
            alertModal.remove();
        }
    }
    toast(type,message,size = null,button = true,icon = true){
        this.modal_button = button
        if(message){
            if(size){
                if(size == "lg"){
                    this.wrap_4.setAttribute("class","modal-dialog modal-dialog-centered")
                }else{
                    this.wrap_4.setAttribute("class","modal-dialog modal-dialog-centered modal-sm")
                }
            }else{
                this.wrap_4.setAttribute("class","modal-dialog modal-dialog-centered modal-sm")
            }
            if(icon){
                if(type == "warning"){
                    this.icon_span.setAttribute("class","fa fa-exclamation-triangle text-warning h2")
                }else if(type == "error"){
                    this.icon_span.setAttribute("class","fa fa-exclamation-triangle text-danger h2")
                }else if(type == "info"){
                    this.icon_span.setAttribute("class","fa fa-info text-primary h2")
                }else if(type == "success"){
                    this.icon_span.setAttribute("class","fa fa-check text-success h2")
                }else{
                    this.icon_span.setAttribute("class","fa fa-info text-primary h2")
                }     
            }
            
            this.message_div.innerHTML = message
            this.close_button.innerText = "OK"
            this.alert_compile()
            this.alert_attach()
            this.alert_open()
            this.alert_listen(button)
        }
    }
    alert_compile(){
        this.wrap_1.appendChild(this.icon_span)
        this.wrap_1.appendChild(this.message_div)
        if(this.modal_button) this.wrap_1.appendChild(this.close_button)

        this.wrap_2.appendChild(this.wrap_1)
        this.wrap_3.appendChild(this.wrap_2)
        this.wrap_4.appendChild(this.wrap_3)
        this.wrap_5.appendChild(this.wrap_4)
    }
    alert_attach(){
        document.body.appendChild(this.wrap_5)
        this.alert_modal = new bootstrap.Modal(document.getElementById('alert_modal'),{
            backdrop: 'static',
            keyboard: false
        });
    }
    alert_open(){
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach((modal) => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
        this.alert_modal.show()
        this.close_button.focus()
        let el = document.getElementsByClassName("modal-backdrop")
        for (let i = 2; i < el.length; i++) {
            if(i != 0 || i != 1){
                el[i].remove()    
            }
        }
    }
    alert_listen(button){
        if(button){
            document.getElementById("alert_button").addEventListener("click", function() {
                setTimeout(() => {
                    const alertModal = document.getElementById("alert_modal");
                    if(alertModal) {
                        alertModal.remove();
                    }
                }, 1000);
            });      
        }
    }
  }
  var bs5 = new BS5_Toaster();
  