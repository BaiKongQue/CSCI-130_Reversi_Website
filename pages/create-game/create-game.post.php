<?php
include_once $_SERVER['DOCUMENT_ROOT'] . './php/classes/game.class.php';

/**
 * POST:
 *  size
 *  sub
 */

$data = [];
if (isset($_POST['sub'], $_POST['size'])) {
    $Game = new Game();
    if ($Game->create_game($_POST['size'])) {
        $data['result'] = true;
    } else {
        $data['error'] = $Game->error;
        $data['result'] = false;
    }
} else {
    $data['error'] = "Please select a size";
    $data['result'] = false;
}

echo json_encode($data);

?>