<?php
include_once "SecureSession.class.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/definitions.php";
sec_session_start();

class Login{
// PRIVATE	
	private $Mysqli;
	
// PUBLIC
	public $error;
	
	public function __construct() {
		$this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_SCHEME);
		
		$this->error = '';
	}

// PRIVATE
	
	private function pass_check($userPassword, $dbPassword){
		$pass = hash('sha512', $userPassword);
		
		if(password_verify($pass, $dbPassword) == TRUE){
			if(password_needs_rehash($dbPassword, PASSWORD_BCRYPT)){
				$npass = password_hash($pass, PASSWORD_BCRYPT);
				if($uu = $this->Mysqli->prepare("UPDATE members SET password = ? WHERE user_id = ? AND username = ?")){
					$uu->bind_param('sis', $npass, $this->user_id, $this->username);
					$uu->execute();
					
					// return true;
					$npass = $pass = null;
					$uu->close();
				} else 
					return false;
			}
			return true;
		}
		
		return false;
	}
	
	
	private function login($userPassword, $username){		
		if($stmt = $this->Mysqli->prepare("SELECT user_id, username, password, first_name, last_name, age, gender, location, icon FROM members WHERE username = ? LIMIT 1")){
			$stmt->bind_param('s', $username);
			$stmt->execute();
			if($stmt->num_rows != 1) return false;
			$stmt->bind_result($dbUserId, $dbUsername, $dbPassword, $dbFirstName, $dbLastName, $dbAge, $dbGender, $dbLocation, $dbIcon);
			$stmt->fetch();
			
			if ($this->pass_check($userPassword, $dbPassword)) {
				// set session
				$user_id = preg_replace("/[^0-9]+/", "", $dbUserId);
		
				$_SESSION['user_id'] = $user_id;
				$_SESSION['username'] = $dbUsername;
				$_SESSION['first_name'] = $dbFirstName;
				$_SESSION['last_name'] = $dbLastName;
				$_SESSION['age'] = $dbAge;
				$_SESSION['gender'] = $dbGender;
				$_SESSION['location'] = $dbLocation;
				$_SESSION['icon'] = $dbIcon;
				$_SESSION['login_string'] = hash('sha512', $user_id . $username . $_SERVER['HTTP_USER_AGENT']);
				
				return true;
			}

			$stmt->free_result();
			$stmt->close;
		} else
			return false;
	}

	private function update_last_login(){
		if($stmt = $this->Mysqli->prepare("UPDATE members SET last_login = NOW() WHERE user_id = ?")){
			$stmt->bind_param('s', $this->user_id);
			if($stmt->execute())
				$stmt->close();
		}
	}

// PUBLIC
	public function run_login($u, $p){
		if(LOGIN_ALLOWED){		
			if (isset($u, $p)){
				$u = filter_var($u, FILTER_SANITIZE_STRING);
				$p = filter_var($p, FILTER_SANITIZE_STRING);
				$u = $p = null;
				if($this->login($u, $p)) {
					return true;
				} else {
					$this->error .= "Username or Password is incorrect.\n";
					return false;
				}
			} else {
				$this->error .= "Username or Password is empty.\n";
				return false;
			}
		} else {
			$this->error .= "Login is not Available at this time please try again later.\n";
			return false;
		}
	}
	
	public function login_check() {
		if(isset($_SESSION['user_id'], $_SESSION['login_string']) && session_status() == PHP_SESSION_ACTIVE){
			$login_stringH = hash('sha512', $_SESSION['user_id'] . $_SESSION['username'] . $_SESSION['acc_type'] . $_SERVER['HTTP_USER_AGENT']);
			if($_SESSION['login_string'] == $login_stringH) 
				return TRUE; 
			else
				return FALSE;
		} else	
			return FALSE;
	}
	
	public function __destruct(){
		$this->Mysqli->close();
		$this->password = $this->username = $this->user_id = $this->Mysqli = null;
	}
}


?>