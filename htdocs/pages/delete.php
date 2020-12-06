<?php
set_page_title("Top Players");

try {
  $conn = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}

try {
	$user = User::load();
} catch (Exception $error) {
	$message = 'You are not logged in.';
	require('login.php');
	return;
}

$delete_account = $_REQUEST['delete_account'] ?? null;
$message = '';
if (!is_null($delete_account)) {

	try {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$conn->begin_transaction();
		$stmt =  $conn->stmt_init();
		$stmt->prepare('delete from user_stat where userid = ?');
		$stmt->bind_param('s', $user->userid);
		$stmt->execute();
		$stmt->close();
		
		$stmt =  $conn->stmt_init();
		$stmt->prepare('delete from user where userid = ?');
		$stmt->bind_param('s', $user->userid);
		$stmt->execute();
		$message = 'Your Account has been deleted.';
		$stmt->close();
		$conn->commit();
		$user->doNotSave();
		$_SESSION['user'] = null;
		require('login.php');
		return;
		
	} catch (mysqli_sql_exception $error) {
		$conn->rollback();
		echo $error->getMessage();
	}
}


set_page_body(<<<EOT
<h3>Delete My Account</h3>
<p><b>This page displays the use of MySQL transactions and delete statements.</b></p>
<hr>
<form method="post" action="index.php?p=delete">
Are you sure you want to delete your account?
<input type="submit" name="delete_account" value="Yes"> <a href="index.php">No</a>
</form>
EOT
);






