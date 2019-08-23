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
$data['error'] = '';

$Login = new Login();
if (!$Login->login_check()) {
    if (isset($_POST['username'], $_POST['password'])) {
        if(!$Login->run_login($_POST['username'], $_POST['password'])) {
            $data['error'] .= $Login->error;
            $data['result'] = false;
        } else 
            $data['result'] = true;
    } else {
        $data['error'] .= "Username or Password was empty!";
        $data['result'] = false;
    }
} else {
    $data['result'] = true;
}
echo json_encode($data);
?>