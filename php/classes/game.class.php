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
 *  player_turn: int
 *  duration: time
 *  grid: array(int)
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
    private function count_tiles() {
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

    private function is_horizontal_wall($index, $size): bool {
        return (intdiv($index, $size) == 0)
        || (intdiv($index, $size) == $size-1);
    }

    private function is_vertical_wall($index, $size): bool {
        return ($index % $size == 0)
        || ($index % $size == $size - 1);
    }

    private function is_wall($index, $isze): bool {
        return $this->is_horizontal_wall($index, $size) || $this->is_vertical_wall($index, $size);
    }

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
     * @return array {[key: int]: int}: where the key is the index and value is the number of tiles
     *  you will get in that spot
     */
    public function moves_array(array &$data): array {
        $grid = &$data['grid'];                                                          // reference grid in data
        $size = sqrt(count($grid));                                                     // hold size
        $player = ($data['player_turn'] == $data['player1_id']) ? GAME_TILE_PLAYER1 : GAME_TILE_PLAYER2; // get if player is 1 or 2 tile
        $res = [];                                                                      // init result array
        for ($index = 0; $index < count($grid); $index++) {                             // iterate through each spot
            if ($grid[$index] != GAME_TILE_NONE)                                        // if none
                continue;                                                               // skip
            foreach ([-$size, 0, $size] as $y) {                                        // for each y around the index
                foreach ([-1, 0 , 1] as $x) {                                           // for each x around the index
                    if (($x == 0 && $y == 0))                                           // if x and y are 0
                        continue;                                                       // skip
                    $spot = $index + $x + $y;                                           // calculate spot
                    $count = 0;                                                         // hold count
                    while (
                        ($spot > 0 && $spot < count($grid))                             // make sure spot is in bounds
                        && (!(($y != 0 && $this->is_horizontal_wall($spot, $size)))     // moving in y direction and is not wall
                            || !(($x != 0 && $this->is_vertical_wall($spot, $size))))   // moving in x direction and is not wall
                        && ($grid[$spot] != $player && $grid[$spot] != GAME_TILE_NONE)  // while spot is not player
                    ) {
                        $spot += $x + $y;                                               // step to next spot
                        $count++;                                                       // increment count
                    }

                    if ($spot!=$index && $count != 0 && $grid[$spot] == $player) {      // if not start and end spot is this player
                        if (!key_exists($index, $res)) {                                // if index not already in res
                            $res[$index] = $count;                                      // add it to res
                        } else {
                            $res[$index] += $count;                                     // add to res
                        }
                    }
                }
            }
        }
        return $res;                                                                    // return result
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
    public function create_game(int $size, string $difficulty = NULL): array {
        if ($this->Login->login_check()) {                          // check if user is logged in
            // sanitize inputs
            $size = filter_var($size, FILTER_SANITIZE_NUMBER_INT);  // sanitize the input
            // $vs = filter_var($vs, FILTER_SANITIZE_STRING);
            if ($difficulty != NULL)  $difficulty = filter_var($difficulty, FILTER_SANITIZE_STRING);

            // create initial grid
            $sizeSqr = $size*$size;
            $grid = array_fill(0, $sizeSqr, 0);                     // create game grid array
            $half = intdiv($sizeSqr, 2) - (intdiv($size, 2)) - 1;   // get middle of board
            $grid[$half] = GAME_TILE_PLAYER1;                       // player 1 tile
            $grid[$half + 1 + $size] = GAME_TILE_PLAYER1;           // player 1 tile
            $grid[$half + 1] = GAME_TILE_PLAYER2;                   // player 2 tile
            $grid[$half + $size] = GAME_TILE_PLAYER2;               // player 2 tile
            $grid = json_encode($grid);                             // turn array into json string
            $sizeSqr = NULL;                                        // clear var
            if ($stmt = $this->Mysqli->prepare("INSERT INTO games(player1_id, player1_score, grid, player_turn".($difficulty != NULL ? ", player2_id, player2_score" : "") .", start_time) VALUES('".$_SESSION['player_id']."',0,'$grid','".$_SESSION['player_id']."'".($difficulty != NULL ? ", ".AI_DIFFICULTY_ID[$difficulty].", 0" : "") .",NOW())")) {
                if ($stmt->execute()) {                             // execute query
                    return ['result' => true, "id" => $stmt->insert_id];                                    // successfully created game
                } else {
                    $this->error .= "Failed to create new game, please try again later.\n"; // error failed to connect to db
                    return ['result' => false];
                }
            } else {
                $this->error .= "Error connecting to server, try again later.\n";
                return ['result' => false];
            }
        } else {
            $this->error .= "You are not logged in!\n";             //  error user is not logged in
            return ['result' => false];
        }
    }

    /**
     * Retrieve the game data of specified game id.
     * @param int $gameId: id of the game
     * @return {
     *      game_id: int,
     *      player1_id: int,
     *      player2_id: int,
     *      duration: Time,
     *      player1_score: int,
     *      player2_score: int,
     *      player_turn: int,
     *      grid: array
     * }: an array of all the data needed for the game.
     */
    public function get_game_data(int $gameId) {
        if ($this->Login->login_check()) {                  // check if user is logged in
            if ($stmt = $this->Mysqli->prepare("SELECT player1_id, player2_id, TIMEDIFF(NOW(), start_time), player1_score, player2_score, player_turn, grid FROM games WHERE game_id = ? LIMIT 1")) {
                $stmt->bind_param('i', $gameId);            // bind game id
                if ($stmt->execute()) {                     // execute
                    $stmt->bind_result($player1_id, $player2_id, $duration, $player1_score, $player2_score, $player_turn, $grid);
                    $stmt->fetch();                         // fetch row
                    return [                                // return array
                        'game_id' => $gameId,               // game id
                        'player1_id' => $player1_id,        // player1 id
                        'player2_id' => $player2_id,        // player2 id
                        'duration' => $duration,            // duration
                        'player1_score' => $player1_score,  // player1 score
                        'player2_score' => $player2_score,  // player2 score
                        'player_turn' => $player_turn,      // player turn
                        'grid' => json_decode($grid)        // game grid data
                    ];
                } else {
                    $this->error .= "Error connecting to the server try again later.\n";    // error processing request
                    return false;
                }
                $stmt->free_result();                       // free result
                $stmt->close();                             // close connection
            } else {
                $this->error .= "Failed to connect to server, try again later.\n";
                $this->error = $this->Mysqli->error;
                return ['result' => false];
            }
        } else {
            $this->error .= "You are not logged in!\n";     // error not logged in
            return ['result' => false];
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
                $selection .= "p.first_name LIKE '$firstName%'";                    // add firstname to query
                if ($lastName != NULL) $selection .= "AND ";                        // if lastname is not null add AND
            }
            if ($lastName != NULL) {                                                // if last name is not null
                $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);          // sanitize string
                $selection .= "p.last_name LIKE '$lastName%'";                      // add lastname to query
            }
        }
        if ($stmt = $this->Mysqli->prepare("SELECT g.game_id, p.first_name, p.last_name, g.score, TIMEDIFF(g.end_time, g.start_time) duration FROM (SELECT game_id, player1_id player_id, player1_score score, start_time, end_time FROM games WHERE end_time IS NOT NULL UNION SELECT game_id, player2_id player_id, player2_score score, start_time, end_time FROM games WHERE end_time IS NOT NULL) g LEFT JOIN players p USING(player_id) $selection ORDER BY $sortBy $orderBy")) {
            $stmt->execute();                                                                   // run prepared query
            $stmt->bind_result($dbGameId, $dbFirstName, $dbLastName, $dbScore, $dbDuration);    // bind results
            $res = [];                                                                          // init res array
            while($stmt->fetch()) {                                                             // fetch each row
                $a = [];                                                                        // start new array
                $a['game_id'] = $dbGameId;
                $a['first_name'] = $dbFirstName;                                                // add first name
                $a['last_name'] = $dbLastName;                                                  // add last name
                $a['score'] = $dbScore;                                                         // add score
                $a['duration'] = $dbDuration;                                                   // duration
                array_push($res, $a);                                                           // push to res array
            }
            return $res;                                                                        // return res
            $stmt->free_result();                                                               // free results
            $stmt->close();                                                                     // close connection
        } else {
            $this->error .= "there was an error connecting to the server, try again later.\n"; // error preparing query
            return ['result' => false];
        }
    }

    public function get_player_icon(array $playerIds): array {
        $playerIds = array_filter($playerIds, "is_numeric");                            // filter the id to be a number
        if (!empty($playerIds)) {                                                       // check if its still there
            $playerIds = implode(",", $playerIds);                                      // turn array into string with ','
            if ($stmt = $this->Mysqli->prepare("SELECT player_id, icon FROM players WHERE player_id in ($playerIds)")) {    // prepare query
                $stmt->execute();                                                       // execute query
                $stmt->bind_result($playerId, $iconName);                               // bind results
                $res = [];                                                              // init result array
                while($stmt->fetch()) {                                                 // fetch each row
                    $res[$playerId] = $iconName;                                        // set icon file name to player id
                }
                return $res;                                                            // return result
            } else {
                $this->error .= "Failed to connect to server, try again later.\n";      // error with query
            }
        }
    }

    public function get_player_lobbies(): array {
        if ($this->Login->login_check()) {                                              // check if user is logged in
            if ($stmt = $this->Mysqli->prepare("
                select
                    games.game_id,
                    games.player1_id,
                    games.player1_score,
                    p1.first_name p1_first_name,
                    p1.last_name p1_last_name,
                    p1.icon p1_icon,
                    games.player2_id,
                    games.player2_score,
                    p2.first_name p2_first_name,
                    p2.last_name p2_last_name,
                    p2.icon p2_icon,
                    timediff(now(), games.start_time) duration
                from
                    {oj games
                        left outer join players p1 on games.player1_id = p1.player_id
                        left outer join players p2 on games.player2_id = p2.player_id}
                where
                    games.player1_id = ? or games.player2_id = ?
                ORDER BY games.game_id
            ")) {
                $stmt->bind_param('ii', $_SESSION['player_id'], $_SESSION['player_id']);    // bind the params
                $stmt->execute();                                                           // execute query
                $stmt->bind_result(                                                         // bind the results
                    $game_id, 
                    $p1_id, $p1_score, $p1_first_name, $p1_last_name, $p1_icon,
                    $p2_id, $p2_score, $p2_first_name, $p2_last_name, $p2_icon,
                    $duration
                );
                $res = [];                                                                  // init result array
                while($stmt->fetch()) {                                                     // fetch each row
                    $game = [];                                                             // array for each game
                    $game['game_id'] = $game_id;                                            // set game id
                    $game['player1_id'] = $p1_id;                                           // set p1 id
                    $game['player1_score'] = $p1_score;                                     // set p1 score
                    $game['player1_first_name'] = $p1_first_name;                           // set p1 first name
                    $game['player1_last_name'] = $p1_last_name;                             // set p1 last name
                    $game['player1_icon'] = $p1_icon;                                       // set p1 icon
                    $game['player2_id'] = $p2_id;                                           // set p2 id
                    $game['player2_score'] = $p2_score;                                     // set p2 score
                    $game['player2_first_name'] = $p2_first_name;                           // set p2 first name
                    $game['player2_last_name'] = $p2_last_name;                             // set p2 last name
                    $game['player2_icon'] = $p2_icon;                                       // set p2 icon
                    $game['duration'] = $duration;                                          // set duration
                    array_push($res, $duration);                                            // push game to result array
                }
                return $res;                                                                // return result
            } else {
                $this->error .= "Failed to connect to server, try again later.\n";          // error with query
            }
        } else {
            $this->error .= "You are not logged in!\n";                                     // user is not logged in
        }
    }

    public function __deconstruct() {
        $this->Mysqli->close();     // close mysql connection
        $this->Mysqli = NULL;       // set null
    }
}

?>