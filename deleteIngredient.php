<?php
session_start();
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ingredientID'])) {
    $ingredientID = $_GET['ingredientID'];

    // Check if the ingredient exists
    $checkIngredientStmt = $dbconnect->prepare("SELECT * FROM ingredient WHERE ingredientID = ?");
    $checkIngredientStmt->bind_param("i", $ingredientID);
    $checkIngredientStmt->execute();
    $result = $checkIngredientStmt->get_result();

    if ($result->num_rows > 0) {
        // Ingredient exists, proceed with deletion

        // Remove nutritional facts for the ingredient
        $deleteNutritionalFactsStmt = $dbconnect->prepare("DELETE FROM nutritionalFacts WHERE ingredientID = ?");
        $deleteNutritionalFactsStmt->bind_param("i", $ingredientID);
        $deleteNutritionalFactsStmt->execute();

        // Remove the ingredient from the recipeIngredient table
        $deleteRecipeIngredientStmt = $dbconnect->prepare("DELETE FROM recipeIngredient WHERE ingredientID = ?");
        $deleteRecipeIngredientStmt->bind_param("i", $ingredientID);
        $deleteRecipeIngredientStmt->execute();

        // Remove the ingredient
        $deleteIngredientStmt = $dbconnect->prepare("DELETE FROM ingredient WHERE ingredientID = ?");
        $deleteIngredientStmt->bind_param("i", $ingredientID);
        $deleteIngredientStmt->execute();

        $_SESSION['ingredient_removed'] = true;

        // Redirect back to the ingredients page with a parameter in the URL
        header('Location: ingredients.php?ingredient_removed=true');
        exit();
    } else {
        echo '<script>alert("Ingredient not found!");</script>';
    }
} else {
    // Invalid request
    echo '<script>alert("Invalid request!");</script>';
}
?>
