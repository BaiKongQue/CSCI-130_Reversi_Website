<?php
include_once "./classes/login.class.php";

$Login = new Login();
echo json_decode(["result" => $Login->login_check()]);

?>