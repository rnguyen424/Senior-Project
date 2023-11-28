<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $type = $_POST['type'];
    $ingredientName = $_POST['ingredient'];
    $portion = $_POST['measure'];
    $proteins = $_POST['proteins'];
    $fats = $_POST['fats'];
    $carbs = $_POST['carbs'];

    if ($portion < 0 || $proteins < 0 || $fats < 0 || $carbs < 0) {
        echo "Error: Nutritional facts cannot be negative.";
        exit();
    }

    // Insert into 'ingredient' table
    $stmtIngredient = $dbconnect->prepare("INSERT INTO ingredient (type, ingredient) VALUES (?, ?)");
    $stmtIngredient->bind_param("ss", $type, $ingredientName);
    $stmtIngredient->execute();

    // Get the generated ingredient ID
    $ingredientID = $stmtIngredient->insert_id;

    // Insert into 'nutritionalFacts' table
    $stmtNutritionalFacts = $dbconnect->prepare("INSERT INTO nutritionalFacts (measure, proteins, fats, carbs, ingredientID) VALUES (?, ?, ?, ?, ?)");
    $stmtNutritionalFacts->bind_param("dddsi", $portion, $proteins, $fats, $carbs, $ingredientID);
    $stmtNutritionalFacts->execute();

    // Check if both inserts were successful
    if ($stmtIngredient->affected_rows > 0 && $stmtNutritionalFacts->affected_rows > 0) {
        // Redirect to adminProfile.php with success message
        header("Location: adminProfile.php?success=1");
        exit();
    } else {
        echo "Error adding ingredient.";
    }

    // Close statements
    $stmtIngredient->close();
    $stmtNutritionalFacts->close();
}
?>
