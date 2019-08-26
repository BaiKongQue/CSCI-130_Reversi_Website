<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/classes/game.class.php';

$Game = new Game();
return $Game->get_game_data();

?>