<?php
session_start();
include 'connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $type = $_POST['type'];
    $ingredientName = $_POST['ingredient'];
    $portion = $_POST['measure'];
    $proteins = $_POST['proteins'];
    $fats = $_POST['fats'];
    $carbs = $_POST['carbs'];

    // Check if the ingredientID is set in the URL
    if (isset($_POST['ingredientID'])) {
        $ingredientID = $_POST['ingredientID'];

        // Update 'ingredient' table
        $stmtUpdateIngredient = $dbconnect->prepare("UPDATE ingredient SET type = ?, ingredient = ? WHERE ingredientID = ?");
        $stmtUpdateIngredient->bind_param("ssi", $type, $ingredientName, $ingredientID);
        $stmtUpdateIngredient->execute();

        // Update 'nutritionalFacts' table
        $stmtUpdateNutritionalFacts = $dbconnect->prepare("UPDATE nutritionalFacts SET measure = ?, proteins = ?, fats = ?, carbs = ? WHERE ingredientID = ?");
        $stmtUpdateNutritionalFacts->bind_param("dddsi", $portion, $proteins, $fats, $carbs, $ingredientID);
        $stmtUpdateNutritionalFacts->execute();

        // Check if both updates were successful
        if ($stmtUpdateIngredient->affected_rows > 0 || $stmtUpdateNutritionalFacts->affected_rows > 0) {
            // Set session variable for success message
            $_SESSION['ingredient_updated'] = true;

            // Redirect to ingredients.php
            header("Location: ingredients.php");
            exit();
        } else {
            echo "Error updating ingredient.";
        }

        // Close statements
        $stmtUpdateIngredient->close();
        $stmtUpdateNutritionalFacts->close();
    } else {
        echo "ingredientID not set in the URL.";
    }
}
?>
