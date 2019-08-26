<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/definitions.php";
include_once 'SecureSession.class.php';
include_once 'login.class.php';
sec_session_start();

class Game {
// PRIVATE
    private $Mysqli;
    private $Login;
    
// PUBLIC
    public $error;
    public $gameGrid;
    public $player1;
    public $player2;
    public $p1_score;
    public $p2_score;

    public function __construct() {
        $this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_SCHEME);
        $this->error = '';
        $this->Login = new Login();
    }

// PRIVATE
    

// PUBLIC
    public function get_game_data(int $gameId) {
        if ($this->Login->login_check()) {
            if ($stmt = $this->Mysqli->prepare("SELECT player1_id, player2_id, TIMEDIFF(start_time, NOW()), player1_score, player2_score, data FROM games WHERE game_id = ? LIMIT 1")) {
                $stmt->bind_param('i', $gameId);
                if ($stmt->execute()) {
                    $stmt->bind_result($player1_id, $player2_id, $start_time, $player1_score, $player2_score, $data);
                    $stmt->fetch();
                    return [
                        'game_id' => $gameId,
                        'player1_id' => $player1_id,
                        'player2_id' => $player2_id,
                        'start_time' => $start_time,
                        'player1_score' => $player1_score,
                        'player2_score' => $player2_score,
                        'data' => $data
                    ];
                } else {
                    $this->error .= "Error connecting to the server try again later.\n";
                    return false;
                }
                $stmt->free_result();
                $stmt->close();
            }
        } else {
            $this->error .= "You are not logged in!\n";
            return false;
        }
    }

    public function get_scores(int $playerId = NULL) {
        $selection = ($playerId != NULL) ?
            "SELECT player1_score FROM games where player1_id = ? UNION SELECT player2_score FROM games where player2_id = ?"
        :
            "";
        if ($stmt = $this->Mysqli->prepare("")) {

        }
    }
}

?>