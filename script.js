/*********************
 * Global Javascript *
 *********************/
window.onload = function() {
    var loggedInElements = document.getElementsByClassName("logged-in");
    var loggedOutElements = document.getElementsByClassName("logged-out");
    this.is_logged_in(function(loggedIn) {
        if (loggedIn) {
            for (let c of loggedInElements)
                c.style.display = "inherit";
            for (let c of loggedOutElements)
                c.style.display = "none";
        } else {
            for (let c of loggedInElements)
                c.style.display = "none";
            for (let c of loggedOutElements)
                c.style.display = "inherit";
        }
    })
}

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
window.addEventListener("click", closeNavFromOutside, false);

function closeNavFromOutside(e) {
    if(!document.getElementById("nav-container").contains(e.target) && 
       !document.getElementById("nav-button-open").contains(e.target) &&
       document.getElementById("nav-container").style.left == "0px"){
        popNav();
    }
}

function popNav() {
    let navElement = document.getElementById("nav-container");
    if(navElement.style.left == "0px") {
        navElement.style.left = "-300px";
    } else {
        navElement.style.left = "0";
    }
}