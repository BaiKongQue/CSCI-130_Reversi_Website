/*********************
 * Global Javascript *
 *********************/

function is_logged_in(callback) {
    let xhttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            callback(JSON.parse(this.responseText)['result']);
        }
    }
    xhttp.open("GET", "http://"+location.hostname+"/php/login.check.php", true);
    xhttp.send();
}

/**************
 * Navigation *
 **************/
var isPop = false;
window.addEventListener("click", closeNavFromOutside, false);

function closeNavFromOutside(e) {
    if(!document.getElementById("nav-container").contains(e.target) && 
       !document.getElementById("nav-button-open").contains(e.target)){
        document.getElementById("nav-container").style.left = "-300px";
        isPop = !isPop;
    }
}

function popNav() {
    isPop = !isPop;
    if(isPop) {
        document.getElementById("nav-container").style.left = "0";
    }
    else {
        document.getElementById("nav-container").style.left = "-300px";
    }
}