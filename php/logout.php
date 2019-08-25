<?php
include_once './classes/SecureSession.class.php';
sec_session_start();					// start secure session

/**
 * Logs the user out by clearing the session. Once finished
 * it will send the person to the index page.
 */

$_SESSION = array();					// clear _SESSION
$params = session_get_cookie_params();	// get params
setcookie(session_name(),				// clear all cookie data
		 '',
		 time() - 42000,
		 $params['path'],
		 $params['domain'],
		 $params['secure'],
		 $params['httponly']);
session_destroy();						// destroy session

header("LOCATION: ../index.html");		// redirect to index.html
?>