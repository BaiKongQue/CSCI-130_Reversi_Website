<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/classes/game.class.php';

/**
 * GET:
 *  player_id
 *  sort
 *  order
 */

$Game = new Game();
echo json_encode($Game->get_scores(
    (isset($_GET['first_name'])) ? $_GET['first_name'] : NULL,
    (isset($_GET['last_name'])) ? $_GET['last_name'] : NULL,
    (isset($_GET['sort'])) ? $_GET['sort'] : 'score',
    (isset($_GET['order'])) ? $_GET['order'] : 'DESC'
));
?>