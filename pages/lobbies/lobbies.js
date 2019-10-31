let lobbies = document.getElementById('lobbies-list-ul');
let xhttp = new XMLHttpRequest();

xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        let res = JSON.parse(this.responseText);
        if (res.result) {
            for (let r of res.result) {
                lobbies.innerHTML +="<li>" +
                                        "<a href=\"../game/game.html?id="+r.game_id+"\">" +
                                            "<div id=\"player-blocks\">" +
                                                player_block(r.player1) +
                                                "<div><h1>VS</h1></div>" +
                                                player_block(r.player2) +
                                            "</div>" +
                                            "<div>" +
                                                "<div>Duration: "+ r.duration +"</div>" +
                                                "<div><a href=\""+  +"\"><button>Play</button></a></div>" + 
                                            "</div>" +
                                        "</a>" +
                                    "</li>";
            }
        } else {
            document.getElementById("error-msg").innerText = res.error;
        }
    }
}

xhttp.open('GET', "./lobbies.get.php", true);
xhttp.send();

function player_block(player) {
    return "<div class=\"player-block\">" +
				"<div><h2>"+ player.first_name + " "+ player.last_name +"</h2></div>" +
				"<div><img src=\"../../images/upload/users/" + player.icon + "\" alt=\"player icon\" /></div>" +
				"<div><strong>Score: "+ player.score +"</strong></div>" +
			"</div>";
}