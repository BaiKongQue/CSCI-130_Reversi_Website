<?php
include_once "./classes/login.class.php";

/**
 * Sends if the person is currently logged in or not.
 * 
 * @return json {"result": boolean}
 */
$Login = new Login();                                   // set login
$data = [];                                             // set data
$data['result'] = $Login->login_check();                // get if logged in
if ($Login->error != "")                                // if there is error
    $data['error'] = $Login->error;                     // log it
if ($data['result']) {                                  // if result is true
    foreach (['player_id', 'username', 'first_name', 'last_name', 'age', 'gender', 'location', 'icon'] as $k) { // get each
        $data['session'][$k] = $_SESSION[$k];           // session
    }
}
echo json_encode($data);                                // return if logged in

?>