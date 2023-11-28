<?php
session_start();
require 'connection.php';

if (isset($_POST['submit'])) {
    $admincode = $_POST['admincode'];
    $query = "INSERT INTO adminVerify (adminCode) VALUES ('$admincode')";
    
    if (!mysqli_query($dbconnect, $query)) {
        die('An error occurred. Your data was not posted.');
    } else {
        echo "<script> alert('Admin Verification Code Created Successfully')</script>";
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Create Admin Code</title>
    <link rel="stylesheet" type="text/css" href="register.css">
  </head>
  <body>
    <header>
      <h1>Create Admin Code</h1>
    </header>
    <div class="container">
      <form action="createAdminCode.php" method="post">
        <label for="admincode"><b>Admin Verification Code</b></label>
        <input type="text" name="admincode" placeholder="Enter Admin Verification Code" id="admincode" required> <!-- Modified name attribute -->
        
        <input type="submit" name="submit" value="Create Code">
      </form>
    </div>
  </body>
</html>
