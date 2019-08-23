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

$data = [];
if (isset($_GET['data'])) {
    sec_session_start();
    $sessionData = [];
    foreach (explode(",", $_GET['data']) as $v) {
        if (!in_array($v, SESSION_KEYS)) {
            $data['error'] = "Error Processing request";
            break;
        }
        $sessionData[$v] = $_SESSION[$v];
    }
    if ($data['error'] != NULL) {
        $data['result'] = $sessionData;
    }
} else {
    $data['error'] = "Error processing request.";
}

echo json_encode($data)
?>