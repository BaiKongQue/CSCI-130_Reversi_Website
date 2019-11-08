let form = document.getElementById('filters-form');
let lobbies = document.getElementById('lobbies-list-ul');
let xhttp = new XMLHttpRequest();
let data = [];

form.oninput =function() {
    LoadData();
};

xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        let res = JSON.parse(this.responseText);
        if (res.result) {
            if (res.result.length <= 0) {
                lobbies.innerHTML += "<div style=\"text-align:center\">You have no current games! Go start one by <a href=\"../create-game/create-game.html\">Creating a game!</a></div>"
            } else {
                data = res.result;
                login_pipe.push(LoadData);
            }
        } else {
            document.getElementById("error-msg").innerText = res.error;
        }
    }
}

xhttp.open('GET', "./lobbies.get.php", true);
xhttp.send();

function player_block(player) {
    return (player.first_name != null) ? "<div class=\"player-block\">" +
				"<div><h2>"+ player.first_name + " "+ player.last_name +"</h2></div>" +
				"<div><img src=\"../../images/upload/users/" + player.icon + "\" alt=\"player icon\" /></div>" +
				"<div><strong>Score: "+ player.score +"</strong></div>" +
            "</div>" : "<div class=\"player-block\">Waiting for opponent</div>";
}

function DisplayData(r) {
    lobbies.innerHTML +="<li>" +
                            "<a href=\"../game/game.html?id="+r.game_id+"\">" +
                                "<div id=\"player-blocks\">" +
                                    player_block(r.player1) +
                                    "<div><h1>VS</h1></div>" +
                                    player_block(r.player2) +
                                "</div>" +
                                "<div id=\"game-duration\">Duration: " + r.duration + "</div>" +
                                "<div id=\"game-id\">Game id: " + r.game_id + "</div>" +
                            "</a>" +
                        "</li>";
}

function LoadData() {
    let formData = new FormData(form);
    let matches = (name, data) => formData.get(name) != "" && !data.toLowerCase().includes(formData.get(name).toLowerCase());    
    lobbies.innerHTML = "";

    for (let r of data) {
        let cont = false;
        if (formData.get('view-all') || (!formData.get('view-all') && (r.player1.id == sessionData.player_id || (r.player2.id && r.player2.id == sessionData.player_id)))) {
            for (let i of ['first_name', 'last_name']) {
                if (matches(i, r.player1[i]) || (r.player2.id && matches(i, r.player2[i]))) { 
                    cont = true
                    break
                }
            }
            if (cont) continue;
            
            DisplayData(r);
        }
    }
}