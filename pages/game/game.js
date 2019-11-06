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
var ai_heuristic_board = [];

var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
dataHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        data = JSON.parse(this.responseText)['result'];

        //Find the grid size and bit_size
        let length = data.grid.length;
        grid_size = Math.sqrt(length);
        bit_size = 600 / grid_size;

        _GetMoveAvialable();
        _GetAiHeuristic();
    }
}

var getMoveHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
getMoveHttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        available_move = JSON.parse(this.responseText)['result'];
    }
}

canvas.addEventListener('mousedown', _ClickOn, false);

// PRIVATE
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
            ai_heuristic_board = [10, 3, 3, 10,
                                   3, 0, 0, 3,
                                   3, 0, 0, 3,
                                  10, 3, 3, 10];
            break;
        case 36: 
            ai_heuristic_board = [10, -8, 5, 5, -8, 10,
                                  -8, -5, 3, 3, -5, -8,
                                   5,  3, 0, 0,  3,  5,
                                   5,  3, 0, 0,  3,  5,
                                  -8, -5, 3, 3, -5, -8,
                                  10, -8, 5, 5, -8, 10];
            break;
        case 64:
            ai_heuristic_board = [10, -8,  5,  5,  5,  5, -8, 10,
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

//public function convert_to_1D(int $size, int $x, int $y): int { return ($y * $size) + $x; }

function _ClickOn(event) {
    var x = Math.floor((event.x-canvas.getBoundingClientRect().left) / bit_size);
    var y = Math.floor((event.y-canvas.getBoundingClientRect().top) / bit_size);
    data.grid[(y * grid_size) + x] = 1;
    _GetMoveAvialable();
}

//Work?
function _GetAiMove() {
    var ai_index = 0;
    var ai_hueristic = 0;
    // Loop through the tile
    for (var i = 0; i < ai_heuristic_board.length; ++i) {
        if (i in available_move) { //Check if the current tile is in the available tile
            var current_heuristic = ai_available_move[i] + ai_heuristic_board[i]; //Finding the heuristic value
            if (current_heuristic > ai_hueristic) { //Check if the current heuristic value is greater than the max value
                ai_hueristic = current_heuristic; //Reassigned
                ai_index = i;
            }
        }
    }
    var ai = {'Index': ai_index, 'Hueristic': ai_hueristic};
    return ai; 
}

function _GetGameData() {
    const game_id = _GetRouteParams()["id"];
    if (game_id != undefined) {
        dataHttp.open("GET", "game.get.php?id=" + game_id, true);
        dataHttp.send();
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
    // var current_index = 0;
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

            if (current_index in available_move) {
                context.beginPath()
                context.fillStyle = 'pink';
                context.fillRect((i * bit_size) + 1, (j * bit_size) + 1, bit_size - 2, bit_size - 2);
                context.closePath();

                context.beginPath();
                context.fillStyle = 'white';
                context.fillText(available_move[current_index], (i * bit_size) + bit_size/2 + 1, (j * bit_size) + bit_size/2 + 12);
                context.closePath();

            }
            // current_index++;
        }
    }
}

function _GetMoveAvialable() {
    getMoveHttp.open("POST", "moves.get.php", true);
    getMoveHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    getMoveHttp.send("data=" + JSON.stringify(data));
}

// PUBLIC
function OnInit() {
    canvas.width = 600;
    canvas.height = 600;
    context.font = "30px Arial";
    context.fillStyle = "white";
    context.textAlign = "center";
    context.fillText("Loading...", canvas.width/2, canvas.height/2);

    _GetGameData();   

    setInterval(() => {
        _GetGameData();
    }, 5 * 1000);
    
    setInterval(() => {
        _OnRender();
    }, 1000/frameRate);
}
