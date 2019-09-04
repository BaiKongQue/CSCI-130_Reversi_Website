<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/classes/game.class.php';

/**
 * POST:
 *  size
 *  vs
 *  sub
 * 
 * @param int size: sqrt size of the board (4,6,9)
 * @param string vs: who the user is going agains ("player", "computer")
 * @param sub: if the submit button was recieved.
 * @return json {result: bool, error?: string} if the game was successfully created.
 */

$data = [];                                                                 // initialize data to send
if (isset($_POST['sub'], $_POST['size'], $_POST['vs'])) {                   // check if all posts are set
    if ($_POST['vs'] == "player" || ($_POST['vs'] == "computer" && isset($_POST['difficulty']))) { // check if vs is correct and if computer if difficulty is set
        $Game = new Game();                                                 // create new Game object
        if ($Game->create_game($_POST['size'], ($_POST['vs'] == 'computer' ? $_POST['difficulty'] : NULL))) { // create a new game
            $data['result'] = true;                                         // successfully created a new game
        } else {
            $data['error'] = $Game->error;                                  // error creating new game
            $data['result'] = false;                                        // result is false
        }
    } else {
        $data['error'] = "Please select a computer difficulty to face.";    // computer difficulty not set
        $data['result'] = false;                                            // result false
    }
} else {
    $data['error'] = "One or more field is empty!";                         // one or more field was empty
    $data['result'] = false;                                                // result false
}

echo json_encode($data);                                                    // return json result

?>