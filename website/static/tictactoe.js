var gameBoard = [
    [null, null, null],
    [null, null, null],
    [null, null, null]
];

var currentPlayer = "X";

var boardElement = document.getElementById("game-board");
var cells = document.getElementsByClassName("cell");
for (var i = 0; i < cells.length; i++) {
    cells[i].addEventListener("click", handleCellClick);
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2)
    return parts.pop().split(";").shift();
}

function handleCellClick(event) {
    var cellElement = event.target;
    cellElement.style.color = "green";
    var row = cellElement.getAttribute("data-row");
    var col = cellElement.getAttribute("data-col");

    if (gameBoard[row][col] !== null) {
        return;
    }

    gameBoard[row][col] = currentPlayer;
    cellElement.innerHTML = currentPlayer;

    var gameOver = checkForWin("X");
    if (gameOver) {
        submitGame("X");
    } else if (checkForTie()) {
        submitGame("");
    } else {
        //currentPlayer = (currentPlayer === "X") ? "O" : "X";
        if(currentPlayer === "X"){
            currentPlayer = "O"
            computerMove();
        }else{
            currentPlayer = "X"
            computerMove();
        }
    }
}

function computerMove() {
    var bestScore = -Infinity;
    var move;
    for (var i = 0; i < 3; i++) {
        for (var j = 0; j < 3; j++) {
            if (gameBoard[i][j] == null) {
                gameBoard[i][j] = "O";
                var score = minimax(gameBoard, 0, false) + (Math.random() * 5.1);
                gameBoard[i][j] = null;
                if (score > bestScore) {
                    bestScore = score;
                    move = { i, j };
                }
            }
        }
    }
    gameBoard[move.i][move.j] = "O";
    var cell = document.querySelector(`[data-row="${move.i}"][data-col="${move.j}"]`);
    cell.innerHTML = "O";
    cell.style.color = "red";
    
    var gameOver = checkForWin("O");
    if (gameOver) {
        submitGame("O");
    }
    currentPlayer = "X";
}

function minimax(board, depth, isMaximizing) {
    var result = checkForWin(board);
    if (result !== null) {
        return result === "O" ? 10 - depth : depth - 10;
    }

    if (isMaximizing) {
        var bestScore = -Infinity;
        for (var i = 0; i < 3; i++) {
            for (var j = 0; j < 3; j++) {
                if (board[i][j] == null) {
                    board[i][j] = "O";
                    var score = minimax(board, depth + 1, false);
                    board[i][j] = null;
                    bestScore = Math.max(score, bestScore);
                }
            }
        }
        return bestScore;
    } else {
        var bestScore = Infinity;
        for (var i = 0; i < 3; i++) {
            for (var j = 0; j < 3; j++) {
                if (board[i][j] == null) {
                    board[i][j] = "X";
                    var score = minimax(board, depth + 1, true);
                    board[i][j] = null;
                    bestScore = Math.min(score, bestScore);
                }
            }
        }
        return bestScore;
    }
}

function checkForWin(currentPlayer) {
    var winCombinations = [
        [[0,0],[0,1],[0,2]],
        [[1,0],[1,1],[1,2]],
        [[2,0],[2,1],[2,2]],
        [[0,0],[1,0],[2,0]],
        [[0,1],[1,1],[2,1]],
        [[0,2],[1,2],[2,2]],
        [[0,0],[1,1],[2,2]],
        [[0,2],[1,1],[2,0]]
    ];

    for (var i = 0; i < winCombinations.length; i++) {
        var winCombo = winCombinations[i];
        if (gameBoard[winCombo[0][0]][winCombo[0][1]] === currentPlayer
            && gameBoard[winCombo[1][0]][winCombo[1][1]] === currentPlayer
            && gameBoard[winCombo[2][0]][winCombo[2][1]] === currentPlayer) {
            return true;
        }
    }
    return false;
}

function checkForTie() {
    for (var i = 0; i < 3; i++) {
        for (var j = 0; j < 3; j++) {
            if (gameBoard[i][j] === null) {
                return false;
            }
        }
    }
    return true;
}

function showAlert(message) {
    var alertBox = document.getElementById("custom-alert");
    var messageBox = document.getElementById("custom-alert-message");
    var okButton = document.getElementById("custom-alert-button");
    
    messageBox.innerHTML = message;
    alertBox.style.display = "block";
    
    okButton.onclick = function() {
      alertBox.style.display = "none";
    }
}
  
function submitGame(a){
    if(a=="O" || a=="X"){
        for (var i = 0; i < cells.length; i++) {
            cells[i].removeEventListener("click", handleCellClick);
        }
        document.getElementById("token").value = getCookie("token");
        showAlert(a+" win the game")
    }
    
}


