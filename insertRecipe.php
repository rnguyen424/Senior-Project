<?php
session_start();

include 'connection.php'; // Include your database connection file

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $time = $_POST['time'];
    $servings = $_POST['servings'];

    // Process instructions array
    $instructionsArray = isset($_POST['instructions']) ? $_POST['instructions'] : [];
    $instructions = implode("\n", $instructionsArray);

    // Get userID
    $user = $_SESSION['username'];
    $stmt = $dbconnect->prepare("SELECT userID FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $ID = $stmt->get_result()->fetch_assoc();
    $userID = $ID['userID'];


    $goalID = isset($_POST['goalType']) ? $_POST['goalType'] : 1;
    $dietID = isset($_POST['dietType']) ? $_POST['dietType'] : 1;

    // Insert data into 'recipe' table
    $sql_recipe = "INSERT INTO recipe (title, description, time, servings, goalID, dietID, userID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_recipe = $dbconnect->prepare($sql_recipe);
    $stmt_recipe->bind_param("ssiiiii", $title, $description, $time, $servings, $goalID, $dietID, $userID);

    if ($stmt_recipe->execute()) {
        $recipeID = $stmt_recipe->insert_id;

        // Insert data into 'instruction' table
        foreach ($instructionsArray as $step) {
            $sql_instruction = "INSERT INTO instruction (step, recipeID) VALUES (?, ?)";
            $stmt_instruction = $dbconnect->prepare($sql_instruction);
            $stmt_instruction->bind_param("si", $step, $recipeID);
            $stmt_instruction->execute();
        }

        $ingredients = isset($_POST['selectedIngredients']) ? [$_POST['selectedIngredients']] : [];
        $measurements = isset($_POST['measurements']) ? $_POST['measurements'] : [];
       
        // Insert data into 'recipeIngredient' table
        foreach ($ingredients as $ingredientList) {
            // Split the ingredient list into individual ingredients
            $ingredientArray = explode(';', $ingredientList);
        
            foreach ($ingredientArray as $index => $ingredientName) {
                // Trim whitespace from each ingredient
                $ingredientName = trim($ingredientName);
        
                // Get the ingredientID from the 'ingredient' table based on the ingredient name
                $stmt_getIngredientID = $dbconnect->prepare("SELECT ingredientID FROM ingredient WHERE ingredient = ?");
                $stmt_getIngredientID->bind_param("s", $ingredientName);
                $stmt_getIngredientID->execute();
                $ingredientIDResult = $stmt_getIngredientID->get_result()->fetch_assoc();
        
                if ($ingredientIDResult) {
                    $ingredientID = $ingredientIDResult['ingredientID'];
        
                    $measurement = $measurements[$index] ?? ''; // Use ingredient name as key
        
                    $sqlIngredient = "INSERT INTO recipeIngredient (recipeID, ingredientID, measurement) VALUES (?, ?, ?)";
                    $stmt_ingredient = $dbconnect->prepare($sqlIngredient);
                    $stmt_ingredient->bind_param("iii", $recipeID, $ingredientID, $measurement);
                    $stmt_ingredient->execute();
                } else {
                    echo "Error: Ingredient '$ingredientName' not found in the database.";
                    // Handle the error as needed
                }
            }
        }
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Get details of the uploaded file
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
        
            // Specify the destination directory
            $uploadDir = '/home/rnguyen2/public_html/CS487/uploads/';
        
            // Move the uploaded file to the desired location
            $destPath = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Insert the path to the image into the database
                $sql_image = "UPDATE recipe SET image = ? WHERE recipeID = ?";
                $stmt_image = $dbconnect->prepare($sql_image);
                $stmt_image->bind_param("si", $destPath, $recipeID);
                $stmt_image->execute();
            } else {
                echo "Error moving uploaded file.";
            }
        }
        

        $_SESSION['success_message'] = "Recipe created successfully!";
        echo '<script type="text/javascript">';
        echo 'alert("' . $_SESSION['success_message'] . '");';
        echo 'window.location.href = "homepage.php";';
        echo '</script>';
        exit();
    } else {
        echo "Recipe Error: " . $stmt_recipe->error;
    }
} else {
    echo "Invalid request";
}
?>
