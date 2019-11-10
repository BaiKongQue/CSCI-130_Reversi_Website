/*********************
 * Global Javascript *
 *********************/
/**
 * Runs when the window finishes loading.
 */
window.onload = function() {
    is_logged_in();  // load the logged-in logged-out elements
}

/*********
 * Login * 
 *********/
sessionData = {
    "player_id":"8","username":"paradox","first_name":"Alice","last_name":"Doe","age":23,"gender":"girl","location":"Internet","icon":"first avatar.jpg"
};
login_pipe = [];
/**
 * Checks wether the user is logged in or not by sending a request to the server.
 * @param function(bool) callback: function to run when the get request finishes, and gives a parameter if the user is logged in or not
 */
function is_logged_in(callback = () => {}) {
    let xhttp = new XMLHttpRequest();                                               // create new xmlhttp request 
    xhttp.onreadystatechange = function() {                                         // if the state changes and finishes
        if (this.readyState == 4 && this.status == 200) {                           // successfully get
            let res = JSON.parse(this.responseText);                                // get result
            res.result = true;
            // sessionData = res.session;                                              // set session data
            for (let p of login_pipe) p(res.result);                                // send result to piped functions
            callback(res.result);                                                   // callback function and pass result
        }
    }
    xhttp.open("GET", "http://"+location.hostname+"/php/login.check.php", true);    // prepare get request
    xhttp.send();                                                                   // send request
}

/**
 * shows or hides all the elements with the class logged-in or logged-out according to if the user
 * is logged in or out.
 */
function load_login_elements(loggedIn) {
    var loggedInElements = document.getElementsByClassName("logged-in");        // get all logged-in classes
    var loggedOutElements = document.getElementsByClassName("logged-out");      // get all logged-out classes
    if (loggedIn) {                                                         // if logged in
        for (let c of loggedInElements)                                     // for each logged-in element
            c.style.display = "inherit";                                    // show
        for (let c of loggedOutElements)                                    // for each logged-out element
            c.style.display = "none";                                       // hide
    } else {
        for (let c of loggedInElements)                                     // for each logged-in element
            c.style.display = "none";                                       // hide
        for (let c of loggedOutElements)                                    // for each logged-out element
            c.style.display = "inherit";                                    // show
    }
}
login_pipe.push(load_login_elements);

function RedirectIsLoggedIn() {
    login_pipe.push((loggedIn) => {
        if (loggedIn) document.location = "http://" + location.hostname;
    });
}

function RedirectNotLoggedIn() {
    login_pipe.push((loggedIn) => {
        if (!loggedIn) document.location = "http://" + location.hostname + "/pages/login/login.html";
    });
}

/**************
 * Navigation *
 **************/
window.addEventListener("click", closeNavFromOutside, false);               // add event listener if the user clicks

/**
 * checks wether the user clicks outside the nav bar when its open.
 * @param event e: event
 */
function closeNavFromOutside(e) {
    if(!document.getElementById("nav-container").contains(e.target) && 
       !document.getElementById("nav-button-open").contains(e.target) &&
       document.getElementById("nav-container").style.left == "0px"){
        popNav();
    }
}

/**
 * toggles the nav bar from open to closed
 */
function popNav() {
    let navElement = document.getElementById("nav-container");
    if(navElement.style.left == "0px") {
        navElement.style.left = "-300px";
    } else {
        navElement.style.left = "0";
    }
}