<?php
include_once "./classes/SecureSession.class.php";

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
if (isset($_GET['data'])) {                                 // if GET data is set
    sec_session_start();                                    // start session
    $sessionData = [];                                      // hold session data
    foreach (explode(",", $_GET['data']) as $v) {           // foreach data
        if (!in_array($v, $_SESSION)) {                     // if not in session
            $data['error'] = "Error Processing request";    // error to data
            break;
        }
        $sessionData[$v] = $_SESSION[$v];                   // add to session data
    }
    if (empty($data['error'])) {                            // if no errors
        $data['result'] = $sessionData;                     // add session data to result
    }
} else {
    $data['error'] = "Error processing request.";           // error processing
}

echo json_encode($data);                                    // send data
?>