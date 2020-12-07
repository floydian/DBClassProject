<?php

set_page_title("Join a Faction");


$faction_name = $_REQUEST['login_name'] ?? null;
$login_name = $_REQUEST['login_name'] ?? null;
$display_name = $_REQUEST['display_name'] ?? null;

if (!is_null($faction_name) && !is_null($login_name) && !is_null($display_name))  {

	try {
		db_connect();
	} catch (Exception $error) {
		set_page_body("Sorry, but something went wrong. Please check back later.");
		return; 
	}
	
	try {
		$user = User::join($faction_name, $login_name);
		$message = sprintf('Welcome to the family. A job is waiting for you', $user->display_name);
		require('login.php');
		return;
	} 
	catch (Exception $error) {
		$error_code = $error->getCode();
		if ($error_code & Faction::FACTION_NAME_MISSING) {
			$message .= 'You did not specify which faction family to join<br>';
		}
	}
}


$faction_name_safe = htmlentities($faction_name);

set_page_body(<<<EOT
<h2>Join one of the Factions in Query With The Fishes</h2>
<form method="post" action="index.php?p=faction_join">
    Faction Name <input type="text" name="faction_name" value="$faction_name_safe"><br>
	Login name <input type="text" name="login_name"<br>
	<input type="submit" value="Join">
</form>
<p>$message</p>
<p>Need to create an account? <a href="index.php?p=register">Create one here.</a></p>
<p>Want to create a faction? <a href="index.php?p=faction_create">Create one here.</a></p>
EOT
);