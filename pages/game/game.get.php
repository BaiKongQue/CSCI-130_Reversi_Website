<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

/**
 * GET:
 *  id: int
 * @param int id: id of the game to process
 * @return array {result: array, error?: string}: result will contain the
 * game data array.
 */
 
$res = [];                                              // init res
$Game = new Game();                                     // new Game object
$res['result'] = $Game->get_game_data($_GET['id']);     // get game data
if (!empty($Game->error))                               // if error
    $res['error'] = $Game->error;                       // record error
echo json_encode($res)                                  // return json encoded res
?>