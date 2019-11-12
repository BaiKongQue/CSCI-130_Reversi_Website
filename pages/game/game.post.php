<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

/**
 * POST:
 *  data
 * @param array data: package of data
 */
$data = [];
if (isset($_POST['data'], $_POST['index']) && !empty($_POST['data']) && !empty($_POST['index'])) {
    $Game = new Game();
    $data['result'] = $Game->update_game_data($_POST['data'], $_POST['index']);
    if ($Game->error != "") $data['error'] = $Game->error;
} else {
    $data['error'] = "one or more parameter is missing!";
    $data['result'] = false;
}

echo json_encode($data);
?>