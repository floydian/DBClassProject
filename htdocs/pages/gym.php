<?php

try {
  $dbh = db_connect();
} catch (Exception $error) {
  return; // die();
}

set_page_title("Gym");


$user = user_get();

if (!$user) {
	
	set_error_message(<<<EOT
You're not logged in. Please login in here: <a href="index.php?p=login">Login</a>
EOT
	);
	
	return; // Exits the included script and allows the rest of index.php to execute.
}


$train_type = $_REQUEST['train_type'] ?? null;

if (!is_null($train_type)) {
	// process the training.
}

// Whether someone is training or not, reshow the gym training form (which I haven't added to the set_page_body() function call).

set_page_body(<<<EOT
Train strength<br>
Train defense<br>
EOT
);

