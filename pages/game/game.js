class Board {
    constructor() {
        this.canvas = document.getElementById("board");
        this.context = this.canvas.getContext("2d");
        this.data = {};
    }

    getRouteParams() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        return vars;
    }

    onInit() {
        this.canvas.style.width = "600px";
        this.canvas.style.height = "600px";

        var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        dataHttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                this.data = JSON.parse(this.responseText)['result'].game_id;
            }
        }

        const game_id = this.getRouteParams()["id"];
        if(game_id != undefined) {
            dataHttp.open("GET", "game.get.php?id=" + game_id, true);
            dataHttp.send();
        }
        
    }

    
}



