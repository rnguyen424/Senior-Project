<?php
//connection to database
$hostname = "localhost";
$username = "rnguyen2";
$password = "olemi$$2023";
$db = "rnguyen2";

$dbconnect=mysqli_connect($hostname,$username,$password,$db);

if ($dbconnect->connect_error) {
  die("Database connection failed: " . $dbconnect->connect_error);
}
?>
