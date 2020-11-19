<?php

set_page_title("Gym");
$training_result = '';

try {
  $dbh = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}


try {
	$user = User::load();
} catch (Exception $error) {
	set_error_message(<<<EOT
You're not logged in. Please login in here: <a href="index.php?p=login">Login</a>
EOT
	);
	return; // Exits the included script and allows the rest of index.php to execute.
}



$train_type = $_REQUEST['train_type'] ?? null;

// if (!is_null($train_type) || !in_array($train_type, ['strength', 'defense', 'agility' /*, .... */]) ) {

if (!is_null($train_type)) {
  if ($train_type == 'strength') {
	  // update user set energy = energy - 1 where userid = ?
	  // check and see that the energy was actually taken.
	  // If not, tell user
	  if ($error) {
		  $training_result = 'sorry, but you are out of energy'; // return;
	  } else {
		// train strength, by executing sql (update user_stat set strength = strength + 1 where userid = ? )
		  // select...
		 $training_result = 'you now have strength: ?'; // return;
	  }
	  
  }
      
      // handle agility training
}

// Whether someone is training or not, reshow the gym training form (which I haven't added to the set_page_body() function call).

set_page_body(<<<EOT
<form method="post" action="index.php?p=gym">
<input type="button" name="train_type" value="strength" Train strength<br>
<input type="button" name="train_type" value="agility" Train agility<br>
Train defense<br>
$training_result
EOT
);














