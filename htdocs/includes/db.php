<?php
  
/*
  The db class file
  Database user: db_project_user
  Database pass: db_project_pass
*/
  
$username = $_POST["user"];
$password = $_POST["pass"];

function db_connect() {
$conn = mysqli_connect("localhost", "root", "","db_Class_project");
if (!$conn) {
   throw new Exception("Failed to Connect: " . mysqli_connect_error());
	     }
return $conn;
}


try{
	$conn = db_connect();
    } catch (Exception $e) {
	echo 'Caught Exception: ', $e->getMessage(), "\n";
	}	

?>
