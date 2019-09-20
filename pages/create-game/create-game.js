function submit_create_game() {
    let form = document.getElementById('create-game-form');
    let formData = new FormData(form);
    for (let input of formData.values()) {
        if (!input) return;
    }
    let xhttp = new XMLHttpRequest();
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            if (res.result) {
                document.location = "../game/game.html?id=" + res.id;
            } else {
                document.getElementById("error-msg").innerText = res.error;
            }
        }
    }

    xhttp.open('POST', './create-game.post.php', true);
    xhttp.send(formData);
}