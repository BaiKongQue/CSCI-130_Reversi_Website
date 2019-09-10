<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

$res = [];
$Game = new Game();
$data = $Game->get_game_data($_GET['id']);

$res['result'] = $Game->moves_array($data);
if (!empty($Game->error))
    $res['error'] = $Game->error;
echo json_encode($res)
?>