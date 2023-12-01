<?php

require 'connection.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];
//login function
$stmt = $dbconnect->prepare("SELECT password, accountType FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($result->num_rows === 1 && password_verify($password, $user['password'])) {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;

    if ($user['accountType'] == 'Admin') {
        header("Location: homepage.php");
    } else {
        header("Location: homepage.php");
    }
    exit();
} else {
    echo "<script>alert('Invalid username or password.');</script>";
    header("refresh:0; url=index.php");
}
?>
