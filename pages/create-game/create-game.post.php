<?php
include_once $_SERVER['DOCUMENT_ROOT'] . './php/classes/game.class.php';

/**
 * POST:
 *  size
 *  vs
 *  sub
 */

$data = [];
if (isset($_POST['sub'], $_POST['size'], $_POST['vs'])) {
    if ($_POST['vs'] == "player" || ($_POST['vs'] == "computer" && isset($_POST['difficulty']))) {
        $Game = new Game();
        if ($Game->create_game($_POST['size'], $_POST['vs'], $_POST['difficulty'])) {
            $data['result'] = true;
        } else {
            $data['error'] = $Game->error;
            $data['result'] = false;
        }
    } else {
        $data['error'] = "Please select a computer difficulty to face.";
        $data['result'] = false;
    }
} else {
    $data['error'] = "One or more field is empty!";
    $data['result'] = false;
}

echo json_encode($data);

?>