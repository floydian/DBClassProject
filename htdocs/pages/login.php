<?php

set_page_title("Login");


$login_name = $_REQUEST['login_name'] ?? null;
$password = $_REQUEST['password'] ?? null;
$message = $message ?? ''; // index.php?p=register and index.php?p=logout will set $message for use on this page.
if (!is_null($login_name) && !is_null($password))  {

	try {
		db_connect();
	} catch (Exception $error) {
		set_page_body("Sorry, but something went wrong. Please check back later.");
		return; 
	}
	
	try {
		$user = User::login($login_name, $password);
		$message = sprintf('Welcome back %s!. Please wait while we load the game.', $user->display_name);
		$message.= <<<EOT
<script>
window.location.href = "index.php";
</script>
EOT;
	} catch (Exception $error) {
		$message = 'The login name and password combination are invalid.';
	}
	
}


$login_name_safe = htmlentities($login_name);

set_page_body(<<<EOT
<h2>Login to Query With The Fishes</h2>
<form method="post" action="index.php?p=login">
	Login name <input type="text" name="login_name" value="$login_name_safe"><br>
	Password <input type="password" name="password"><br>
	<input type="submit" value="Login">
</form>
<p>$message</p>
EOT
);



















