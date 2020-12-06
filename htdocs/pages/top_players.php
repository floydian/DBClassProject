<?php
set_page_title("Top Players");

try {
  $conn = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}


$user_list = '';
$stmt =  $conn->stmt_init();
if ($stmt->prepare('select display_name, userid from top_players;')) {
	if ($stmt->execute()) {
		$result = $stmt->get_result();
		$count = 0;
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$count++;
			$user_list .= <<<EOT
<tr>
	<td>
	{$row['display_name']}
	</td>
	<td style="text-align: right">
	$count
	</td>
</tr>

EOT;
		}
	}
}
$stmt->close();


set_page_body(<<<EOT
<h3>Top 10 Players</h3>
<p><b>This page displays the use of a MySQL View, which in turn uses a join</b></p>
<hr>
<table>
	<tr>
		<th>Nickname</th>
		<th>Rank</th>
	</tr>
$user_list
</table>
EOT
);






