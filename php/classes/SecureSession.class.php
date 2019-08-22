<?php
function sec_session_start() {
	if(session_status() == PHP_SESSION_NONE){
		$config = json_decode(file_get_contents("C:/Apache24/htdocs/config/login_options.json"), true);
		$config = $config['Session'];	
		
		$session_name = 'sec_session_id';
		$secure = $config['Secure'];
		$httponly = true;
		
		if (ini_set('session.use_only_cookies', 1) === FALSE){
			header("Location: ./error.php?err=could not initiate a safe session (ini_set)");
			exit();
		}
		
		$cookieParams = session_get_cookie_params();
		$lifetime = ($config['LifeTime'] <= FALSE)? $cookieParams['lifetime'] : $config['LifeTime'];
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