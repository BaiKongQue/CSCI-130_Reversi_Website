<?php
include_once "SecureSession.class.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/definitions.php";
sec_session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Registration {
// PRIVATE
	private $Mysqli;
	private $dbHost;
    private $dbUser;
    private $dbPass;
	private $dbName;
	
// PUBLIC
	public $error;
	
	public function __construct(){
		$this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD); // start mysqli connection
		$this->error = "";	// initialize error to empty string
		if ($this->Mysqli->multi_query('
		CREATE DATABASE IF NOT EXISTS reversi;
		CREATE TABLE IF NOT EXISTS reversi.`players` (
			`player_id` int(11) NOT NULL AUTO_INCREMENT,
			`username` varchar(45) NOT NULL,
			`password` char(60) NOT NULL,
			`first_name` varchar(45) NOT NULL,
			`last_name` varchar(45) NOT NULL,
			`age` int(11) NOT NULL DEFAULT "1",
			`gender` enum("boy","girl","other") NOT NULL DEFAULT "other",
			`location` varchar(60) NOT NULL,
			`icon` varchar(45) NOT NULL,
			PRIMARY KEY (`player_id`),
			UNIQUE KEY `player_id_UNIQUE` (`player_id`),
			UNIQUE KEY `username_UNIQUE` (`username`)
		) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
		CREATE TABLE reversi.`games` (
			`game_id` int(11) NOT NULL AUTO_INCREMENT,
			`player1_id` int(11) NOT NULL,
			`player2_id` int(11) DEFAULT NULL,
			`player1_score` int(11) NOT NULL DEFAULT "0",
			`player2_score` int(11) DEFAULT NULL,
			`start_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`end_time` datetime DEFAULT NULL,
			`grid` json NOT NULL,
			`player_turn` int(11),
			PRIMARY KEY (`game_id`),
			UNIQUE KEY `game_id_UNIQUE` (`game_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
	')) {
			while ( $this->Mysqli->more_results() and $this->Mysqli->next_result() ) {
				$rs = $this->Mysqli->use_result();
				if( $rs instanceof \mysqli_result )
					$rs->free();
			}
		}
		$this->Mysqli->select_db(DB_SCHEME);
	}

// PRIVATE
	/**
	 * Checks if the username meets the REGISTER_USERNAME_MAX_LENGTH requirement and
	 * if the username is unique.
	 * @param string $username: username
	 * @return bool wether the username is valid or not.
	 */
	private function username_check($username){
		if(strlen($username) > REGISTER_USERNAME_MAX_LENGTH) {	// check if username is less than max len
			$this->error .= "Username must be less than " . REGISTER_USERNAME_MAX_LENGTH . " characters.\n"; // error username is >= max len
			return false;
		}

		if($stmt = $this->Mysqli->prepare("SELECT username FROM players WHERE username = ? LIMIT 1")){ // prepare select query to check if username exists
			$stmt->bind_param('s', $username);						// bind params
			$stmt->execute();										// execute
			$stmt->store_result();									// store the results

			$updated = $stmt->num_rows == 1;

			$stmt->free_result();									// free results
			$stmt->close();											// close connection
			if($updated){											// check if row exists
				$this->error .= "This username already exist!\n"; 	// error username exists
				return false;
			} else
				return true;										// username dne, return true
		} else {
			$this->error .= "Error reaching servers, please try again later.\n";
			return false;
		}
	}
	
	/**
	 * Checks if the password and passwordConf match, and if the password is contains
	 * one upper-case, one lower-case, and one number. Also checks if the password
	 * meets the REGISTER_PASSWORD_MIN_LENGTH requirement.
	 * @param string $password: password
	 * @param string $passwordConfirm: confirmation password
	 * @return bool wether the password passes all the requirements or not.
	 */
	private function pass_check($password, $passwordConfirm){
		if (strlen($password) < REGISTER_PASSWORD_MIN_LENGTH) {					// check if password is min len
			$this->error .= "Password must be a minimum length of " . REGISTER_PASSWORD_MIN_LENGTH . "\n"; // error password is less than min len
			return false;
		}
		
		if($password != $passwordConfirm) {										// check if passwords match
			$this->error .= "Passwords do not math.\n";							// error passwords do not match
			return false;
		}
		
		if(!preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/', $password)) {	// check if password contains one uppercase, one lowercase, and one number
			$this->error .= 'your password need a upper case letter (A-Z), one number (0-9), and one lower case letter (a-z).';	// error does not contain regex
			return false;
		}
		
		return true;
	}

// PUBLIC
	/**
	 * registration function, checks all the user data, cleans it, and verifies it.
	 * @param string $user: username 
	 * @param string $password: password
	 * @param string $passwordConfirm: confirmation password
	 * @param string $firstName: person's first name
	 * @param string $lastName: person's last name
	 * @param int	 $age: person's age
	 * @param string $gender: person's gender
	 * @param string $location: person's location
	 * @param string $icon: icon file name (no file extension)
	 * @return bool true if run well else false if there was a error.
	 */
	public function register(string $username, string $password, string $passwordConfirm, string $firstName, string $lastName, int $age, string $gender, string $location, string $icon){
		if (REGISTER_ALLOWED) {	// if registering is allowed
			if (isset($username, $password, $passwordConfirm, $firstName, $lastName, $age, $gender, $location, $icon)) {	// check if all fields filled
				// Sanatize all the post data
				$username = filter_var($username, FILTER_SANITIZE_STRING);
				$password = filter_var($password, FILTER_SANITIZE_STRING);
				$passwordConfirm = filter_var($passwordConfirm, FILTER_SANITIZE_STRING);
				$firstName = ucfirst(strtolower(filter_var($firstName, FILTER_SANITIZE_STRING)));
				$lastName = ucfirst(strtolower(filter_var($lastName, FILTER_SANITIZE_STRING)));
				$age = filter_var($age, FILTER_SANITIZE_NUMBER_INT);
				$gender = filter_var($gender, FILTER_SANITIZE_STRING);
				$location = ucwords(strtolower(filter_var($location, FILTER_SANITIZE_STRING)));
				$icon = filter_var($icon, FILTER_SANITIZE_STRING);

				if($this->username_check($username) && $this->pass_check($password, $passwordConfirm)){	// check if username is valid and unique, and if password is valid
					$pass = password_hash($password, PASSWORD_BCRYPT);									// hash password
					$password = $passwordConfirm = NULL;												// free password and passwordConfirm
					if($stmt = $this->Mysqli->prepare("INSERT INTO players(username, password, first_name, last_name, age, gender, location, icon) VALUES(?, ?, ?, ?, ?, ?, ?, ?)")){ // prepare mysqli insert query
						$stmt->bind_param('ssssisss', $username, $pass, $firstName, $lastName, $age, $gender, $location, $icon);	// bind params
						$res = !$stmt->execute();
						$stmt->close();
						if($res){																		// if not execute
							$this->error .= "Failed to connect to server. Try again.\n";				// error
							return false;
						} else
							return true;																// succesfully registered
					} else {
						$this->error .= "There was a error connecting to the server. Try again.\n";		// error preparing query
						return false;
					}
				}
			} else {
				$this->error .= "One or more field is empty\n";											// error not all fields filled out
				return false;
			}
		} else {
			$this->error .= "Registration is not available at this time, please try again later.\n";	// error preping query
			return false;
		}
	}

	public function __destruct(){
		$this->Mysqli->close();	// close mysqli connection
		$this->Mysqli = NULL;	// free Mysqli
	}
}

?>