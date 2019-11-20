<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

$data = [];
if(isset($_POST['game_id'])){
    $Game = new Game();
    $data['result'] = $Game->join_game($_POST['game_id']);
    if ($data['result'])
        $data['game_id'] = $_POST['game_id'];
    if ($Game->error != "")
        $data['error'] = $Game->error;
} else {
    $data['error'] = "One or more parameter is missing!";
    $data['result'] = false;
}

echo json_encode($data);
  
?>
