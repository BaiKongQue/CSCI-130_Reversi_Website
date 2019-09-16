<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

/**
 * GET:
 *  id: int
 * @param int id: id of the game to process
 * @return array {result: array, error?: string}: result will contain the
 * game data array.
 */
 
$res = [];                                                  // init res
if (isset($_GET['id']) && $_GET['id'] == 0) {
    $res['result'] = json_decode(file_get_contents("dummy.json"));
} else if (isset($_GET['id']) && !empty($_GET['id'])) {
    $Game = new Game();                                     // new Game object
    $res['result'] = $Game->get_game_data($_GET['id']);     // get game data
    if (!empty($Game->error))                               // if error
        $res['error'] = $Game->error;                       // record error
} else {
    $res['result'] = false;                                 // result false
    $res['error'] = "game id is missing!";                  // game id missing
}
echo json_encode($res)                                      // return json encoded res
?>