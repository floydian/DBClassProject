<?php

set_page_title("Create a Faction");


$message = '';
$faction_name = $_REQUEST['faction_name'] ?? null;
$display_name = $_REQUEST['power_level'] ?? null;
$rank = $_REQUEST['rank'] ?? null;

if (!is_null($faction_name) && !is_null($power_level) && !is_null($rank)) {

	try {
		db_connect();
	} catch (Exception $error) {
		set_page_body("Sorry, but something went wrong. Please check back later.");
		return; 
	}
	
	try {
		$factionid = Faction::insertNew([
			'faction_name' => $faction_name,
			'power_level' => $power_level,
			'rank' => $rank,
		]);
		$message = sprintf('You have made a new faction. Get some friends to join your cause.', htmlentities($faction_name));
		require('login.php');
		return;
	} 
	
	catch (Exception $error) {
		
		$error_code = $error->getCode();
		if ($error_code & Faction::FACTION_NAME_MISSING) {
			$message .= 'You forgot to name the family...<br>';
		}
		if ($error_code & Faction::FACTION_NAME_EXISTS) {
			$message .= 'You will not make it far if you try riding the coat tails of others. Pick an original name<br>';
		}
		if ($error_code & Faction::FACTION_NAME_TOO_SHORT) {
			$message .= sprintf('Faction names must be at least %d characters long.<br>', Faction::FACTION_NAME_MIN_LENGTH);
		}
		if ($error_code & Faction::FACTION_NAME_TOO_LONG) {
			$message .= sprintf('Faction names must be no longer than %d characters long.<br>', Faction::FACTION_NAME_MAX_LENGTH);
		}
		
		if (strlen($message) > 0) {
			$message = substr($message, 0, -4);
		}
	}
}


$min_name = Faction::FACTION_NAME_MIN_LENGTH;
$max_name = Faction::FACTION_NAME_MAX_LENGTH;
$faction_name_safe = htmlentities($faction_name);
$power_level_safe = htmlentities($power_level);
$rank_safe = htmlentities($rank);

set_page_body(<<<EOT
<h2>Create a Faction for Query With The Fishes</h2>
<form method="post" action="index.php?p=faction_create">
	Faction Name ($min_name to $max_name characters) <input type="text" name="faction_name" value="$faction_name_safe"><br>
	Power Level <input type="text" name="power_level" value="$power_level_safe"><br>
	Rank <input type="text" name="rank" value="$rank_safe"><br>
	<input type="submit" value="Create">
</form>
<p>$message</p>
<p>Need to create an account? <a href="index.php?p=register">Create one here.</a></p>
EOT
);