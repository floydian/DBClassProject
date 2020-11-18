<?php
/*
  Created by Wayne Fincher
*/

/*
	Bringing in the database.
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
// url.com/?name=foo&blah=1



$main_content = [
	'page_title' => '',
	'page_body' => '',
	'error_message' => '',
	'some_other_content' => '',
	'defense' => '',
	'strength' => '',
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


// SUPER IMPORTANT
// index.php?p=new_page_name

// All pages on the site are loaded from here. index.php?p=login or index.php?p=new_page
switch ($p) {
		
	case 'new_page_name': require_once('../pages/new_page_name.php'); break;
		
	case 'login': require_once('../pages/login.php'); break;
		
	
	case 'gym': require_once('../pages/gym.php'); break; // return;
	
	// Check user's logged in status. If not logged in, gracefully fail.
	case 'new_page': /* require_once('./pages/new_page.php') */ break;
	
}

echo <<<EOT
<!DOCTYPE html>
<html>
<head>
  <title>Game Title</title>
</head>
<body>

<h1>{$main_content['page_title']}</h1>

<!-- left panel which has user stat data in it -->
Strength: {$main_content['strength']}
Defense: {$main_content['defense']}


<main>
{$main_content['page_body']}
</main>

<div class="error_message">
{$main_content['error_message']}
</div>
</body>
</html>
EOT;




























