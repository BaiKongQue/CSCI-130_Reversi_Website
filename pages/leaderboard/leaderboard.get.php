<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/classes/game.class.php';

/**
 * GET:
 *  first_name
 *  last_name
 *  include_ai
 *  sort
 *  order
 * 
 * Get a list of the scores based on first name or last name, and sort
 * by specified attribute or order by specified DESC or ASC
 * 
 * @param string first_name: first name of person to find (leave empty or NULL for all)
 * @param string last_name: last name of person to find (leave empty or NULL for all)
 * @param int sort: attribute to sort by (first_name, last_name, score, duration)
 * @param string order: how to order the result (ASC, DESC)
 * @return json {first_name: string, last_name: string, score: int, duration: Time}[] an array of objects holding leaderboard data
 */
$data = [];
$Game = new Game();                                             // create new Game
$data['result'] = $Game->get_scores(                             // encode into json result
    (isset($_GET['first_name'])) ? $_GET['first_name'] : NULL, 
    (isset($_GET['last_name'])) ? $_GET['last_name'] : NULL,
    (isset($_GET['include_ai'])) ? $_GET['include_ai'] : false,
    (isset($_GET['sort'])) ? $_GET['sort'] : 'score',
    (isset($_GET['order'])) ? $_GET['order'] : 'DESC'
);
$data['error'] = $Game->error;
echo json_encode($data);
?>