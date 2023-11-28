<?php
session_start();
require 'connection.php';

if (isset($_POST['requestID'])) {
    $requestID = mysqli_real_escape_string($dbconnect, $_POST['requestID']);

    // Delete the request from the database
    $deleteStmt = $dbconnect->prepare("DELETE FROM request WHERE requestID = ?");
    $deleteStmt->bind_param("i", $requestID);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Echo JavaScript for displaying a confirmation message
    echo '<script>alert("Request deleted successfully!");</script>';
} else {
    // Echo JavaScript for displaying an error message
    echo '<script>alert("Error deleting request.");</script>';
}


echo '<script>window.location.href = "adminProfile.php";</script>';
?>