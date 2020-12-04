<?php

set_page_title("Gym");
$training_result = '';
error_reporting(32767);

$output = '';

try {
  $conn = db_connect();
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

if (!isset($_SESSION['energy'])){
	$_SESSION['energy'] = 3;
}

if ($_SESSION['energy'] == 0) {
	$_SESSION['energy'] = 3;
}


$training = $_REQUEST['training'] ?? null;


$rnd1 = random_int(0,3);
$rnd2 = random_int(0,3);
$rnd3 = random_int(0,3);
$rnd4 = random_int(0,3);

 
if (!is_null($training)){
	$_SESSION['energy'] = $_SESSION['energy'] - 1;
	$output = "Training was a success! <br>
			Strength +{$rnd1}<br>
			Defense +{$rnd2}<br>
			Speed +{$rnd3}<br>
			Dexterity+{$rnd4}<br> <br>";

      $user->strength = $user->strength + $rnd1;
      $user->defense = $user->defense + $rnd2;
	$user->speed = $user->speed + $rnd3;
	$user->dexterity = $user->dexterity + $rnd4;
	
	$stmt = $conn->stmt_init();

if ($stmt->prepare('update user_stat set strength = ?, defense = ?, speed = ?, dexterity = ? where userid = ?') && 
       $stmt->bind_param('iiiis', $user->strength, $user->defense, $user->speed, $user->dexterity, $user->userid) && $stmt->execute()) {} else { }  

     $stmt->close();
}


set_page_body(<<<EOT
<form method="post" action="index.php?p=gym">
{$output}
Stats:<br>
Strength: {$user->strength} <br>
Defense: {$user->defense} <br>
Speed: {$user->speed} <br>
Dexterity: {$user->dexterity} <br> <br>
Energy: {$_SESSION['energy']}/ 3 <br>
<input type="submit" name="training" value="training"> <br>
</form>
EOT
);














