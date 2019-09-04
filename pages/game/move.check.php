<pre>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/game.class.php";

$Game = new Game();
$data = $Game->get_game_data($_GET['id']);
print_r($data);
echo "<br/>";
echo $data['grid'];
for ($i = 0; $i < count($data['grid']); $i++) {
    if ($i % 6 == 0)
        echo "<br/>";
    echo $data['grid'][$i] . " ";
}
echo "<br/>------------<br/>";
print_r($Game->can_move($data, 1));
?>
</pre>