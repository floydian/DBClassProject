<?php
  
/*
  The db class file
  Database user: db_project_user
  Database pass: db_project_pass
*/

// This small class is intended to prevent a problem where a
// reference to the database connection is needed in an included script
// but the included script has no idea what the name of the variable
// containing the database reference is in the global namespace.
// Hence, we store $conn in DB::$conn which is accessible inside of any
// scope. You can still do $dbh = db_connect(); as before.
// But now, once db_connect() is called, DB::$conn will be available as well.
class DB {
	static public $conn = null;
}



function db_connect() {
	if (is_null(DB::$conn)) {
		DB::$conn = mysqli_connect("localhost", "db_project_user", "db_project_pass","db_class_project");
		if (!DB::$conn) {
			throw new Exception("Failed to Connect: " . mysqli_connect_error());
		}
	}
	return DB::$conn;
}



/*
try{
	$conn = db_connect();
	
	//	put your code
	
    } catch (Exception $e) {
	echo 'Caught Exception: ', $e->getMessage(), "\n";
	}
*/	
