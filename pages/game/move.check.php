<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

/**
 * POST:
 *  data: array
 * @param array data: game data package
 * @return array {result: {[key: int]: int}, error?: string}: where the key is the index and value is the number of tiles
 *  you will get in that spot
 */
 
$res = [];                                      // init res
if (isset($_POST['data']) && !empty($_POST['data'])) {
    $data = json_decode($_POST['data']);
    $Game = new Game();                             // new Game object
    $res['result'] = $Game->moves_array($data);     // store result of moves
    if (!empty($Game->error))                       // if error
        $res['error'] = $Game->error;               // record error
} else {
    $res['result'] = false;
    $res['error'] = "Game data is missing!";
}
echo json_encode($res)                          // return json encoded res
?>