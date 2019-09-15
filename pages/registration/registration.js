function submit_registration() {
    let form = document.getElementById('register-form');
    let formData = new FormData(form);
    for (let input of formData.values()) {
        if (!input) return;
    }
    let xhttp = new XMLHttpRequest();
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            if (res.result) {
                document.location = "../login/login.html";
            } else {
                document.getElementById("error-msg").innerText = res.error;
            }
        }
    }

    xhttp.open('POST', './registration.post.php', true);
    xhttp.send(formData);
}