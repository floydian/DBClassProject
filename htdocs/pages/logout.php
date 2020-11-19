<?php

/*
try {
  $dbh = db_connect();
} catch (Exception $error) {
  set_page_body("Sorry, but something went wrong. Please check back later.");
  return; 
}
*/

User::logout();
$message = 'You are now logged out.';
require('login.php');
return;
