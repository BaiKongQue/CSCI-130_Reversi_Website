var isPop = false;
window.addEventListener("click", closeNavFromOutside, false);

function closeNavFromOutside(e) {
    if(!document.getElementById("nav-list").contains(e.target) && 
       !document.getElementById("nav-button").contains(e.target)){
        document.getElementById("nav-list").style.width = "0px";
        isPop = !isPop;
    }
}

function popNav() {
    isPop = !isPop;
    if(isPop) {
        document.getElementById("nav-list").style.width = "200px";
    }
    else {
        document.getElementById("nav-list").style.width = "0px";
    }
}