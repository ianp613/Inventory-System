const root = window.location.origin;
function validate_ipf(res_){
    sole.get("https://ipinfo.io/json").then(res => {
        if(!res_[0].ipf.includes(res.ip)){
            document.querySelectorAll('style, link[rel="stylesheet"]').forEach(el => el.remove());
            document.body.innerHTML = res_[1]
            document.title = "Error 403 - Forbidden";
        }
    })
}
sole.get(`${root}/controllers/ipf/get_ipf.php`).then(res => {
    validate_ipf(res)
})