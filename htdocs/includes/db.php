<?php
  
/*
  The db class file
  Database user: db_project_user
  Database pass: db_project_pass
*/

function db_connect() {
$conn = mysqli_connect("localhost", "root", "","db_Class_project");
if (!$conn) {
   throw new Exception("Failed to Connect: " . mysqli_connect_error());
	     }
return $conn;
}

/*
try{
	$conn = db_connect();
	
	
	/*
		put your code
	*/
	
    } catch (Exception $e) {
	echo 'Caught Exception: ', $e->getMessage(), "\n";
	}
*/	


// down here
