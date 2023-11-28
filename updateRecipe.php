<?php
session_start();
include 'connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    $recipeID = $_POST['recipeID']; // Add the hidden input field for recipeID in your HTML form

    // Delete existing ingredients and instructions for the recipe
    if (!empty($_POST['selectedIngredients'])) {
        $stmt_delete_recipeIngredient = $dbconnect->prepare("DELETE FROM recipeIngredient WHERE recipeID = ?");
        $stmt_delete_recipeIngredient->bind_param("i", $recipeID);
        $stmt_delete_recipeIngredient->execute();
    }
    
    // Check if instructions array is not null or not empty
    if (isset($_POST['instructions']) && is_array($_POST['instructions']) && !empty($_POST['instructions']) && $_POST['instructions'][0] != '') {
        $stmt_delete_instruction = $dbconnect->prepare("DELETE FROM instruction WHERE recipeID = ?");
        $stmt_delete_instruction->bind_param("i", $recipeID);
        $stmt_delete_instruction->execute();
    }

    // Update recipe details
    $title = $_POST['title'];
    $description = $_POST['description'];
    $time = $_POST['time'];
    $servings = $_POST['servings'];

    $goalID = isset($_POST['goalType']) ? $_POST['goalType'] : 1;
    $dietID = isset($_POST['dietType']) ? $_POST['dietType'] : 1;

    $sql_update_recipe = "UPDATE recipe SET title = ?, description = ?, time = ?, servings = ?, goalID = ?, dietID = ? WHERE recipeID = ?";
    $stmt_update_recipe = $dbconnect->prepare($sql_update_recipe);
    $stmt_update_recipe->bind_param("ssiiiii", $title, $description, $time, $servings, $goalID, $dietID, $recipeID);

    if ($stmt_update_recipe->execute()) {
        // Process instructions array
        $instructionsArray = isset($_POST['instructions']) ? $_POST['instructions'] : [];
        if (!empty($instructionsArray)) {
            foreach ($instructionsArray as $step) {
                if ($step != '') {
                    $sql_insert_instruction = "INSERT INTO instruction (step, recipeID) VALUES (?, ?)";
                    $stmt_insert_instruction = $dbconnect->prepare($sql_insert_instruction);
                    $stmt_insert_instruction->bind_param("si", $step, $recipeID);
                    $stmt_insert_instruction->execute();
                }
            }
        }
    
        // Process ingredients array
        $ingredients = isset($_POST['selectedIngredients']) ? [$_POST['selectedIngredients']] : [];
        $measurements = isset($_POST['measurements']) ? $_POST['measurements'] : [];
        if (!empty($ingredients)) {
            foreach ($ingredients as $ingredientList) {
                $ingredientArray = explode(';', $ingredientList);
    
                foreach ($ingredientArray as $index => $ingredientName) {
                    $ingredientName = trim($ingredientName);
    
                    $stmt_getIngredientID = $dbconnect->prepare("SELECT ingredientID FROM ingredient WHERE ingredient = ?");
                    $stmt_getIngredientID->bind_param("s", $ingredientName);
                    $stmt_getIngredientID->execute();
                    $ingredientIDResult = $stmt_getIngredientID->get_result()->fetch_assoc();
    
                    if ($ingredientIDResult) {
                        $ingredientID = $ingredientIDResult['ingredientID'];
    
                        $measurement = $measurements[$index] ?? '';
    
                        $sql_insert_recipeIngredient = "INSERT INTO recipeIngredient (recipeID, ingredientID, measurement) VALUES (?, ?, ?)";
                        $stmt_insert_recipeIngredient = $dbconnect->prepare($sql_insert_recipeIngredient);
                        $stmt_insert_recipeIngredient->bind_param("iii", $recipeID, $ingredientID, $measurement);
                        $stmt_insert_recipeIngredient->execute();
                    } else {
                        echo "Error: Ingredient '$ingredientName' not found in the database.";
                        // Handle the error as needed
                    }
                }
            }
        }

    

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];

            $uploadDir = '/home/rnguyen2/public_html/CS487/uploads/';
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $sql_update_image = "UPDATE recipe SET image = ? WHERE recipeID = ?";
                $stmt_update_image = $dbconnect->prepare($sql_update_image);
                $stmt_update_image->bind_param("si", $destPath, $recipeID);
                $stmt_update_image->execute();
            } else {
                echo "Error moving uploaded file.";
            }
        }

        $_SESSION['success_message'] = "Recipe updated successfully!";
        echo '<script type="text/javascript">';
        echo 'alert("' . $_SESSION['success_message'] . '");';
        echo 'window.location.href = "homepage.php";';
        echo '</script>';
        exit();
    } else {
        echo "Recipe Update Error: " . $stmt_update_recipe->error;
    }
} else {
    echo "Invalid request";
}
?>
