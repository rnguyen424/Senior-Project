<?php
session_start();
include 'connection.php';

if (isset($_POST['submit'])) { 
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordrpt = $_POST['passwordrpt'];

    // Check if passwords match
    if ($password !== $passwordrpt) {
        echo "<script> alert('Passwords do not match')</script>";
        echo ("<script>window.history.go(-1);</script>");
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $stmt_u = $dbconnect->prepare("SELECT * FROM user WHERE username = ?");
        $stmt_u->bind_param('s', $username);
        $stmt_u->execute();
        $result_u = $stmt_u->get_result();

        $stmt_e = $dbconnect->prepare("SELECT * FROM user WHERE email = ?");
        $stmt_e->bind_param('s', $email);
        $stmt_e->execute();
        $result_e = $stmt_e->get_result();

        if ($result_u->num_rows > 0) {
            echo "<script> alert('Username is taken')</script>";
            echo ("<script>window.history.go(-1);</script>");
        } elseif ($result_e->num_rows > 0) {
            echo "<script> alert('Email is taken')</script>";
            echo ("<script>window.history.go(-1);</script>");
        } else {
            // Insert new user with account type set to "User"
            $accountType = "User";
            $stmt_insert = $dbconnect->prepare("INSERT INTO user (firstName, lastName, username, email, password, phoneNumber, accountType) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param('sssssis', $firstName, $lastName, $username, $email, $hash, $phoneNumber, $accountType);
            
            if ($stmt_insert->execute()) {
                echo "<script>window.location.href='index.php';</script>"; 
            } else {
                die('An error occurred. Your data was not posted.');
            }
        }
    }
} else {
    echo "Failed to post data";
}
?>
