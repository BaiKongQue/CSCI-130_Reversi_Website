<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/definitions.php";

function sec_session_start() {
	if(session_status() == PHP_SESSION_NONE){		
		$session_name = 'sec_session_id';
		$secure = SESSION_SECURE;
		$httponly = true;
		
		if (ini_set('session.use_only_cookies', 1) === FALSE){
			header("Location: ./error.php?err=could not initiate a safe session (ini_set)");
			exit();
		}
		
		$cookieParams = session_get_cookie_params();
		$lifetime = (SESSION_LIFE <= FALSE)? $cookieParams['lifetime'] : SESSION_LIFE;
		session_set_cookie_params($lifetime,
			$cookieParams['path'],
			$cookieParams['domain'],
			$secure,
			$httponly);
		session_name($session_name);
		session_start();
		session_regenerate_id();
	}
}
?>