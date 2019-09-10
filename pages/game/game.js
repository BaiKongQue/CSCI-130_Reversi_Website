class Board {
    constructor() {
        this.canvas = document.getElementById("board");
        this.context = this.canvas.getContext("2d");
    }

    onInit() {
        this.canvas.style.width = "600px";
        this.canvas.style.height = "600px";
    }
}

board = new Board();
board.onInit();

