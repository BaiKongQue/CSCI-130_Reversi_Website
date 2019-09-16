var canvas = document.getElementById("board");
var context = canvas.getContext("2d");
var data = {};
var grid_size = 0;
var bit_size = 0;
var frameRate = 60;

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
            _OnRender();
        }
    }

    const game_id = _GetRouteParams()["id"];
    if (game_id != undefined) {
        dataHttp.open("GET", "game.get.php?id=" + game_id, true);
        dataHttp.send();
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
            context.fillStyle = 'green';
            context.fillRect((i * bit_size) + 1, (j * bit_size) + 1, bit_size - 2, bit_size - 2);
            context.closePath();
            
            if (data.grid[current_index] == 1) {
                _DrawCircle('white', i, j); 
            } else if (data.grid[current_index] == 2) {
                _DrawCircle('black', i, j);
            }
            current_index++;
        }
    }
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
        _OnRender();
    }, 1000/frameRate);
}
