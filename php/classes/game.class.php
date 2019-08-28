<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/definitions.php";
include_once 'SecureSession.class.php';
include_once 'login.class.php';
sec_session_start();

/**
 * Game Data structure:
 *  game_id: int
 *  player1_id: int
 *  player2_id: int
 *  player1_score: int
 *  player2_score: int
 *  start_time: time
 *  data: array(int)
 */

class Game {
// PRIVATE
    private $Mysqli;
    private $Login;
    
// PUBLIC
    public $error;

    public function __construct() {
        $this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_SCHEME);
        $this->error = '';
        $this->Login = new Login();
    }

// PRIVATE
    /**
     * Convert 2D coordinates into 1D index
     * @param int $size: size of the game grid
     * @param int $x: x coordinate
     * @param int $y: y coordinate
     * @return int: index of x and y coordinate 
     */
    private function convert_to_1D(int $size, int $x, int $y): int { return ($y * $size) + $x; }
    /**
     * Convert 1D index to 2D coordinate X
     * @param int $size: size of the game grid
     * @param int $i: index to convert to 2D X
     * @return int: converted i to X coordinate
     */
    private function convert_to_x_2D(int $size, int $i): int { return intdiv($i, $size); }
    /**
     * Convert 1D index to 2D coordinate Y
     * @param int $size: size of the game grid
     * @param int $i: index to convert to 2D Y
     * @return int: converted i to Y coordinate
     */
    private function convert_to_y_2D(int $size, int $i): int { return $y % $size; }

    /**
     * Retrieves what the new score will be.
     * 
     */
    private function get_new_score(): int {
        
    }

    private function can_move(array $data, int $moveX, int $moveY): boolean {
        return false;
    }

// PUBLIC
    /**
     * create a new game and add it to the database
     * @param int $size: size of the game grid
     * @return boolean: if the game was successfully created
     */
    public function create_game(int $size): boolean {
        $size = filter_var($size, FILTER_SANITIZE_NUMBER_INT);  // sanitize the input
        if ($this->Login->login_check()) {                      // check if user is logged in
            // create initial grid
            $grid = array_fill(0, $size * $size, 0);            // create game grid array
            $half = intdiv($size, 2) - 1;                       // get middle of board
            $grid[$half] = GAME_TILE_PLAYER1;                   // player 1 tile
            $grid[$half + 1 + $size] = GAME_TILE_PLAYER1;       // player 1 tile
            $grid[$half + 1] = GAME_TILE_PLAYER2;               // player 2 tile
            $grid[$half + $size] = GAME_TILE_PLAYER2;           // player 2 tile
            if ($stmt = $this->Mysqli->prepare("INSERT INTO games(player1_id, player1_score, start_time, data, player_turn) VALUES(?,?,?, NOW(), ?)")) {
                $stmt->bind_param('iisi', $_SESSION['player_id'], 0, $grid, $_SESSION['player_id']); // bind params
                if ($stmt->execute()) {                         // execute query
                    return true;                                // successfully created game
                } else {
                    $this->error .= "Failed to create new game, please try again later.\n"; // error failed to connect to db
                    return false;
                }
            }
        } else {
            $this->error .= "You are not logged in!\n";        //  error user is not logged in
            return false;
        }
    }

    /**
     * Retrieve the game data of specified game id.
     * @param int $gameId: id of the game
     * @return {
     *      game_id: int,
     *      player1_id: int,
     *      player2_id: int,
     *      start_time: Time,
     *      player1_score: int,
     *      player2_score: int,
     *      data: array
     * }: an array of all the data needed for the game.
     */
    public function get_game_data(int $gameId): array {
        if ($this->Login->login_check()) {                  // check if user is logged in
            if ($stmt = $this->Mysqli->prepare("SELECT player1_id, player2_id, TIMEDIFF(start_time, NOW()), player1_score, player2_score, data FROM games WHERE game_id = ? LIMIT 1")) {
                $stmt->bind_param('i', $gameId);            // bind game id
                if ($stmt->execute()) {                     // execute
                    $stmt->bind_result($player1_id, $player2_id, $start_time, $player1_score, $player2_score, $data);
                    $stmt->fetch();                         // fetch row
                    return [                                // return array
                        'game_id' => $gameId,               // game id
                        'player1_id' => $player1_id,        // player1 id
                        'player2_id' => $player2_id,        // player2 id
                        'start_time' => $start_time,        // start time
                        'player1_score' => $player1_score,  // player1 score
                        'player2_score' => $player2_score,  // player2 score
                        'data' => $data                     // game grid data
                    ];
                } else {
                    $this->error .= "Error connecting to the server try again later.\n";    // error processing request
                    return false;
                }
                $stmt->free_result();                       // free result
                $stmt->close();                             // close connection
            }
        } else {
            $this->error .= "You are not logged in!\n";     // error not logged in
            return false;
        }
    }

    /**
     * Retrieves a query of all the scores and people that have completed a game. Can change Sort,
     * Order and who to search for with limit input.
     * @param string $firstName: person's first name to look for, NULL for everyone.
     * @param string $lastName: person's last name to look for, NULL for everyone.
     * @param string $sortBy: attribute to sort by (first_name, last_name, score, duration)
     * @param string $orderBy: order to sort by (ASC, DESC)
     * @return {first_name: string, last_name: string, score: int, duration: Time}[]: a list of score data.
     */
    public function get_scores(string $firstName = NULL, string $lastName = NULL, string $sortBy = "score", string $orderBy = "DESC"): array {
        $selection = "";                                                            // set selection
        if ($firstName != NULL || $lastName != NULL) {                              // if first name or last name isnt null
            $selection .= "WHERE ";                                                 // add WHERE
            if ($firstName != NULL) {                                               // if first name not null
                $firstName = filter_var($firstName, FILTER_SANITIZE_STRING);        // sanitize string
                $selection .= "p.first_name = '$firstName'";                        // add firstname to query
                if ($lastName != NULL) $selection .= "AND ";                        // if lastname is not null add AND
            }
            if ($lastName != NULL) {                                                // if last name is not null
                $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);          // sanitize string
                $selection .= "p.last_name = '$lastName'";                          // add lastname to query
            }
        }
        if ($stmt = $this->Mysqli->prepare("SELECT p.first_name, p.last_name, g.score, TIMEDIFF(g.end_time, g.start_time) duration FROM (SELECT player1_id player_id, player1_score score, start_time, end_time FROM games UNION SELECT player2_id player_id, player2_score score, start_time, end_time FROM games) g LEFT JOIN players p USING(player_id) " . $selection . " ORDER BY ? ?")) {
            $stmt->bind_param('ss', $sortBy, $orderBy);
            $stmt->execute();                                                       // run prepared query
            $stmt->bind_result($dbFirstName, $dbLastName, $dbScore, $dbDuration);   // bind results
            $res = [];                                                              // init res array
            while($stmt->fetch()) {                                                 // fetch each row
                $a = [];                                                            // start new array
                $a['first_name'] = $dbFirstName;                                    // add first name
                $a['last_name'] = $dbLastName;                                      // add last name
                $a['score'] = $dbScore;                                             // add score
                $a['duration'] = $dbDuration;                                       // duration
                array_push($res, $a);                                               // push to res array
            }
            return $res;                                                            // return res
            $stmt->free_result();                                                   // free results
            $stmt->close();                                                         // close connection
        } else {
            $this->error .= "there was an error connecting to the server, try again later.\n"; // error preparing query
            return false;
        }
    }

    public function __deconstruct() {
        $this->Mysqli->close();     // close mysql connection
        $this->Mysqli = NULL;       // set null
    }
}

?>