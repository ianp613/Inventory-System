const root = window.location.origin;
function validate_ipf(res_){
    sole.get("https://ipinfo.io/json").then(res => {
        if(!res_.ipf.includes(res.ip)){
            window.location.replace("../access-denied.php")
        }
    })
}
sole.get(`${root}/controllers/ipf/get_ipf.php`).then(res => {
    validate_ipf(res)
})