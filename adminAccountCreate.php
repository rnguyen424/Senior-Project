<?php
session_start();
include 'connection.php';
if (isset($_POST['submit'])) { 
  $admincode = $_POST['verificationcode'];
  
  // Check if the admin verification code is present in the adminVerify table
  $check_query = "SELECT * FROM adminVerify WHERE adminCode = '$admincode'";
  $check_result = mysqli_query($dbconnect, $check_query);
  $check_count = mysqli_num_rows($check_result);
  
  if ($check_count > 0) {
    // Admin verification code is correct, proceed with account creation
    $accounttype = 'Admin';
    $firstname = $_POST['firstName'];
    $lastname = $_POST['lastName'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $uName = "SELECT * From user WHERE username = '$username'";
    $eMail = "SELECT * From user WHERE email = '$email'";

    $res_u = mysqli_query($dbconnect, $uName);
    $res_e = mysqli_query($dbconnect, $eMail);

    if (mysqli_num_rows($res_u) > 0) {
      echo "<script> alert('Username is taken')</script>";
      echo ("<script>window.history.go(-1);</script>");
    }
    elseif (mysqli_num_rows($res_e) > 0) {
      echo "<script> alert('Email is taken')</script>";
      echo ("<script>window.history.go(-1);</script>");
    }
    
    else {
      $stmt = $dbconnect->prepare("INSERT INTO user (accountType, firstName, lastName,  username, email, password, phoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param('ssssssi', $accounttype, $firstname, $lastname, $username, $email, $hash, $number);
      if (!$stmt->execute()) {
        die('An error occurred. Your data was not posted.');
      } 
      else {
        echo "<script>window.location.href='index.php';</script>";
      }
    }
  }
  else {
    // Admin verification code is incorrect, show error message and redirect to adminRegister.php
    echo "<script> alert('Incorrect Admin Verification Code')</script>";
    echo ("<script>window.location.href='adminRegister.php';</script>");
  }
}
else {
  echo "Failed to post data";
}
?>