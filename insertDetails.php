<?php
session_start();
include 'connection.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
    echo ("<script> alert('You must be logged in first.')</script>");
    echo ("<script>window.history.go(-1);</script>");
}

else{
    if (isset($_POST['submit'])) {
        $dateOfBirth = $_POST['dateOfBirth'];
        $weight = $_POST['weight'];
        $height = $_POST['height'];
        $user = $_SESSION['username']; 
    
        $stmt = $dbconnect->prepare("SELECT userID FROM user WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $ID = $stmt->get_result()->fetch_assoc();
        $userID = $ID['userID'];
    
        $stmt = $dbconnect->prepare("INSERT INTO userDetails (dateOfBirth, weight, height, userID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sddi', $dateOfBirth, $weight, $height, $userID);
    
        if ($stmt->execute()) {
            header("Location: userprofile.php"); 
        } else {
            die('An error occurred. Your post was not created.');
        }
    } else {
        echo "Invalid request.";
    }
}
    ?>
    

