<?php

set_page_title("Register");

try {
  $dbh = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}


$message = '';
$login_name = $_REQUEST['login_name'] ?? null;
$password = $_REQUEST['password'] ?? null;
if (!is_null($login_name) && !is_null($password))  {
	try {
		$userid = User::insertNew([
			'login_name' => $login_name,
			'password' => $password,
		]);
		$message = sprintf('User with userid %d was created!', $userid);
	} catch (Exception $error) {
		
		$error_code = $error->getCode();
		if ($error_code & User::LOGIN_NAME_MISSING) {
			$message .= 'Please enter a login name.<br>';
		}
		if ($error_code & User::LOGIN_NAME_EXISTS) {
			$message .= 'This login name is already taken.<br>';
		}
		if ($error_code & User::LOGIN_NAME_TOO_SHORT) {
			$message .= sprintf('The login name must be at least %d characters long.<br>', User::LOGIN_NAME_MIN_LENGTH);
		}
		if ($error_code & User::LOGIN_NAME_TOO_LONG) {
			$message .= sprintf('The login name must be no longer than %d characters long.<br>', User::LOGIN_NAME_MAX_LENGTH);
		}
		if ($error_code & User::PASSWORD_MISSING) {
			$message .= 'Please enter a password.<br>';
		}
		if ($error_code & User::PASSWORD_TOO_SHORT) {
			$message .= sprintf('The password must be at least %d characters long.<br>', User::PASSWORD_MIN_LENGTH);
		}
		if ( ($error_code & User::NO_DB_CONNECTION) || ($error_code & User::CANT_CREATE_USER) ) {
			$message .= 'The game is not able to register new users at this time. Please check back soon.<br>';
		}
		if (strlen($message) > 0) {
			$message = substr($message, 0, -4);
		}
	}
}


$min_name = User::LOGIN_NAME_MIN_LENGTH;
$max_name = User::LOGIN_NAME_MAX_LENGTH;
$min_pass = User::PASSWORD_MIN_LENGTH;

set_page_body(<<<EOT
<h2>Register for Query With The Fishes</h2>
<form method="post" action="index.php?p=register">
	Login name ($min_name to $max_name characters) <input type="text" name="login_name" value="$login_name"><br>
	Password (at least $min_pass characters) <input type="password" name="password">
	<input type="submit" value="Register">
</form>
<p>$message</p>
EOT
);


























