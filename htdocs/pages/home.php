<?php

set_page_title("Home");

// No db required for this page.
/*
try {
  $dbh = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}
*/

try {
	$user = User::load();
} catch (Exception $error) {
	require('login.php');
	return;
}

set_page_body(<<<EOT
Home page for the game.
EOT
);














