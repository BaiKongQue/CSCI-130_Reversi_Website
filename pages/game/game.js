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

// PRIVATE
function _GetRouteParams() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function _GetGameData() {
    var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    dataHttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            data = JSON.parse(this.responseText)['result'];

            //Find the grid size and bit_size
            let length = data.grid.length;
            grid_size = Math.sqrt(length);
            bit_size = 600 / grid_size;
            _GetMoveAvialable();
        }
    }

    const game_id = _GetRouteParams()["id"];
    if (game_id != undefined) {
        dataHttp.open("GET", "game.get.php?id=" + game_id, true);
        dataHttp.send("data=" + JSON.stringify(data));
    }
}

function _DrawCircle(color, i, j) {
    let radius = bit_size / 2;
    let x_center = (i * bit_size) + radius;
    let y_center = (j * bit_size) + radius;

    context.beginPath();
    context.arc(x_center,  y_center, radius - 2, 0, 2 * Math.PI, false);
    context.fillStyle = color;
    context.fill();
    context.lineWidth = 5;
    context.closePath();
}

function _OnRender() {
    let current_index = 0;
    context.fillStyle = "black";
    context.fillRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i < grid_size; ++i) {
        for (var j = 0; j < grid_size; ++j) {
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
            current_index++;
        }
    }
}

function _GetMoveAvialable() {
    var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    dataHttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            available_move = JSON.parse(this.responseText)['result'];
            
        }
    }

    dataHttp.open("POST", "moves.get.php", true);
    dataHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    dataHttp.send("data=" + JSON.stringify(data));
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
        _GetMoveAvialable();
    }, 5 * 1000);
    // set inter (3 * 1000)
    //      OnLoop
    setInterval(() => {
        _OnRender();
    }, 1000/frameRate);
}
