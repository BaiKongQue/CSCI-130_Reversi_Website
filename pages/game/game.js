var boardColor = document.getElementById("board-color");
var p1PieceColor = document.getElementById("p1-color");
var p2PieceColor = document.getElementById("p2-color");

var canvas = document.getElementById("board");
var context = canvas.getContext("2d");
var data = {};
var grid_size = 0;
var bit_size = 0;
var frameRate = 60;
var available_move = {};

/***************************
 * Http ready state set up *
 ***************************/
var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
dataHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        res = JSON.parse(this.responseText)['result'];
        data = res.data;
        available_move = res.moves;
        //Find the grid size and bit_size
        let length = data.grid.length;
        grid_size = Math.sqrt(length);
        bit_size = 600 / grid_size;

        if (data.player2_id < 0 && data.player_turn == data.player2_id) {
            _MoveAi();
        }
    }
}

var sendMoveHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
sendMoveHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        res = JSON.parse(this.responseText)['result'];
        data = res.data;
        available_move = res.moves;
        // _GetMoveAvialable();
        if (data.player2_id < 0 && data.player_turn == data.player2_id) {
            _MoveAi();
        }
    }
}

/*******************
 * Event listeners *
 *******************/
canvas.addEventListener('mousedown', _ClickOn, false);

/*********************
 * PRIVATE functions *
 *********************/
function _GetGameData() {
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
                console.log('a');
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
        // _GetMoveAvialable();
        _SendMove(i);
        // _MoveAi();
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
}

function _InitStart() {
    _GetGameData();
}

function _InitLoop() {
    setInterval(() => {
        // if (document.hasFocus())
        //     _GetGameData();
    }, 5 * 1000);
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

function reset() {

}