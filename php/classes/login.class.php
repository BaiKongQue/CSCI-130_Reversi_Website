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
		$this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_SCHEME);	// start new mysql connection
		$this->error = '';	// initialize error
	}

// PRIVATE
	/**
	 * Check wether the passwords are valid or not.
	 * @param string $userPassword: the user's input password
	 * @param string $dbPassword: password from the database to check against
	 * @return bool: wether the password was valid or not.
	 */
	private function pass_check(string $userPassword, string $dbPassword): bool{
		// $pass = hash('sha512', $userPassword);
		$pass = $userPassword;
		
		if(password_verify($pass, $dbPassword) == TRUE){				// verify password against dbpassword
			if(password_needs_rehash($dbPassword, PASSWORD_BCRYPT)){	// check if dbpassword needs rehash
				$npass = password_hash($pass, PASSWORD_BCRYPT);			// generate new hash
				if($uu = $this->Mysqli->prepare("UPDATE members SET password = ? WHERE player_id = ? AND username = ?")){ // prepare update
					$uu->bind_param('sis', $npass, $this->player_id, $this->username);	// bind params
					$uu->execute();										// execute update
					return true;										// successfully updated and verified
					$npass = $pass = null;								// clear new password and old
					$uu->close();										// close connection
				} else 
					return false;										// error updating
			}
			return true;												// successfully verified password
		}
		
		return false;													// failed to verify password
	}
	
	/**
	 * Try and log user in and set session.
	 * @param string $username: user's input username
	 * @param string $userPassword: user's input password
	 * @return bool: wether the user successfully logged in
	 */
	private function login(string $username, string $userPassword): bool {
		if($stmt = $this->Mysqli->prepare("SELECT player_id, username, password, first_name, last_name, age, gender, location, icon FROM players WHERE username = ? LIMIT 1")){
			$stmt->bind_param('s', $username);							// bind params
			$stmt->execute();											// execute select statement
			$stmt->store_result();										// store result
			if ($stmt->num_rows != 1) return false; 					// if no rows user does not exist
			$stmt->bind_result($dbUserId, $dbUsername, $dbPassword, $dbFirstName, $dbLastName, $dbAge, $dbGender, $dbLocation, $dbIcon); // bind all the results
			$stmt->fetch();												// fetch row
			
			if ($this->pass_check($userPassword, $dbPassword)) { 		// check if password is valid
				// set session
				$player_id = preg_replace("/[^0-9]+/", "", $dbUserId);	// clean id of non numeric numbers
		
				$_SESSION['player_id'] = $player_id;					// player id
				$_SESSION['username'] = $dbUsername;					// username
				$_SESSION['first_name'] = $dbFirstName;					// first name
				$_SESSION['last_name'] = $dbLastName;					// last name
				$_SESSION['age'] = $dbAge;								// age
				$_SESSION['gender'] = $dbGender;						// gender
				$_SESSION['location'] = $dbLocation;					// location
				$_SESSION['icon'] = $dbIcon;							// icon
				$_SESSION['login_string'] = hash('sha512', $player_id . $username . $_SERVER['HTTP_USER_AGENT']); // hash login string to prevent non valid sessions
				
				return true;											// successfully logged in
			}

			$stmt->free_result();										// free results
			$stmt->close();												// close connection
		} else {
			$this->error .= "Error connecting to server!";				// failed connecting to server
		}
		
		return false;													// failed to login
	}

// PUBLIC
	/**
	 * Run and process the user's login.
	 * @param string $u: user's input username
	 * @param string $p: user's input password
	 * @return bool: wether the user successfully login or not
	 */
	public function run_login(string $u, string $p): bool {
		if(LOGIN_ALLOWED){														// if login system is on
			if (isset($u, $p)){													// if inputs are set
				$u = filter_var($u, FILTER_SANITIZE_STRING);					// sanitize u string
				$p = filter_var($p, FILTER_SANITIZE_STRING);					// sanitize p string
				if($this->login($u, $p)) {										// if login
					return true;												// successful login
				} else {
					$this->error .= "Username or Password is incorrect.\n";		// failed to login
					return false;
				}
				$u = $p = null;													// clear u and p
			} else {
				$this->error .= "Username or Password is empty.\n";				// field(s) empty
				return false;													// failed
			}
		} else {
			$this->error .= "Login is not Available at this time please try again later.\n";	// login system is offline
			return false;
		}
	}
	
	/**
	 * Check wether the user is currently logged in by checking the sessions.
	 * @return bool: wether the user is logged in or not
	 */
	public function login_check(): bool {
		if(isset($_SESSION['player_id'], $_SESSION['login_string']) && session_status() == PHP_SESSION_ACTIVE){				// if sessions are active
			$login_stringH = hash('sha512', $_SESSION['player_id'] . $_SESSION['username'] . $_SERVER['HTTP_USER_AGENT']);	// generate what login string should look like
			if($_SESSION['login_string'] == $login_stringH)	// compare
				return TRUE; 								// user is correctly logged in
			else
				return FALSE;								// login strings do not match fail check
		} else {
			return false;									// user not logged in, sessions not active
		}
	}
	
	public function __destruct(){
		$this->Mysqli->close();	// close mysql connection
		$this->Mysqli = null; 	// clear
	}
}


?>