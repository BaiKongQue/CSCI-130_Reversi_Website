<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * POST:
 *  data
 * @param array data: package of data
 */
$data = [];
if (isset($_POST['data'], $_POST['index']) && !empty($_POST['data'])) {
    $Game = new Game();
    $data['result'] = $Game->update_game_data(json_decode($_POST['data'], true), $_POST['index'], (isset($_POST['ai']) ? TRUE : FALSE));
    if ($Game->error != "") $data['error'] = $Game->error;
} else {
    $data['error'] = "one or more parameter is missing!";
    $data['result'] = false;
}

echo json_encode($data);
?>