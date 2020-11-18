<?php

$error = false;

// suitable when selecting one row from the database.
$stmt =  $conn->stmt_init();
if ( !$stmt->prepare('some select query') ) {
	$error = true;
} else if ( !$stmt->bind_param('ss', $param_1, $param_2) ) {
	$error = true;
} else if (!$stmt->execute()) {
	$error = true;
} else if ( !($result = $stmt->get_result()) ) {
	$error = true;
} else if ( !($data = $result->fetch_array(MYSQLI_ASSOC)) ) {
	$error = true;
} else {
	/*
		Success.
		Do some successful things here.
	*/
}
$stmt->close();




// This next example is suitable for when there are multiple rows returned
// from the select statement.

$error = false;

// suitable when selecting one row from the database.
$stmt =  $conn->stmt_init();
if ( !$stmt->prepare('some select query') ) {
	$error = true;
} else if ( !$stmt->bind_param('ss', $param_1, $param_2) ) {
	$error = true;
} else if (!$stmt->execute()) {
	$error = true;
} else if ( !($result = $stmt->get_result()) ) {
	$error = true;
} else {
	
	// Be careful. If your query returned an empty result set and if you need to know that,
	// then use a counter in the while loop.
	$num_results = 0;
	while($data = $result->fetch_array(MYSQLI_ASSOC)) {
		$num_results++;
		// save or process data
	}
	
	if ($num_results > 0) {
		/*
			Success.
			Do some successful things here.
		*/
	}
}
$stmt->close();












