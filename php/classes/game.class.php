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

    public function _construct() {
        $this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_SCHEME);
        $this->error = '';
        $this->Login = new Login();
    }

// PRIVATE
    

// PUBLIC
    public function GetGameData() {
        if ($this->Login->login_check()) {
            if ($stmt = $this->Mysqli->prepare("SELECT ")) {
                
            }
        } else {
            $this->error .= "You are not logged in!\n";
            return false;
        }
    }
}

?>