<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/php/classes/login.class.php";
echo "HERE";
/**
 * POST:
 *  string username
 *  string password
 */
$err = "";
$Login = new Login();
if ($Login->login_check()) {
    if (isset($_POST['username'], $_POST['password'])) {
        return $Login->run_login($_POST['username'], $_POST['password']);
    } else {
        $err .= "Username or Password was empty!";
        return false;
    }
}

?>