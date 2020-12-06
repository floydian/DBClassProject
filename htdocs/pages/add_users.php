<?php
set_time_limit(60 * 5);
set_page_title("Add Test Users");

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


// userid, display_name, login_name, password, join_date, last_login, premium_status, email


$message = '';
$can_create_users = false;
$stmt =  $conn->stmt_init();
if ($stmt->prepare("select count(*) from user where login_name = 'Test.Login.1'") && $stmt->execute()) {
	$result = $stmt->get_result();
	$row = $result->fetch_array(MYSQLI_NUM);
	if ($row[0] == 0) {
		$can_create_users = true;
	}
	$message = 'The users have already been added.<br>';
}
$stmt->close();

if ($can_create_users) {
	$data = [];
	$joindate = new DateTime(sprintf('2020-01-01 %d:%02dpm', random_int(1,10), random_int(0,59)), new DateTimeZone('America/New_York'));
	$lastlogin = new DateTime(sprintf('2020-11-%02d %d:%02dpm', random_int(1,23), random_int(1,10), random_int(0,59)), new DateTimeZone('America/New_York'));
	for ($i = 1; $i <= 100; $i++) {
		$data[] = [
			'display_name' => 'Test.User.' . $i,
			'login_name' => 'Test.Login.' . $i,
			'password' => User::passHash('Test.Pass.' . $i),
			'join_date' => $joindate->format('Y-m-d H:i:s'),
			'last_login' => $lastlogin->format('Y-m-d H:i:s'),
			'email' => sprintf('Test.Email.%d@foo.com', $i),
		];
		$joindate->modify('+1day');
		$joindate->modify(sprintf('%d:%02dpm', random_int(1,10), random_int(0,59)));
		$lastlogin->modify(sprintf('2020-11-%02d %d:%02dp', random_int(1,23), random_int(1,10), random_int(0,59)));
	}


	$stmt =  $conn->stmt_init();
	$userids = [];
	if ($stmt->prepare('insert into user (login_name, display_name, password, email, join_date, last_login) values (?, ?, ?, ?, ?, ?)')) {
		foreach ($data as $key => $datum) {
			if ($stmt->bind_param('ssssss', $datum['login_name'], $datum['display_name'],
			$datum['password'], $datum['email'], $datum['join_date'], $datum['last_login'])) {
				if (!$stmt->execute()) {
					//print_r($stmt->error_list);
				} else {
					$userids[$key] = $conn->insert_id;
				}
			} else {
				echo "outer bind_param error";
			}
		}
		
		$stmt->close();
		$stmt =  $conn->stmt_init();
		if ($stmt->prepare('insert into user_stat (userid, strength, defense, speed, dexterity) values (?, 1,1,1,1)')) {
			foreach ($data as $key => $datum) {
				if ($stmt->bind_param('s', $userids[$key])) {
					if (!$stmt->execute()) {
						//print_r($stmt->error_list);
					} else {
						// userid, strength, defense, speed, dexterity
						if (!$stmt->execute()) {
							//print_r($stmt->error_list);
						}
					}
				} else {
					echo "inner bind_param error";
				}
			}
		} else {
			echo "inner prepared statement error";
		}
	} else {
		echo "outer prepared statement error";
	}
	$stmt->close();
	$message = '100 users added to the game.<br>';
}


set_page_body(<<<EOT
<p><b>This page displays the use of a Mysql insert (creating 100 uesrs)</b></p>
$message
Login names: Test.Login.##<br>
Password: Test.Pass.##<br>
Where ## is 1 through 100. For example, the first user is Test.Login.1 and Test.Pass.1<br>
EOT
);






