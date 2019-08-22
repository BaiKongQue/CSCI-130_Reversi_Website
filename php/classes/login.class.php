<?php
include_once "SecureSession.class.php";
include_once "../config/definitions.php";
sec_session_start();

class Login{
// PRIVATE
	private $password;
	private $user_id;
	private $Mysqli;
	private $db_pass;
	private $username;
	private $db_username;
	private $db_email;
	
// PUBLIC
	public $err;
	
	public function __construct() {
		$this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_SCHEME);
		
		$this->err = '';
	}

// PRIVATE
	private function set_sessions(){
		$user_id = preg_replace("/[^0-9]+/", "", $this->user_id);
		
		$_SESSION['user_id'] = $user_id;
		if($this->_Option['Register']['Username'])
			$_SESSION['username'] = $this->db_username;
		$_SESSION['login_string'] = hash('sha512', $user_id . ($this->_Option['Register']['Username'] ? $this->db_username : "") . $this->acc_type . $_SERVER['HTTP_USER_AGENT']);
	}
	
	private function pass_check(){
		if(!$this->_locked){
			$pass = hash('sha512', $this->password);
			
			if(password_verify($pass, $this->db_pass) == TRUE){
				if(password_needs_rehash($this->db_pass, PASSWORD_BCRYPT)){
					$npass = password_hash($pass, PASSWORD_BCRYPT);
					if($uu = $this->Mysqli->prepare("UPDATE members SET password = ? WHERE user_id = ? AND " . $this->_UoE . " = ?")){
						$uu->bind_param('sis', $npass, $this->user_id, $this->username);
						$uu->execute();
						
						return true;
						$npass = $pass = null;
						$uu->close();
					} else 
						return false;
				}
				return true;
			} else
				return false;
		}
	}
	
	private function checkbrute(){
		$now = time();
		$valid_attempts = $now - ($this->_Option['Login']['Hour_Attempts'] * 60 * 60);
		
		if($stmt = $this->Mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) {
			$stmt->bind_param('i', $this->user_id);
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows >= $this->_Option['Login']['Num_Attempts']) {
				$this->_locked = true;
				return true;
			 } else {
				$this->_locked = false;
				return false;
			 }
		}
	}
	
	private function failed_attempt(){
		$now = time();
		if($stmt = $this->Mysqli->prepare("INSERT INTO login_attempts(user_id, time) VALUES(?,?)")){
			$stmt->bind_param('ss', $this->user_id, $now);
			if(!$stmt->execute())
				$this->err = "Failed to connect to server.";
		}
	}
	
	
	private function check_user(){
		$add = '';
		$e = ($this->_Option['Register']['Email']) ? "email" : "";
		$u = ($this->_Option['Register']['Username']) ? "username" : "";
		if($u != '') $add .= ", " . $u;
		if($e != '') $add .= ", " . $e;
		
		if($stmt = $this->Mysqli->prepare("SELECT user_id, password" . $add . " FROM members WHERE " . $this->_UoE . " = ? LIMIT 1")){
			$stmt->bind_param('s', $this->username);
			$stmt->execute();
			
			// collects statement result into a array
			$meta = $stmt->result_metadata(); 
		    while ($field = $meta->fetch_field()) 
		        $params[] = &$row[$field->name];
		
			call_user_func_array(array($stmt, 'bind_result'), $params); 

		    while ($stmt->fetch()) { 
		        foreach($row as $key => $val) 
		            $c[$key] = $val;
		        $result[] = $c; 
		    } 			
			// end
			$result = $result[0];
			
			$this->user_id = $result['user_id'];
			$this->db_pass = $result['password'];
			if($this->_Option['Register']['Email']) $this->db_email = $result['email'];
			if($this->_Option['Register']['Username']) $this->db_username = $result['username'];
			
			if($stmt->num_rows == 1)
				return true;
			else
				return false;
			
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
		if($this->_Option['Login']['Login_Allowed']){		
			if (isset($u, $p)){
				$this->username = filter_var($ue, FILTER_SANITIZE_STRING);
				$this->password = filter_var($p, FILTER_SANITIZE_STRING);
				$ue = $p = null;
				
				if($this->check_user() && !$this->checkbrute() && $this->pass_check()){
					$this->set_sessions();
					$this->update_last_login();
					return true;
				} elseif($this->_locked) {
					$this->err .= "Account locked, please wait about " . $this->_Option['Login']['Hour_Attempts'] . "hr" . ($this->_Option['Login']['Hour_Attempts'] > 1 ? "s" : "") . " to try again.";
					return false;
				} else {
					$this->err .= "Email/Username or Password is incorrect.\n";					
					if($this->user_id != null) $this->failed_attempt();
					return false;
				}
			} else {
				$this->err .= "Email/Username or Password is empty.\n";
				return false;
			}
		} else {
			$this->err .= "Login is not Available at this time please try again later.\n";
			return false;
		}
	}
	
	public function login_check() {
		if(isset($_SESSION['user_id'], $_SESSION['login_string']) && session_status() == PHP_SESSION_ACTIVE){
			$login_stringH = hash('sha512', $_SESSION['user_id'] . ($this->_Option['Register']['Username'] ? $_SESSION['username'] : "") . $_SESSION['acc_type'] . $_SERVER['HTTP_USER_AGENT']);
			if($_SESSION['login_string'] == $login_stringH) 
				return TRUE; 
			else
				return FALSE;
		} else	
			return FALSE;
	}
	
	
	
	public function __destruct(){
		$this->Mysqli->close();
		$this->db_pass = $this->password = $this->username = $this->user_id = $this->Mysqli = null;
	}
}


?>