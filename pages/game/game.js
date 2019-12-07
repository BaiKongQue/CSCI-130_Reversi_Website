var boardColor = document.getElementById("board-color");
var p1PieceColor = document.getElementById("p1-color");
var p2PieceColor = document.getElementById("p2-color");
var errorMsg = document.getElementById('error-msg');

var canvas = document.getElementById("board");
var context = canvas.getContext("2d");
var data = {};
var grid_size = 0;
var bit_size = 0;
var frameRate = 60;
var available_move = {};

var runAi = true;
/***************************
 * Http ready state set up *
 ***************************/
var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

var sendMoveHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
sendMoveHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        res = JSON.parse(this.responseText)['result'];
        data = res.data;

        if (JSON.parse(this.responseText).error) {
            errorMsg.innerText = "";
            errorMsg.innerText = JSON.parse(this.responseText).error;
        }

        available_move = res.moves;
        
        _DisplayPlayer(1);
        _DisplayPlayer(2);

        if (runAi && data.player2_id < 0 && data.player_turn == data.player2_id) {
            runAi = false;
            _MoveAi();
            runAi = true;
        }
    }
}

function _GetGameData(callback = (data) => {}) {
    dataHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        res = JSON.parse(this.responseText)['result'];
        data = res.data;
        
        if (JSON.parse(this.responseText).error) {
            errorMsg.innerText = JSON.parse(this.responseText).error;
        }

        available_move = res.moves;
        //Find the grid size and bit_size
        let length = data.grid.length;
        grid_size = Math.sqrt(length);
        bit_size = 600 / grid_size;

        _DisplayPlayer(1);
        _DisplayPlayer(2);

        callback(data);
    }
    }
    const game_id = _GetRouteParams()["id"];
    if (game_id != undefined) {
        dataHttp.open("GET", "game.get.php?id=" + game_id, true);
        dataHttp.send();
    }
}

function _SendMove(index, ai = false) {
    sendMoveHttp.open("POST", "game.post.php", true);
    sendMoveHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    sendMoveHttp.send("data=" + JSON.stringify(data) + "&index=" + index + "&ai=" + ai);
}

/*******************
 * Event listeners *
 *******************/
canvas.addEventListener('mousedown', _ClickOn, false);

/*********************
 * PRIVATE functions *
 *********************/
function _GetRouteParams() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function _GetAiHeuristic() {
    switch (data.grid.length) {
        case 16:
            return [10, 3, 3, 10,
                     3, 0, 0, 3,
                     3, 0, 0, 3,
                    10, 3, 3, 10];
            break;
        case 36: 
            return [10, -8, 5, 5, -8, 10,
                    -8, -5, 3, 3, -5, -8,
                     5,  3, 0, 0,  3,  5,
                     5,  3, 0, 0,  3,  5,
                    -8, -5, 3, 3, -5, -8,
                    10, -8, 5, 5, -8, 10];
            break;
        case 64:
            return [10, -8,  5,  5,  5,  5, -8, 10,
                    -8, -5, -2, -2, -2, -2, -5, -8,
                     5, -2,  1,  1,  1,  1, -2,  5,
                     5, -2,  1,  0,  0,  1, -2,  5,
                     5, -2,  1,  0,  0,  1, -2,  5,
                     5, -2,  1,  1,  1,  1, -2,  5,
                    -8, -5, -2, -2, -2, -2, -5, -8,
                    10, -8,  5,  5,  5,  5, -8, 10];
            break;
    }
}

function _GetAiMove() {
    let max = null;
    let hueristic_board = _GetAiHeuristic();
    for(let i in available_move) {
        if (max == null || available_move[i] + hueristic_board[i] > available_move[max] + hueristic_board[max])
            max = i;
    }
    return max;
}

function _MoveAi() {
    setTimeout(() => {
        switch (data.player2_id) {
            case -1: // easy ai
                let keys = Object.keys(available_move);
                _SendMove(keys[Math.floor(Math.random() * keys.length)], true);
                break;
            case -2: // hard ai
                _SendMove(_GetAiMove(), true);
                break;
        }
    }, 1 * 1000);
}

function _ClickOn(event) {
    var x = Math.floor((event.x-canvas.getBoundingClientRect().left) / bit_size);
    var y = Math.floor((event.y-canvas.getBoundingClientRect().top) / bit_size);
    var i = (y * grid_size) + x;
    if (sessionData && sessionData.player_id == data.player_turn && i in available_move) {
        _SendMove(i);
    }
}

function _DisplayPlayer(playerNumber) {
    let player = 'player' + playerNumber;
    let playerDom = document.getElementById(player + "-container");
    if (data[player+'_id'] != null) {
        let img;
        let name;
        if (data[player + "_id"] > 0) {
            img = data[player + "_icon"];
            name = data[player + "_name"];
        } else {
            if (data[player + "_id"] == -1) {
                img = 'ai_dusty.jpg';
                name = 'AI Dusty';
            } else {
                img = 'ai_vader.png';
                name = 'AI Vader';
            }
        }
        if (data.player_turn == data[player+'_id']) {
            playerDom.classList.add("turn");
        } else {
            playerDom.classList.remove("turn");
        }
        // playerDom.innerHTML = "";
        playerDom.innerHTML = "<h1>Player "+playerNumber+"</h1>" +
                                // (data.player_turn == data[player + '_id'] ? "Turn" : "") + 
                                "<div class=\"player-icon\">" +
                                  "<img src=\"../../images/upload/users/"+img+"\" alt=\""+player+" icon\" />" +
                               "</div>" +
                               "<div class=\"player-name\">" + name + "</div>" +
                               "<div class=\"player-score\">Score: " + data[player + "_score"] + "</div>";
    } else {
        playerDom.innerHTML = "<h1>Waiting for Opponent</h1>";
    }
}

function _DrawCircle(color, i, j) {
    var radius = bit_size / 2;
    var x_center = (i * bit_size) + radius;
    var y_center = (j * bit_size) + radius;

    context.beginPath();
    context.arc(x_center,  y_center, radius - 2, 0, 2 * Math.PI, false);
    context.fillStyle = color;
    context.fill();
    context.lineWidth = 5;
    context.closePath();
}

function _DisplayWinner() {
    context.fillStyle = "#383838";
    context.globalAlpha = 0.8;
    context.fillRect(0, (canvas.height/2) - 50, canvas.width, 65);
    context.globalAlpha = 1.0;
    context.font = "60px Arial";
    context.fillStyle = "white"
    context.fillText("Winner: Player " + (data.winner == data.player1_id ? 1 : 2) + "!", canvas.width/2, canvas.height/2);
}

function _OnRender() {
    context.fillStyle = "black";
    context.fillRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i < grid_size; ++i) {
        for (var j = 0; j < grid_size; ++j) {
            let current_index = (j * grid_size) + i;
            context.beginPath()
            context.fillStyle = boardColor.value;
            context.fillRect((i * bit_size) + 1, (j * bit_size) + 1, bit_size - 2, bit_size - 2);
            context.closePath();
            
            if (data.grid[current_index] == 1) {
                _DrawCircle(p1PieceColor.value, i, j); 
            } else if (data.grid[current_index] == 2) {
                _DrawCircle(p2PieceColor.value, i, j);
            }

            if (data.player_turn == sessionData.player_id && current_index in available_move) {
                context.beginPath()
                context.fillStyle = 'pink';
                context.fillRect((i * bit_size) + 1, (j * bit_size) + 1, bit_size - 2, bit_size - 2);
                context.closePath();

                context.beginPath();
                context.fillStyle = 'white';
                context.fillText(available_move[current_index], (i * bit_size) + bit_size/2 + 1, (j * bit_size) + bit_size/2 + 12);
                context.closePath();

            }
        }
    }

    if (data.finished) {
        _DisplayWinner();
    }
}

function _InitStart() {
    _GetGameData((data) => {
        if (data.player2_id < 0 && data.player_turn == data.player2_id) {
            runAi = false;
            _MoveAi();
            runAi = true;
        }
    });
}

function _InitLoop() {
    setInterval(() => {
        // if you want updates when the window is active add:
        // !data.finished && document.hasFocus()
        if (data.player_turn != sessionData.player_id)
             _GetGameData();
    }, 1 * 1000);
}

function _InitRender() {
    setInterval(() => {
        _OnRender();
    }, 1000/frameRate);
}

/********************
 * PUBLIC functions *
 ********************/
function OnInit() {
    canvas.width = 600;
    canvas.height = 600;
    context.font = "30px Arial";
    context.fillStyle = "white";
    context.textAlign = "center";
    context.fillText("Loading...", canvas.width/2, canvas.height/2);

    login_pipe.push(_InitStart);
    login_pipe.push(_InitLoop);
    login_pipe.push(_InitRender);
}