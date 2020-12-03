<?php

set_page_title("Register");


$message = '';
$login_name = $_REQUEST['login_name'] ?? null;
$display_name = $_REQUEST['display_name'] ?? null;
$email = $_REQUEST['email'] ?? null;
$password = $_REQUEST['password'] ?? null;
if (!is_null($login_name) && !is_null($password) && !is_null($display_name) && !is_null($email))  {

	try {
		db_connect();
	} catch (Exception $error) {
		set_page_body("Sorry, but something went wrong. Please check back later.");
		return; 
	}
	
	try {
		$userid = User::insertNew([
			'login_name' => $login_name,
			'display_name' => $display_name,
			'email' => $email,
			'password' => $password,
		]);
		$message = sprintf('Thanks %s for registering! You may now login to the game.', htmlentities($display_name));
		require('login.php');
		return;
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
		
		if ($error_code & User::DISPLAY_NAME_MISSING) {
			$message .= 'Please enter a nickname.<br>';
		}
		if ($error_code & User::DISPLAY_NAME_EXISTS) {
			$message .= 'This nickname is already taken.<br>';
		}
		if ($error_code & User::DISPLAY_NAME_TOO_SHORT) {
			$message .= sprintf('The nickname must be at least %d characters long.<br>', User::DISPLAY_NAME_MIN_LENGTH);
		}
		if ($error_code & User::DISPLAY_NAME_TOO_LONG) {
			$message .= sprintf('The nickname must be no longer than %d characters long.<br>', User::DISPLAY_NAME_MAX_LENGTH);
		}
		if ($error_code & User::DISPLAY_NAME_INVALID) {
			$message .= 'The nickname contains invalid characters.<br>';
		}
		if ($error_code & User::DISPLAY_EQUALS_LOGIN) {
			$message .= 'The nickname must be different from the login.<br>';
		}
		
		if ($error_code & User::EMAIL_MISSING) {
			$message .= 'Please enter an email address.<br>';
		}
		if ($error_code & User::EMAIL_EXISTS) {
			$message .= 'This email is already taken.<br>';
		}
		if ($error_code & User::EMAIL_INVALID) {
			$message .= 'The email address is not valid.<br>';
		}
		if ($error_code & User::EMAIL_TOO_LONG) {
			$message .= sprintf('The email address must be no longer than %d characters long.<br>', User::EMAIL_MAX_LENGTH);
		}
		
		if ($error_code & User::PASSWORD_MISSING) {
			$message .= 'Please enter a password.<br>';
		}
		if ($error_code & User::PASSWORD_TOO_SHORT) {
			$message .= sprintf('The password must be at least %d characters long.<br>', User::PASSWORD_MIN_LENGTH);
		}
		if ( ($error_code & User::NO_DB_CONNECTION) || ($error_code & User::CANT_CREATE_USER) || ($error_code & User::CANT_CREATE_USER_STAT) ) {
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
$min_nick = User::DISPLAY_NAME_MIN_LENGTH;
$max_nick = User::DISPLAY_NAME_MAX_LENGTH;
$max_email = User::EMAIL_MAX_LENGTH;
$nick_allowable = implode(', ', User::DISPLAY_ALLOWABLE_CHARS);

$login_name_safe = htmlentities($login_name);
$display_name_safe = htmlentities($display_name);
$email_safe = htmlentities($email);

set_page_body(<<<EOT
<h2>Register for Query With The Fishes</h2>
<form method="post" action="index.php?p=register">
	Login name ($min_name to $max_name characters) <input type="text" name="login_name" value="$login_name_safe"><br>
	Email (maximum of $max_email characters) <input type="email" name="email" value="$email_safe"><br>
	Password (at least $min_pass characters) <input type="password" name="password"><br>
	Nickname ($min_nick to $max_nick letters, numbers, $nick_allowable) <input type="text" name="display_name" value="$display_name_safe"><br>
	<input type="submit" value="Register">
</form>
<p>$message</p>
EOT
);


























