function submit_login() {
    let form = document.getElementById('login-form');
    let formData = new FormData(form);
    let xhttp = new XMLHttpRequest();
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            if (res.result) {
                document.location = "../../index.html";
            } else {
                document.getElementById("error-msg").innerText = res.error;
            }
        }
    }

    xhttp.open('POST', './login.post.php', true);
    xhttp.send(formData);
}