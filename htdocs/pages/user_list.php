<?php
set_page_title("Player List");

try {
  $conn = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}

// Player does not need to be logged in.
/*
try {
	$user = User::load();
} catch (Exception $error) {
	$message = 'You are not logged in.';
	require('login.php');
	return;
}
*/

$num_users = 0;
$stmt =  $conn->stmt_init();
if ($stmt->prepare("select count(*) from user") && $stmt->execute()) {
	$result = $stmt->get_result();
	$row = $result->fetch_array(MYSQLI_NUM);
	$num_users = $row[0];
}
$stmt->close();


$rpp = (int) ($_REQUEST['rpp'] ?? 10);
if ($rpp < 5) {
	$rpp = 5;
} else if ($rpp > 50) {
	$rpp = 50;
}
$num_pages = (int) ceil($num_users / $rpp);


$page_num = (int) ($_REQUEST['page_num'] ?? 1);
if ($page_num < 1) {
	$page_num = 1;
} else if ($page_num > $num_pages) {
	$page_num = $num_pages;
}


$offset = $rpp * ($page_num - 1);



// userid, display_name, login_name, password, join_date, last_login, premium_status, email

// format('Y-m-d H:i:s')

$user_list = '';
$stmt =  $conn->stmt_init();
if ($stmt->prepare("call userList(?, ?)")) {
	if ($stmt->bind_param('ss', $offset, $rpp)) {
		if ($stmt->execute()) {
			$result = $stmt->get_result();
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$user_list .= <<<EOT
<tr>
	<td>
	{$row['display_name']}
	</td>
	<td>
	{$row['last_login']}
	</td>
	<td>
	{$row['join_date']}
	</td>
</tr>

EOT;
			}
		}
	}
}
$stmt->close();

$page_range = range(max($page_num - 4, 1), min($page_num + 4, $num_pages));
$page_range_links = '';
foreach ($page_range as $value) {
	if ($value == $page_num) {
		$page_range_links .= sprintf(' <b>%d</b>', $value);
	} else {
		$page_range_links .= sprintf(' <a href="index.php?p=user_list&rpp=%1$d&page_num=%2$d">%2$d</a>', $rpp, $value);
	}
}


$next_page = 'Next';
if ($page_num < $num_pages) {
	$next_page = sprintf('<a href="index.php?p=user_list&rpp=%d&page_num=%d">Next</a>', $rpp, $page_num + 1);
}
$previous_page = 'Back';
if ($page_num > 1) {
	$previous_page = sprintf('<a href="index.php?p=user_list&rpp=%d&page_num=%d">Back</a>', $rpp, $page_num - 1);
}


set_page_body(<<<EOT
<h3>Player List</h3>
<p><b>This page displays the use of a MySQL Prepared Statement</b></p>
<form method="GET" action="index.php">
	Display <input type="text" name="rpp" value="$rpp" size="4"> players |
	Page <input type="text" name="page_num" value="$page_num" size="4">
	<input type="submit" value="Go">
	$previous_page $page_range_links $next_page
	<input type="hidden" name="p" value="user_list">
</form>
<hr>
<table>
	<tr>
		<th>Nickname</th>
		<th>Last Login</th>
		<th>Join Date</th>
	</tr>
$user_list
</table>
EOT
);






