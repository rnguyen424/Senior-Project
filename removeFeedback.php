<?php
session_start();
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['feedbackID'])) {
        $feedbackID = $_POST['feedbackID'];

        $removeFeedbackStmt = $dbconnect->prepare("DELETE FROM feedback WHERE feedbackID = ?");
        $removeFeedbackStmt->bind_param("i", $feedbackID);
        $removeFeedbackStmt->execute();

        if ($removeFeedbackStmt->execute()) {
            // Set success message in session
            $_SESSION['feedback_removed'] = "Feedback removed successfully";
        } else {
            // Set error message in session if deletion fails
            $_SESSION['feedback_removed'] = "Error removing feedback: " . mysqli_error($dbconnect);
        }

        echo '<script>window.location.href = "userprofile.php?feedback_removed=true";</script>';
        exit();
    } else {
        echo "Feedback ID not provided.";
    }
} 
?>
