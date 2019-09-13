<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/php/classes/login.class.php";

/**
 * POST:
 *  string username
 *  string password
 * 
 * processes POST data and attempts to log user in.
 * @return json {"result": booelan, "error"?: string}
 */
$data = [];
if (isset($_POST['username'], $_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $Login = new Login();
    if(!$Login->login_check() && $Login->run_login($_POST['username'], $_POST['password'])) {
        $data['result'] = true;
    } else if ($Login->login_check()) {
        $data['error'] = "Already Logged in!";
        $data['result'] = false;
    } else {
        $data['error'] = $Login->error;
        $data['result'] = false;
    }
} else {
    $data['error'] = "Username or Password was empty!";
    $data['result'] = false;
}
echo json_encode($data);
?>