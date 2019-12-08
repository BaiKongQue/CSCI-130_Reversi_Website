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
		$this->Mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD);	// start new mysql connection
		$this->error = '';	// initialize error
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
			INSERT IGNORE INTO reversi.`players` VALUES (8,"paradox","$2y$10$xd2uKP0fk3/1Yr5WsIdPsOk7oKYziYWpuZmklLcTsrWwaaEP0M1X2","Alice","Doe",23,"girl","Internet","first avatar.jpg"),(9,"tgillette","$2y$10$e5BDQVKovL0WCsjjw/jfU.XS/qxo/iGp8XtOZ1sYVE/tJ7OlF0RUS","Tyler","Gillette",26,"boy","California","PKfav.jpg"),(10,"Zekhai","$2y$10$tM5HXoFKSIoKqD6Q.TTR9ujwI.TSwhVEZojv.B0vQ3TIo8REwEJUe","Khai","Pham",69,"boy","Freshoe","Profile.jpeg"),(11,"llp909","$2y$10$/S6RUSxTB.w6aSkhdnMm7ei641iuMCr7C429/y/DQFYTHwulUv1mW","Lee","Perkins",22,"boy","Fresno","hea_icon.jpg"),(12,"Test","$2y$10$6n9zyG/rZfFb42HzD/9Kluvc/WszonQhP132woJj5CimbrD7IX/S6","Bob","Joe",13,"boy","Fresno","side-peacock.jpg");
			INSERT IGNORE INTO reversi.`games` VALUES (32,9,12,0,0,"2019-11-08 15:56:07",NULL,"[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 0, 0, 0, 0, 0, 0, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]",9),(34,10,8,28,36,"2019-11-08 16:04:14","2019-11-22 14:41:26","[1, 2, 2, 2, 2, 2, 2, 2, 1, 2, 2, 1, 2, 2, 2, 2, 1, 1, 2, 2, 1, 2, 1, 2, 1, 1, 1, 1, 2, 2, 2, 2, 1, 2, 1, 1, 2, 2, 2, 2, 1, 1, 1, 1, 1, 2, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2]",0),(35,10,-2,0,0,"2019-11-08 16:07:30",NULL,"[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 0, 0, 0, 0, 0, 0, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]",10),(39,8,-1,46,18,"2019-11-15 09:35:45","2019-11-24 00:49:07","[2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1, 2, 1, 1, 1, 1, 2, 1, 1, 2, 1, 1, 1, 2, 2, 2, 1, 2, 1, 1, 2, 2, 1, 2, 2, 1, 1, 1, 2, 1, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]",0),(40,8,-1,22,14,"2019-11-15 16:21:29","2019-11-21 14:51:07","[2, 2, 1, 1, 1, 1, 1, 2, 2, 1, 2, 1, 1, 1, 2, 1, 2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 2, 1, 2, 1, 1, 1, 1, 1, 1, 2]",0),(41,8,-2,13,23,"2019-11-16 23:43:10","2019-11-21 15:09:46","[1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1, 1, 2, 2, 2, 2, 2, 1, 2, 2, 1, 1, 1, 1, 2]",0),(42,8,-2,30,34,"2019-11-21 12:53:16","2019-11-21 15:09:54","[2, 2, 2, 2, 2, 2, 2, 2, 1, 2, 1, 1, 1, 2, 2, 2, 1, 2, 1, 1, 2, 2, 1, 2, 1, 2, 1, 1, 1, 2, 1, 2, 1, 1, 2, 1, 1, 1, 1, 2, 1, 2, 1, 1, 1, 2, 2, 2, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 1, 1, 1, 2]",0),(43,8,-2,29,35,"2019-11-21 12:56:13","2019-11-21 15:10:00","[2, 1, 1, 1, 1, 1, 1, 1, 2, 2, 1, 2, 2, 2, 1, 1, 2, 2, 2, 1, 1, 1, 1, 1, 2, 1, 2, 2, 1, 1, 2, 1, 2, 1, 2, 2, 2, 1, 2, 1, 2, 2, 1, 1, 2, 2, 1, 1, 2, 2, 2, 2, 2, 2, 1, 1, 2, 2, 2, 2, 2, 2, 2, 1]",0),(44,8,-2,34,30,"2019-11-21 13:02:51","2019-11-21 15:10:06","[2, 2, 2, 2, 2, 2, 2, 1, 1, 2, 2, 2, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 1, 1, 1, 1, 2, 2, 1, 1, 2, 1, 1, 2, 1, 2, 1, 1, 2, 1, 1, 1, 1, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2]",0),(45,8,-2,18,46,"2019-11-21 13:38:11","2019-11-24 00:49:34","[2, 2, 2, 2, 2, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 1, 1, 2, 2, 1, 2, 2, 2, 1, 1, 1, 2, 2, 2, 1, 2, 1, 1, 2, 2, 2, 1, 1, 2, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2]",0),(46,10,8,39,25,"2019-11-21 15:54:14","2019-11-22 10:41:58","[1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 1, 1, 1, 1, 2, 1, 2, 2, 1, 2, 1, 1, 1, 2, 1, 2, 1, 2, 2, 1, 2, 1, 1, 2, 1, 2, 1, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1, 1, 2, 1, 1, 1, 1, 1, 1, 1, 2]",0),(47,8,11,18,46,"2019-11-23 17:54:08","2019-11-23 18:12:26","[2, 2, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 2, 1, 1, 2, 2, 2, 2, 2, 2, 1, 2, 1, 2, 2, 2, 2, 2, 1, 2, 2, 1, 2, 1, 2, 2, 1, 2, 1, 1, 1, 2, 2, 2, 1, 2, 2, 2, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2]",0),(48,8,-2,10,26,"2019-11-24 00:52:16","2019-11-24 00:53:08","[2, 2, 2, 2, 2, 2, 1, 2, 2, 1, 2, 2, 1, 2, 2, 1, 2, 2, 1, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 2, 2, 1, 1, 1, 1, 2]",0),(49,11,-1,20,16,"2019-11-24 01:24:21","2019-11-24 01:27:47","[2, 2, 2, 1, 1, 1, 1, 2, 1, 1, 1, 1, 2, 1, 2, 1, 1, 1, 2, 1, 1, 2, 1, 1, 2, 1, 1, 1, 2, 1, 2, 2, 2, 2, 2, 2]",0),(50,11,-1,9,7,"2019-11-24 01:28:07","2019-11-24 01:28:43","[2, 2, 1, 1, 1, 2, 2, 2, 1, 1, 2, 2, 1, 1, 1, 1]",0),(51,11,-1,2,2,"2019-11-24 01:29:00",NULL,"[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 0, 0, 0, 0, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]",11),(52,12,-2,4,9,"2019-12-06 15:52:39",NULL,"[2, 0, 2, 0, 0, 0, 2, 1, 2, 1, 0, 0, 2, 0, 2, 1, 0, 0, 0, 2, 2, 1, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0]",12),(53,12,8,15,21,"2019-12-06 15:54:58","2019-12-06 16:07:32","[2, 1, 1, 1, 1, 1, 2, 2, 1, 2, 1, 2, 2, 2, 2, 1, 2, 1, 2, 2, 2, 2, 1, 1, 2, 2, 2, 2, 1, 1, 2, 2, 2, 2, 1, 1]",0),(54,8,11,4,0,"2019-12-06 18:51:59",NULL,"[0, 0, 1, 0, 0, 1, 1, 0, 0, 2, 1, 0, 0, 0, 0, 0]",11),(57,11,8,2,0,"2019-12-08 12:09:25",NULL,"[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2, 0, 0, 0, 0, 0, 0, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]",11);
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
		// return true;
	}

	public function __destruct(){
		$this->Mysqli->close();	// close mysql connection
		$this->Mysqli = null; 	// clear
	}
}


?>