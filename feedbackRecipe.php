<?php
session_start();
require 'connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipeID = $_POST['recipeID'];

    // Check if the user has already given feedback
    // Replace with the actual variable storing the user ID
    $user = $_SESSION['username'];
    $stmt = $dbconnect->prepare("SELECT userID FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $ID = $stmt->get_result()->fetch_assoc();
    $userID = $ID['userID'];
    
    $checkFeedbackQuery = "SELECT * FROM feedback WHERE userID = $userID AND recipeID = $recipeID";
    $checkFeedbackResult = mysqli_query($dbconnect, $checkFeedbackQuery);
    
    if (mysqli_num_rows($checkFeedbackResult) > 0) {
        // User has already given feedback, show alert
        echo '<script>alert("You have already given feedback for this recipe.");</script>';
    } else {
        // User has not given feedback, process like or dislike
        if (isset($_POST['like'])) {
            $feedbackValue = 'like';
        } elseif (isset($_POST['dislike'])) {
            $feedbackValue = 'dislike';
        }

        // Insert into the feedback table to track user feedback
        $insertFeedbackQuery = "INSERT INTO feedback (userID, recipeID, feedback) VALUES ($userID, $recipeID, '$feedbackValue')";
        
        mysqli_query($dbconnect, $insertFeedbackQuery);
    }
}

mysqli_close($dbconnect);
header('Location: recipes.php'); // Redirect back to the homepage
exit();
?>
