<?php
include_once "./classes/login.class.php";

/**
 * Sends if the person is currently logged in or not.
 * 
 * @return json {"result": boolean}
 */
$Login = new Login();                                   // set login
echo json_encode(["result" => $Login->login_check()]);  // return if logged in

?>