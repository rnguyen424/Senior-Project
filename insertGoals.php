<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
    echo ("<script> alert('You must be logged in first.')</script>");
    echo ("<script>window.history.go(-1);</script>");
}
else{if (isset($_POST['submit'])) {
    $goal = $_POST['goal'];
    $weeklyGoal = $_POST['weeklyGoal'];
    $goalDate = $_POST['goalDate'];
    $user = $_SESSION['username']; 
    
    $stmt = $dbconnect->prepare("SELECT userID FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $ID = $stmt->get_result()->fetch_assoc();
    $userID = $ID['userID'];


    $stmt = $dbconnect->prepare("INSERT INTO goal (goal, weeklyGoal, goalDate, userID) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ddsi', $goal, $weeklyGoal, $goalDate, $userID);

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
