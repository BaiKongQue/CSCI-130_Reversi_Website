<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

/**
 * GET:
 *  id: int
 * @param int id: id of the game to process
 * @return array {[key: int]: int}: where the key is the index and value is the number of tiles
 *  you will get in that spot
 */
 
$res = [];                                      // init res
$Game = new Game();                             // new Game object
$data = $Game->get_game_data($_GET['id']);      // get game data
$res['result'] = $Game->moves_array($data);     // store result of moves
if (!empty($Game->error))                       // if error
    $res['error'] = $Game->error;               // record error
echo json_encode($res)                          // return json encoded res
?>