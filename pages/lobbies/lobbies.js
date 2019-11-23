let form = document.getElementById('filters-form');
let lobbies = document.getElementById('lobbies-list-ul');
let getLobbies = new XMLHttpRequest();
let joinHttp = new XMLHttpRequest();
let data = [];

form.oninput = function () {
    LoadData();
};

getLobbies.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        let res = JSON.parse(this.responseText);
        if (res.result) {
            data = res.result;
            if (!sessionData.player_id) {
                login_pipe.push(LoadData);
            } else {
                LoadData();
                }
        } else {
            document.getElementById("error-msg").innerText = res.error;
        }
    }
}

joinHttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        let res = JSON.parse(this.responseText);
        if (res.result) {
            document.location = "http://" + location.hostname + "/pages/game/game.html?id=" + res.game_id;
        } else {
            document.getElementById("error-msg").innerText = res.error;
        }
    }
}

function GetLobbies() {
    getLobbies.open('GET', "./lobbies.get.php", true);
    getLobbies.send();
}

function player_block(player, gameId, samePlayer = false) {
    let name;
    let icon;
    if (player.id != null) {
        if (player.id > 0) {
            name = player.first_name + " " + player.last_name;
            icon = player.icon;
        } else {
            if (player.id == -1) { 
                name = "AI Dusty";
                icon = "ai_dusty.jpg";
            } else {
                name = "AI Vader";
                icon = "ai_vader.png";
            }
        }
        return "<div class=\"player-block\">" +
            "<div><h2>" + name + "</h2></div>" +
            "<div><img src=\"../../images/upload/users/" + icon + "\" alt=\"player icon\" /></div>" +
            "<div><strong>Score: " + player.score + "</strong></div>" +
            "</div>";
    } else {
        return "<div>" +
            "<div class=\"player-block\">Waiting for opponent</div>" +
            (!samePlayer ? "<button id=\"joinGame\" onclick=\"joinGame("+gameId+")\" style=\"display:block; margin: 0 auto\">Join</button>" : "") +
            "</div>";
    }
}

function joinGame($gameId) {
    joinHttp.open('POST', "./join.post.php", true);
    joinHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    joinHttp.send("game_id=" + $gameId);
}

function DisplayData(r) {
    lobbies.innerHTML += "<li>" +
        "<div id=\"player-blocks\">" +
            player_block(r.player1, r.game_id) +
            "<div><h1>VS</h1></div>" +
            player_block(r.player2, r.game_id, (r.player1.id == sessionData.player_id)) +
        "</div>" +
        "<div><a href=\"../game/game.html?id=" + r.game_id + "\"><button style=\"display:block;margin:0 auto\">View Game</button></a></div>" +
        "<div id=\"game-duration\">Duration: " + r.duration + "</div>" +
        "<div id=\"game-id\">Game id: " + r.game_id + "</div>" +
    "</li>";
}

function exists() {
    return data.some(function(i) {
        return i.id == sessionData.player_id;
    });
}

function LoadData() {
    let formData = new FormData(form);
    let matches = (name, data) => {
        console.log(name, data, formData.get(name));
        return formData.get(name) != "" && !data.toLowerCase().includes(formData.get(name).toLowerCase())
    };

    if (!exists()) {
        lobbies.innerHTML += "<div style=\"text-align:center\">You have no current games! Go start one by <a href=\"../create-game/create-game.html\">Creating a game!</a></div>"
        return;
    }

    lobbies.innerHTML = "";

    for (let r of data) {
        let cont = false;
        if (formData.get('view-all') || (!formData.get('view-all') && (r.player1.id == sessionData.player_id || (r.player2.id && r.player2.id == sessionData.player_id)))) {
            for (let i of ['first_name', 'last_name']) {
                if (matches(i, r.player1[i]) || (!!r.player2[i] && matches(i, r.player2[i]))) {
                    cont = true;
                    break;
                }
            }
            if (cont) continue;

            DisplayData(r);
        }
    }
}

GetLobbies();