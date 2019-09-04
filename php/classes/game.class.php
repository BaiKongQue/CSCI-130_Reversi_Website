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

// PUBLIC
    /**
     * Convert 2D coordinates into 1D index
     * @param int $size: size of the game grid
     * @param int $x: x coordinate
     * @param int $y: y coordinate
     * @return int: index of x and y coordinate 
     */
    public function convert_to_1D(int $size, int $x, int $y): int { return ($y * $size) + $x; }
    /**
     * Convert 1D index to 2D coordinate X
     * @param int $size: size of the game grid
     * @param int $i: index to convert to 2D X
     * @return int: converted i to X coordinate
     */
    public function convert_to_x_2D(int $size, int $i): int { return intdiv($i, $size); }
    /**
     * Convert 1D index to 2D coordinate Y
     * @param int $size: size of the game grid
     * @param int $i: index to convert to 2D Y
     * @return int: converted i to Y coordinate
     */
    public function convert_to_y_2D(int $size, int $i): int { return $y % $size; }

    /**
     * Retrieves what the current score is.
     * @param array $grid: array of the game grid data to process
     * @param int $player: GAME_TILE of which player to get score for (GAME_TILE_PLAYER1, GAME_TIME_PLAYER2)
     * @return int: how many of each tile the player has
     */
    public function get_score(array $grid, int $player): int {
        $n = 0;
        for ($i = 0; $i < count($grid); $i++) {
            if ($grid[$i] == $player)
                $n++;
        }
        return $n;
    }

    
    /**
     * Calculates wether the specific move would be valid or not
     * @param array data: all the game data
     * @param int $index: index of the spot in grid array trying to move to
     * 
     */
    public function can_move(array $data, int $index): bool {
        $size = $data['size'] - 1;
        $grid &= $data['grid'];
        $player &= $data['grid'][$index];
        $res = [];
        foreach ([-1, 0 , 1] as $v) {
            $direction = ($spot * ($v < 0 ? -1 : 1));
            $spot = $index + $direction + $v;
            $count = 1;
            while ($grid[$spot] != $player && $grid[$spot] != GAME_TILE_NONE) {
                $spot += $direction + $v;
                if ($grid[$spot] == $player) {
                    if (!key_exists($index, $res)) {
                        $res[$index] = $count;
                    } else {
                        $res[$index] += $count;
                    }
                }
                $count++;
            }
        }
        return false;
    }

    /**
     * Calculate the size of the game grid from the data.
     * @param array $grid: game grid data
     * @return int: size of the grid.
     */
    public function get_grid_size(array $grid): int { return sqrt(count($grid)); }

    /**
     * create a new game and add it to the database
     * @param int $size: size of the game grid
     * @param string $vs: who the player is versing
     * @param string $difficulty: if vs is computer how difficult the computer is
     * @return bool: if the game was successfully created
     */
    public function create_game(int $size, string $difficulty = NULL): bool {
        if ($this->Login->login_check()) {                          // check if user is logged in
            // sanitize inputs
            $size = filter_var($size, FILTER_SANITIZE_NUMBER_INT);  // sanitize the input
            // $vs = filter_var($vs, FILTER_SANITIZE_STRING);
            if ($difficulty != NULL)  $difficulty = filter_var($difficulty, FILTER_SANITIZE_STRING);

            // create initial grid
            $sizeSqr = $size*$size;
            $grid = array_fill(0, $sizeSqr, 0);                     // create game grid array
            $half = intdiv($sizeSqr, 2) - 1;                        // get middle of board
            $grid[$half] = GAME_TILE_PLAYER1;                       // player 1 tile
            $grid[$half + 1 + $size] = GAME_TILE_PLAYER1;           // player 1 tile
            $grid[$half + 1] = GAME_TILE_PLAYER2;                   // player 2 tile
            $grid[$half + $size] = GAME_TILE_PLAYER2;               // player 2 tile
            $grid = json_encode($grid);                             // turn array into json string
            $sizeSqr = NULL;                                        // clear var
            if ($stmt = $this->Mysqli->prepare("INSERT INTO games(player1_id, player1_score, grid, player_turn".($difficulty != NULL ? ", player2_id, player2_score" : "") .", start_time) VALUES('".$_SESSION['player_id']."',0,'$grid','".$_SESSION['player_id']."'".($difficulty != NULL ? ", ".AI_DIFFICULTY_ID[$difficulty].", 0" : "") .",NOW())")) {
                if ($stmt->execute()) {                             // execute query
                    return true;                                    // successfully created game
                } else {
                    $this->error .= "Failed to create new game, please try again later.\n"; // error failed to connect to db
                    return false;
                }
            } else {
                $this->error .= "Error connecting to server, try again later.\n";
                $this->error = $this->Mysqli->error;
                return false;
            }
        } else {
            $this->error .= "You are not logged in!\n";             //  error user is not logged in
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
     *      grid: array
     * }: an array of all the data needed for the game.
     */
    public function get_game_data(int $gameId) {
        if ($this->Login->login_check()) {                  // check if user is logged in
            if ($stmt = $this->Mysqli->prepare("SELECT player1_id, player2_id, TIMEDIFF(NOW(), start_time), player1_score, player2_score, grid FROM games WHERE game_id = ? LIMIT 1")) {
                $stmt->bind_param('i', $gameId);            // bind game id
                if ($stmt->execute()) {                     // execute
                    $stmt->bind_result($player1_id, $player2_id, $start_time, $player1_score, $player2_score, $grid);
                    $stmt->fetch();                         // fetch row
                    return [                                // return array
                        'game_id' => $gameId,               // game id
                        'player1_id' => $player1_id,        // player1 id
                        'player2_id' => $player2_id,        // player2 id
                        'start_time' => $start_time,        // start time
                        'player1_score' => $player1_score,  // player1 score
                        'player2_score' => $player2_score,  // player2 score
                        'grid' => $grid                     // game grid data
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
    public function get_scores(string $firstName = NULL, string $lastName = NULL, bool $includeAI = false, string $sortBy = "score", string $orderBy = "DESC"): array {
        $selection = "";                                                            // set selection
        if ($firstName != NULL || $lastName != NULL) {                              // if first name or last name isnt null
            $selection .= "WHERE ";                                                 // add WHERE
            if ($firstName != NULL) {                                               // if first name not null
                $firstName = filter_var($firstName, FILTER_SANITIZE_STRING);        // sanitize string
                $selection .= "p.first_name = '$firstName'";                        // add firstname to query
                if ($lastName != NULL || $includeAI) $selection .= "AND ";                        // if lastname is not null add AND
            }
            if ($lastName != NULL) {                                                // if last name is not null
                $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);          // sanitize string
                $selection .= "p.last_name = '$lastName'";                          // add lastname to query
                if ($includeAI) $selection .= "AND ";
            }
            if ($includeAI) {
                $selection .= "p.player_id > 0";
            }
        }
        if ($stmt = $this->Mysqli->prepare("SELECT p.first_name, p.last_name, g.score, TIMEDIFF(g.end_time, g.start_time) duration FROM (SELECT player1_id player_id, player1_score score, start_time, end_time FROM games UNION SELECT player2_id player_id, player2_score score, start_time, end_time FROM games) g LEFT JOIN players p USING(player_id) $selection ORDER BY $sortBy $orderBy")) {
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