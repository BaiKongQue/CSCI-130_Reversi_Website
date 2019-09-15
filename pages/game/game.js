class Board {
    constructor() {
        this.canvas = document.getElementById("board");
        this.context = this.canvas.getContext("2d");
        this.data = {};
        this.start = null;
        this.grid_size = 0;
        this.bit_size = 0;
    }

    getRouteParams() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        return vars;
    }

    onInit() {
        this.canvas.width = "600";
        this.canvas.height = "600";

        var dataHttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        dataHttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                this.data = JSON.parse(this.responseText)['result'];

                //Find the grid size and bit_size
                let length = this.data['grid'].length;
                this.grid_size = Math.sqrt(length);
                this.bit_size = length / grid_size;
            }
        }

        const game_id = this.getRouteParams()["id"];
        if(game_id != undefined) {
            dataHttp.open("GET", "game.get.php?id=" + game_id, true);
            dataHttp.send();
        }  
        window.requestAnimationFrame((timestamp) => this.onRender(timestamp));
    }

    onRender(timestamp) {
        if(!this.start) {
            this.start = timestamp;
        }
        let progress = timestamp - this.start;

        for(var i = 0; i < this.grid_size; ++i) {
            for(var j = 0; j < this.grid_size; ++j) {
                this.context.fillStyle = 'green';
                this.context.fillRect((i*this.bit_size)+1, (j*this.bit_size)+1, this.bit_size-2, this.bit_size-2);
            }
        }

        if(progress < 2000) {
            window.requestAnimationFrame((timestamp) => this.onRender(timestamp));
        }
    }
    
}



