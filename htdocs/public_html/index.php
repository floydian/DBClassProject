<?php
/*
  Created by Wayne Fincher
*/

/*
	Establishes connection to the database.
*/
require_once('../includes/db.php');

/*
	Provides access to the user databse table.
	This included script should not check logged in status because we may be logging in below.
	However, this include should provide functions/methods for checking login status, and logging in/out the user when needed.
	These functions/methods would be called on any give page as needed, which is included below.
*/
require_once('../includes/user.php');

$p = $_REQUEST['p'] ?? null;


$main_content = [
	'page_title' => '',
	'page_body' => '',
	'error_message' => '',
	'some_other_content' => '',
];


function set_page_title($html) {
	global $main_content;
	$main_content['page_title'] = $html;
}
function set_page_body($html) {
	global $main_content;
	$main_content['page_body'] = $html;
}
function set_error_message($html) {
	global $main_content;
	$main_content['error_message'] = $html;
}

/*
// Add these functions as needed to our main template page. 
// Then add the $main_content['...'] variable to the output below.
function set_some_other_content($html) {
	global $main_content;
	$main_content['some_other_content'] = $html;
}
*/

// All pages on the site are loaded from here. index.php?p=login or index.php?p=new_page
switch ($p) {
	
	case 'login': require_once('../pages/login.php'); break;
	
	// Check user's logged in status. If not logged in, gracefully fail.
	case 'new_page': /* require_once('./pages/new_page.php') */ break;;
	
}

echo <<<EOT
<!DOCTYPE html>
<html>
<head>
  <title>Game Title</title>
</head>
<body>

<h1>{$main_content['page_title']}</h1>

<main>
{$main_content['page_body']}
</main>

<div class="error_message">
{$main_content['error_message']}
</div>
</body>
</html>
EOT;




























