<?php
include_once "./classes/game.class.php";

/**
 * GET:
 *  ids: list int
 * @param int ids: list of player ids separated by "," to retrieve icon names. (ex. 1,2,3,4)
 * @return {int: string}: where key is the player id and key is the icon name.
 */
$data = [];
if (isset($_GET['ids'])) {
    $Game = new Game();
    $res = $Game->get_player_icon(explode(",", $_GET['ids']));
    if (!empty($res)) {
        $data['result'] = $res;
    } else if (empty($res) && $Game->error != "") {
        $data['error'] = $Game->error;
    }
} else {
    $data['result'] = false;
    $data['error'] = "Missing a parameter.";
}

echo json_encode($data);
?>