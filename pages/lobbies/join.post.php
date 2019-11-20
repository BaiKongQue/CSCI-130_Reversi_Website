<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

if(isset($_POST['game_id'], $_POST['player1_id']) && !empty($_POST['player_id'])){
    if($_POST['player1_id'] != $_SESSION['player_id']){
        join_game($_POST['game_id']);
    }
}
  
?>
