<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/definitions.php";

/**
 * Starts a secure session to use.
 */
function sec_session_start() {
	if(session_status() == PHP_SESSION_NONE){								// if there is no session
		if (ini_set('session.use_only_cookies', 1) === FALSE){				// if allowed to use cookies
			header("Location: ./error.php?err=could not initiate a safe session (ini_set)"); // relocate to error page
			exit();															// exit function
		}
		
		$cookieParams = session_get_cookie_params();						// get the cookie params
		session_set_cookie_params(											// set cookie params
			(SESSION_LIFE <= 0)? $cookieParams['lifetime'] : SESSION_LIFE,	// session life
			$cookieParams['path'],											// path
			$cookieParams['domain'],										// domain
			SESSION_SECURE,													// if secure
			SESSION_HTTP_ONLY);												// only http
		session_name(SESSION_NAME);											// name of session
		session_start();													// start session
		session_regenerate_id();											// regenerate id
	}
}
?>