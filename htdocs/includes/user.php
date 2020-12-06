<?php

// `db_class_project`.`user`
// userid, display_name, login_name, password, join_date, last_login, premium_status


class User implements Serializable {
	
	// ERROR BIT FLAGS
	const NO_ERROR 					= 0;
	const LOGIN_NAME_MISSING 		= 1 << 0;
	const LOGIN_NAME_TOO_SHORT 		= 1 << 1;
	const LOGIN_NAME_TOO_LONG 		= 1 << 2;
	const PASSWORD_MISSING 			= 1 << 3;
	const PASSWORD_TOO_SHORT 		= 1 << 4;
	const NO_DB_CONNECTION	 		= 1 << 5;
	const CANT_CREATE_USER	 		= 1 << 6;
	const LOGIN_NAME_EXISTS 		= 1 << 7;
	const EMAIL_MISSING				= 1 << 8;
	const EMAIL_INVALID				= 1 << 9;
	const EMAIL_TOO_LONG			= 1 << 10;
	const DISPLAY_NAME_MISSING 		= 1 << 11;
	const DISPLAY_NAME_TOO_SHORT 	= 1 << 12;
	const DISPLAY_NAME_TOO_LONG 	= 1 << 13;
	const DISPLAY_NAME_INVALID	 	= 1 << 14;
	const DISPLAY_EQUALS_LOGIN	 	= 1 << 15;
	const DISPLAY_NAME_EXISTS	 	= 1 << 16;
	const CONFIG_ERROR	 			= 1 << 17;
	const EMAIL_EXISTS	 			= 1 << 18;
	const USER_LOGIN_INVALID		= 1 << 19;
	const SESSION_ALREADY_STARTED	= 1 << 20;
	const USER_NOT_LOGGED_IN		= 1 << 21;
	const CANT_CREATE_USER_STAT		= 1 << 22;
	// END OF ERROR BIT FLAGS
	
	const LOGIN_NAME_MIN_LENGTH = 6;
	const LOGIN_NAME_MAX_LENGTH = 30;
	const PASSWORD_MIN_LENGTH = 8;
	const EMAIL_MAX_LENGTH = 80;
	const DISPLAY_NAME_MIN_LENGTH = 3;
	const DISPLAY_NAME_MAX_LENGTH = 30;
	const DISPLAY_ALLOWABLE_CHARS = ['-','.','_','$','%','#','@','&'];
	
	static private $sessionStatus = false;
	
	
	static public function passHash($password) {
		return password_hash($password, PASSWORD_ARGON2ID, ['cost' => 10]);
	}
	
	static public function passVerify($password, $hashword) {
		return password_verify($password, $hashword);
	}
	
	static public function loginNameExists($login_name) {
		$exists = false;
		$conn = DB::$conn;
		if (!$conn) {
			throw new Exception('Unable to check if login_name exists in the database already.', self::NO_DB_CONNECTION);
		}
		
		$stmt =  $conn->stmt_init();
		
		if ( !$stmt->prepare('select count(*) as num_results from user where login_name = ? limit 1') ) {
		} else if ( !$stmt->bind_param('s', $login_name) ) {
		} else if (!$stmt->execute()) {
		} else if ( !($result = $stmt->get_result()) ) {
		} else if ( !($data = $result->fetch_array(MYSQLI_ASSOC)) ) {
		} else {
			if ($data['num_results'] == 1) {
				$exists = true;
			}
			$stmt->close();
		}
		
		return $exists;
	}
	
	static public function displayNameExists($display_name) {
		$exists = false;
		$conn = DB::$conn;
		if (!$conn) {
			throw new Exception('Unable to check if display_name exists in the database already.', self::NO_DB_CONNECTION);
		}
		
		$stmt =  $conn->stmt_init();
		if ( !$stmt->prepare('select count(*) as num_results from user where display_name = ? limit 1') ) {
		} else if ( !$stmt->bind_param('s', $display_name) ) {
		} else if (!$stmt->execute()) {
		} else if ( !($result = $stmt->get_result()) ) {
		} else if ( !($data = $result->fetch_array(MYSQLI_ASSOC)) ) {
		} else {
			if ($data['num_results'] == 1) {
				$exists = true;
			}
			$stmt->close();
		}
		
		return $exists;
	}
	
	static public function displayNameValid($display_name) {
		return ctype_alnum(str_replace(User::DISPLAY_ALLOWABLE_CHARS, '', $display_name));
	}
	
	static public function insertNew($config) {
		
		if (!is_array($config) || !isset($config['login_name']) || !isset($config['display_name']) ||
		!isset($config['password']) || !isset($config['email']) ) {
			throw new Exception('User::insertNew requires $config to be array with keys: login_name,
			display_name, password, and email.', self::CONFIG_ERROR);
		}
		
		$conn = DB::$conn;
		if (!$conn) {
			throw new Exception('Unable to insert new user into the database.', self::NO_DB_CONNECTION);
		}
		
		$create_errors = self::NO_ERROR;
		$userid = 0;
		
		$conflicts = self::getLoginDisplayAndEmailConflicts($config);
		
		
		if (!isset($config['login_name'])) {
			$create_errors |= self::LOGIN_NAME_ERROR;
		} else if (strlen($config['login_name']) < self::LOGIN_NAME_MIN_LENGTH) {
			$create_errors |= self::LOGIN_NAME_TOO_SHORT;
		} else if (strlen($config['login_name']) > self::LOGIN_NAME_MAX_LENGTH) {
			$create_errors |= self::LOGIN_NAME_TOO_LONG;
		} else if ($conflicts['login_name']) {
			$create_errors |= self::LOGIN_NAME_EXISTS;
		}
		
		if (!isset($config['display_name'])) {
			$create_errors |= self::DISPLAY_NAME_MISSING;
		} else if ($config['display_name'] === $config['login_name']) {
			$create_errors |= self::DISPLAY_EQUALS_LOGIN;
		} else if (strlen($config['display_name']) < self::DISPLAY_NAME_MIN_LENGTH) {
			$create_errors |= self::DISPLAY_NAME_TOO_SHORT;
		} else if (strlen($config['display_name']) > self::DISPLAY_NAME_MAX_LENGTH) {
			$create_errors |= self::DISPLAY_NAME_TOO_LONG;
		} else if (!self::displayNameValid($config['display_name'])) {
			$create_errors |= self::DISPLAY_NAME_INVALID;
		} else if ($conflicts['display_name']) {
			$create_errors |= self::DISPLAY_NAME_EXISTS;
		}
		
		if (!isset($config['password'])) {
			$create_errors |= self::PASSWORD_MISSING;
		} else if (strlen($config['password']) < self::PASSWORD_MIN_LENGTH) {
			$create_errors |= self::PASSWORD_TOO_SHORT;
		}
		
		if (!isset($config['email'])) {
			$create_errors |= self::EMAIL_MISSING;
		} else if (strlen($config['email']) > self::EMAIL_MAX_LENGTH) {
			$create_errors |= self::EMAIL_TOO_LONG;
		} else if (!filter_var($config['email'], FILTER_VALIDATE_EMAIL)) {
			$create_errors |= self::EMAIL_INVALID;
		} else if ($conflicts['email']) {
			$create_errors |= self::EMAIL_EXISTS;
		}
		
		
		if ($create_errors === self::NO_ERROR) {
			$stmt =  $conn->stmt_init();
			$hashed_password = self::passHash($config['password']);
			if ( !$stmt->prepare('insert into user (login_name, display_name, password, email) values (?, ?, ?, ?)') 		||
			!$stmt->bind_param('ssss', $config['login_name'], $config['display_name'], $hashed_password, $config['email']) 	||
			!$stmt->execute() 																								||
			!($userid = $conn->insert_id)
			) {
				$create_errors |= self::CANT_CREATE_USER;
			} else {
				$stmt->close();
				
				// userid, strength, defense, speed, dexterity
				$stmt =  $conn->stmt_init();
				if ( !$stmt->prepare('insert into user_stat (userid, strength, defense, speed, dexterity) values (?, 1,1,1,1)')	||
				!$stmt->bind_param('s', $userid) 																		||
				!$stmt->execute()
				) {
					$create_errors |= self::CANT_CREATE_USER_STAT;
				} else {
					$stmt->close();
				}
			}
		}
		
		if ($create_errors !== self::NO_ERROR) {
			throw new Exception('Unable to insert new user into the database.', $create_errors);
		}
		
		return $userid;
	}
	
	/*
		This is a helper method for User::insertNew
	*/
	static private function getLoginDisplayAndEmailConflicts($config) {
		$conflicts = [
			'email' => null,
			'login_name' => null,
			'display_name' => null,
		];
		$conn = DB::$conn;
		if (!$conn) {
			throw new Exception('Unable to check if login_name, display name, or email exists in the database already.', self::NO_DB_CONNECTION);
		}
		
		$stmt =  $conn->stmt_init();
		
		if ( !$stmt->prepare('
(select count(*) as conflict, "login_name" as conflict_type from user where login_name = ? limit 1) union
(select count(*) as conflict, "display_name" as conflict_type from user where display_name = ? limit 1) union
(select count(*) as conflict, "email" as conflict_type from user where email = ? limit 1);
		') ) {
		} else if ( !$stmt->bind_param('sss', $config['login_name'], $config['display_name'], $config['email']) ) {
		} else if (!$stmt->execute()) {
		} else if ( !($result = $stmt->get_result()) ) {
		} else {
			while($data = $result->fetch_array(MYSQLI_ASSOC)) {
				$conflicts[$data['conflict_type']] = $data['conflict'];
			}
			$stmt->close();
		}
		
		return $conflicts;
	}
	
	
	static public function login($login_name, $password) {
		
		$conn = DB::$conn;
		if (!$conn) {
			throw new Exception('Unable to load user for login.', self::NO_DB_CONNECTION);
		}
		
		// userid, display_name, login_name, password, join_date, last_login, premium_status
		$stmt =  $conn->stmt_init();
		if ( !$stmt->prepare('select userid, display_name, password, join_date, last_login, premium_status
		from user where login_name = ?') ) {
		} else {
			if ( !$stmt->bind_param('s', $login_name) ) {
			} else if (!$stmt->execute()) {
			} else if ( !($result = $stmt->get_result()) ) {
			} else if ( !($data = $result->fetch_array(MYSQLI_ASSOC)) ) {
			} else if (self::passVerify($password, $data['password'])) {
				$stmt->close();
				unset($data['password']);
				self::startSession();
				$_SESSION['user'] = new self($data);
				
				$stmt =  $conn->stmt_init();
				if ($stmt->prepare('update user set last_login = now() where userid = ?') &&
				$stmt->bind_param('s', $_SESSION['user']->userid) &&
				$stmt->execute()) {} else {print_r($stmt->error_list); die;}
				$stmt->close();
				
				// userid, strength, defense, speed, dexterity
				$stmt =  $conn->stmt_init();
				if ($stmt->prepare('select * from user_stat where userid = ?') &&
				$stmt->bind_param('s', $_SESSION['user']->userid) &&
				$stmt->execute()) {
					$stats_result= $stmt->get_result();
					$stats = $stats_result->fetch_array(MYSQLI_ASSOC);
					var_dump($stats);
					$_SESSION['user']->strength = $stats['strength'];
					$_SESSION['user']->defense = $stats['defense'];
					$_SESSION['user']->speed = $stats['speed'];
					$_SESSION['user']->dexterity = $stats['dexterity'];
				} else {print_r($stmt->error_list); die;}
				$stmt->close();
				return $_SESSION['user'];
			}
			$stmt->close();
		}
		throw new Exception('User login not validated.', self::USER_LOGIN_INVALID);
	}
	
	static private function startSession() {
		if (self::$sessionStatus === true) {
			throw new Exception('Session already started!', self::SESSION_ALREADY_STARTED);
		}
		
		session_start([
			'cookie_lifetime' => 60 * 60,
		]);
		self::$sessionStatus = true;
	}
	
	static private function endSession() {
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		self::$sessionStatus = false;
	}
	
	static public function load() {
		self::startSession();
		if (!isset($_SESSION['user'])) {
			throw new Exception('User is not logged in.', self::USER_NOT_LOGGED_IN);
		}
		$_SESSION['user'] = unserialize($_SESSION['user']);
		set_display_name($_SESSION['user']->display_name);
		return $_SESSION['user'];
	}
	
	static public function logout() {
		self::endSession();
	}
	
	public $userid;
	
	public $display_name;
	
	public $join_date;
	
	public $last_login;
	
	public $premium_status;
	
	public $strength;
	
	public $defense;
	
	public $speed;
	
	public $dexterity;
	
	public function __construct($config) {
		$this->userid = $config['userid'] ?? null;
		$this->display_name = $config['display_name'] ?? null;
		$this->join_date = $config['join_date'] ?? null;
		$this->last_login = $config['last_login'] ?? null;
		$this->premium_status = $config['premium_status'] ?? null;
	}
	
	public function serialize() {
		return serialize([
			$this->userid,
			$this->display_name,
			$this->join_date,
			$this->last_login,
			$this->premium_status,
			$this->strength,
			$this->defense,
			$this->speed,
			$this->dexterity,
		]);
	}
	
	public function unserialize($data) {
		list(
			$this->userid,
			$this->display_name,
			$this->join_date,
			$this->last_login,
			$this->premium_status,
			$this->strength,
			$this->defense,
			$this->speed,
			$this->dexterity
		) = unserialize($data);
	}
	
	private $doNotSave = false;
	
	public function doNotSave() {
		$this->doNotSave = true;
	}
	
	public function __destruct() {
		if (!$this->doNotSave) {
			$_SESSION['user'] = serialize($this);
		}
	}
}































