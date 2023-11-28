<?php
session_start();
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
    $userID = $_POST['userID'];

    // Check if the user exists
    $checkUserStmt = $dbconnect->prepare("SELECT * FROM user WHERE userID = ?");
    $checkUserStmt->bind_param("i", $userID);
    $checkUserStmt->execute();
    $result = $checkUserStmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, proceed with deletion

        // Remove user from userGoals table
        $deleteUserGoalsStmt = $dbconnect->prepare("DELETE FROM userGoals WHERE userID = ?");
        $deleteUserGoalsStmt->bind_param("i", $userID);
        $deleteUserGoalsStmt->execute();

        // Remove user's feedback
        $deleteFeedbackStmt = $dbconnect->prepare("DELETE FROM feedback WHERE userID = ?");
        $deleteFeedbackStmt->bind_param("i", $userID);
        $deleteFeedbackStmt->execute();

        // Remove user's recipes
        $deleteRecipesStmt = $dbconnect->prepare("DELETE FROM recipe WHERE userID = ?");
        $deleteRecipesStmt->bind_param("i", $userID);
        $deleteRecipesStmt->execute();

        // Remove user from allergenAndpreference table
        $deleteUserAllergensStmt = $dbconnect->prepare("DELETE FROM allergenAndpreference WHERE userID = ?");
        $deleteUserAllergensStmt->bind_param("i", $userID);
        $deleteUserAllergensStmt->execute();

        // Remove the user
        $deleteUserStmt = $dbconnect->prepare("DELETE FROM user WHERE userID = ?");
        $deleteUserStmt->bind_param("i", $userID);
        $deleteUserStmt->execute();

        $_SESSION['user_removed'] = true;

        // Redirect back to the users page with a parameter in the URL
        header('Location: adminProfile.php?user_removed=true#v-pills-users');
        exit();
    } else {
        echo '<script>alert("User not found!");</script>';
    }
} else {
    // Invalid request
    echo '<script>alert("Invalid request!");</script>';
}
?>
