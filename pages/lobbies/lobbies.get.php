<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

/**
 * GET:
 *  id: int
 * @param int id: the player id to get on going games
 * @return {result: array, error?: string}: result holds the array of lobbies info
 */
$data = [];
$Game = new Game();
$data['result'] = $Game->get_player_lobbies($_GET['id']);
if ($Game->error != "")
    $data['error'] = $Game->error;
echo json_encode($data);
?>