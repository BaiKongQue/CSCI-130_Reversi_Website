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