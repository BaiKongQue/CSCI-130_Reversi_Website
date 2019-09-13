<?php
include_once "./classes/SecureSession.class.php";
include_once "./classes/Login.class.php";
/**
 * GET:
 *  data
 * 
 * Get one or more data from the session.
 * ex. request: ".../session.get.php?data=user_id"
 * @param GET data: data from session to retrieve (use ',' no spaces for multiple)
 * @return json {"result": [session data], "error"?: string}
 */

$data = [];                                                 // start data array
$Login = new Login();
$logged_in = $Login->login_check();
if (isset($_GET['data']) && $logged_in) {                   // if GET data is set
    sec_session_start();                                    // start session
    $sessionData = [];                                      // hold session data
    foreach (explode(",", $_GET['data']) as $v) {           // foreach data
        if (!isset($_SESSION[$v])) {                        // if not in session
            $data['error'] = "Error Processing request";    // error to data
            break;
        }
        $sessionData[$v] = $_SESSION[$v];                   // add to session data
    }
    if (empty($data['error'])) {                            // if no errors
        $data['result'] = $sessionData;                     // add session data to result
    }
} else if (!$logged_in) {
    $data['error'] = "Not logged in!";
} else {
    $data['error'] = "Error processing request.";           // error processing
}

echo json_encode($data);                                    // send data
?>