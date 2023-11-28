<?php
session_start();
require 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the recipeID is set and is a valid integer
    if (isset($_POST['recipeID']) && is_numeric($_POST['recipeID'])) {
        $recipeID = $_POST['recipeID'];

        // Check if the recipe with the given ID exists in the database
        $stmt_check_recipe = $dbconnect->prepare("SELECT recipeID FROM recipe WHERE recipeID = ?");
        $stmt_check_recipe->bind_param("i", $recipeID);
        $stmt_check_recipe->execute();
        $result_check_recipe = $stmt_check_recipe->get_result();

        if ($result_check_recipe->num_rows > 0) {
            // Recipe exists, delete related recipeIngredient records first
            $stmt_delete_recipeIngredient = $dbconnect->prepare("DELETE FROM recipeIngredient WHERE recipeID = ?");
            $stmt_delete_recipeIngredient->bind_param("i", $recipeID);
            $stmt_delete_recipeIngredient->execute();

            // Delete related instruction records
            $stmt_delete_instruction = $dbconnect->prepare("DELETE FROM instruction WHERE recipeID = ?");
            $stmt_delete_instruction->bind_param("i", $recipeID);
            $stmt_delete_instruction->execute();

            // Delete related feedback records
            $stmt_delete_feedback = $dbconnect->prepare("DELETE FROM feedback WHERE recipeID = ?");
            $stmt_delete_feedback->bind_param("i", $recipeID);
            $stmt_delete_feedback->execute();

            // Then, delete the recipe
            $stmt_delete_recipe = $dbconnect->prepare("DELETE FROM recipe WHERE recipeID = ?");
            $stmt_delete_recipe->bind_param("i", $recipeID);
            $stmt_delete_recipe->execute();

            // Redirect back to homepage after deletion
            header("Location: userprofile.php?recipe_deleted=true");
            exit();
        } else {
            // Recipe not found
            echo "Recipe not found.";
        }
    } else {
        // Invalid recipeID
        echo "Invalid recipe ID.";
    }
}
?>
