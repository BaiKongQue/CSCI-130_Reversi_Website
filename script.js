/*********************
 * Global Javascript *
 *********************/
var urlBase = "127.0.0.1";

function is_logged_in() {
    var xhttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (!JSON.parse(this.responseText)['result']) {
                document.location = urlBase;
            }
        } else {
            document.location = urlBase;
        }
    }
    xhttp.open("GET", "./php/login.check.php", true);
    xhttp.send();
}