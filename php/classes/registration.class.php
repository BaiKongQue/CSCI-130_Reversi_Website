<?php
include_once "SecureSession.class.php";
sec_session_start();

class Registration {
	private $username, $password, $passConfirm, $email, $Mysqli;
	private $_Option;
	public $errPass,
			$errConfirm,
			$errAdd,
			$errCaptcha,	// need to add
			$errEmail,
			$errUserN;
	
	public function __construct(){
		$this->_Option = json_decode(file_get_contents("C:/Apache24/htdocs/config/login_options.json"), true);
		
		$db = $this->_Option['Database'];
		$this->Mysqli = new mysqli($db['Host'], $db['Username'], $db['Password'], $db['Scheme']);
		
		$this->errAdd = $this->errCaptcha = $this->errConfirm = $this->errEmail = $this->errPass = $this->errUserN = "";
	}
	
	private function email_check(){
		if(!$this->_Option['Register']['Email']) return false;
		
		if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
			$this->errEmail .= "Please use a valid Email.\n";
			return false;
		}
		
		if($stmt = $this->Mysqli->prepare("SELECT email FROM members WHERE email = ? LIMIT 1")){
			$stmt->bind_param('s', $this->email);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1){
				$this->errEmail .= "This email address already exist! Please try again.\n";
				return false;
			} else
				return true;
			
			$stmt->free_result();
			$stmt->close();
		}
	}
	
	private function username_check(){
		if(!$this->_Option['Register']['Username'] && !$this->_Option['Register']['Unique_Username']) return false;
		
		if(strlen($this->username) > 30) {
			$this->errUserN .= "Username must be less than 30 characters.\n";
			return false;
		}
		
		if($stmt = $this->Mysqli->prepare("SELECT username FROM members WHERE username = ? LIMIT 1")){
			$stmt->bind_param('s', $this->username);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1 && $this->_Option['Register']['Unique_Username']){
				$this->errUserN .= "This username already exist! Please try again.\n";
				return false;
			} else
				return true;
			
			$stmt->free_result();
			$stmt->close();
		}
	}
	
	private function pass_check(){
		if($this->password != $this->passConfirm)
			$this->errPass .= "Passwords do not math.\n";
		else {
			if(!preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/', $this->password)) {
				$this->errPass = 'your password need a upper case letter (A-Z), one number (0-9), and one lower case letter (a-z).';
				return false;
			} else
				return true;
		}
	}

/* @name register
 * 
 * @param string user username 
 * @param string pass1 password
 * @param string pass2 confirmation password
 * @param string email users email
 * 
 * @return true if run well, false if there was a error.
 * 
 * @descripion registration function, checks all the user data, cleans it, verifies it, and makes it so only one email/username per user.
 */
	public function register($user, $email, $pass1, $pass2){
		if($this->_Option['Register']['Register_Allowed'] && ($this->_Option['Register']['Email'] || $this->_Option['Register']['Username'])){
			if(isset($user, $email, $pass1, $pass2)){
				$this->password = filter_var($pass1, FILTER_SANITIZE_STRING);
				$this->passConfirm = filter_var($pass2, FILTER_SANITIZE_STRING);
				
				$this->email = filter_var($email, FILTER_SANITIZE_STRING);
				$this->username = filter_var($user, FILTER_SANITIZE_STRING);
				$user = $pass1 = $pass2 = $email = NULL;
				
		
				if($this->email_check() || $this->username_check()){ //&& $this->pass_check()){
					$pass = hash('sha512', $this->password);
					$pass = password_hash($pass, PASSWORD_BCRYPT);
					$this->password = $this->passConfirm = NULL;
					
					if($stmt = $this->Mysqli->prepare("INSERT INTO members(username, email, password, last_login, date_created) VALUES(?, ?, ?, NOW(), NOW())")){
						$stmt->bind_param('sss', $this->username, $this->email, $pass);
						if(!$stmt->execute()){
							$this->errAdd .= "Failed to connect to server. Try again.\n";
							return false;
						} else
							return true;
					} else {
						$this->errAdd .= "There was a error connecting to the server. Try again.\n";
						return false;
					}
				}
			} else{
				$this->errAdd .= "One or more field is empty\n";
				return false;
			}
		} else {
			$this->errAdd .= "Registration is not available at this time, please try again later.\n";
			return false;
		}
	}

	public function __destruct(){
		$this->Mysqli->close();
		$this->email = $this->passConfirm = $this->password = $this->username = $this->Mysqli = NULL;
	}
}

?>