<?php
session_start();
require 'connection.php';


// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    // User is logged in, you can display the protected content here
} else {
    // User is not logged in, show a message and redirect to the login page after 3 seconds
    echo "Please log in first to see this page.";
    echo "<meta http-equiv='refresh' content='3;url=index.php'>";
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recipe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-info">
    <a class="navbar-brand text-white">Keep Me Healthy</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item mr-2">
                <button onclick="window.location.href='homepage.php'" class="btn btn-secondary btn-sm">Home</button>
            </li>
            <li class="nav-item mr-2">
                <?php
                $user = $_SESSION['username'];
                $stmtProfile = $dbconnect->prepare("SELECT accountType FROM user WHERE username = ?");
                $stmtProfile->bind_param("s", $user);
                $stmtProfile->execute();
                $resultProfile = $stmtProfile->get_result();
                $userDataProfile = $resultProfile->fetch_assoc();
                $accountTypeProfile = $userDataProfile['accountType'];
                $profileRedirectURL = ($accountTypeProfile === 'Admin') ? 'adminProfile.php' : 'userprofile.php';
                ?>
                <form action="<?php echo $profileRedirectURL; ?>" method="post">
                    <button type="submit" name="submit" class="btn btn-secondary btn-sm">My Profile</button>
                </form>
            </li>
            <li class="nav-item">
                <form action="logout.php" method="post">
                    <button type="submit" name="logout" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<main class="container mt-3">

    <?php
    if (isset($_GET['recipeID'])) {
        $recipeID = $_GET['recipeID'];
        $query = "SELECT * FROM recipe
                  JOIN goal ON recipe.goalID = goal.goalID
                  JOIN diet ON recipe.dietID = diet.dietID
                  WHERE recipe.recipeID = ?";
        $stmt = $dbconnect->prepare($query);
        $stmt->bind_param("i", $recipeID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Display the full recipe information here
            echo '<h1>' . $row['title'] . '</h1>';
            echo '<p>' . $row['description'] . '</p>';

            $imagePath = $row['image'];
            if (file_exists($imagePath)) {
                $imageData = file_get_contents($imagePath);
                $base64Image = base64_encode($imageData);

                $imageWidth = '600px'; // Example width
                $imageHeight = '400px'; // Example height
            
                echo '<img src="data:image/jpeg;base64,' . $base64Image . '" alt="Recipe Image" style="width: ' . $imageWidth . '; height: ' . $imageHeight . ';">';
            } else {
                echo ' ' . $imagePath;
            }

            echo '<div class="mt-3"></div>';

             // Display the details below the image
             echo '<table class="table">';
             echo '<tr><th>Total Time</th><th>Servings</th><th>Goal</th><th>Diet</th></tr>';
             echo '<tr>';
             echo '<td>' . $row['time'] . ' mins</td>';
             echo '<td>' . $row['servings'] . '</td>';
             echo '<td>' . $row['goal'] . '</td>';
             echo '<td>' . $row['diet'] . '</td>';
             echo '</tr>';
             echo '</table>';

            echo '<h2>Ingredients</h2>';
            // Fetch and display ingredients
            $ingredientQuery = "SELECT ingredient.ingredient, recipeIngredient.measurement
                                FROM ingredient
                                JOIN recipeIngredient ON ingredient.ingredientID = recipeIngredient.ingredientID
                                WHERE recipeIngredient.recipeID = ?";
            $ingredientStmt = $dbconnect->prepare($ingredientQuery);
            $ingredientStmt->bind_param("i", $recipeID);
            $ingredientStmt->execute();
            $ingredientResult = $ingredientStmt->get_result();

            echo '<ul>';
            while ($ingredientRow = $ingredientResult->fetch_assoc()) {
                echo '<li>' . $ingredientRow['measurement'] . 'g    ' . $ingredientRow['ingredient'] . '</li>';
            }
            echo '</ul>';

            echo '<h2>Instructions</h2>';
            $instructionQuery = "SELECT step FROM instruction WHERE recipeID = ?";
            $instructionStmt = $dbconnect->prepare($instructionQuery);
            $instructionStmt->bind_param("i", $recipeID);
            $instructionStmt->execute();
            $instructionResult = $instructionStmt->get_result();

            if ($instructionResult->num_rows > 0) {
                echo '<ol>';
                while ($instructionRow = $instructionResult->fetch_assoc()) {
                    echo '<li>' . $instructionRow['step'] . '</li>';
                }
                echo '</ol>';
            } else {
                echo 'No instructions found for this recipe.';
            }

            // Display Nutritional Facts
            $nutritionalQuery = "SELECT nf.measure, nf.proteins, nf.carbs, nf.fats, ri.measurement AS user_measurement
                    FROM nutritionalFacts nf
                    JOIN recipeIngredient ri ON nf.ingredientID = ri.ingredientID
                    JOIN recipe r ON r.recipeID = ri.recipeID
                    WHERE r.recipeID = ?";
            $nutritionalStmt = $dbconnect->prepare($nutritionalQuery);
            $nutritionalStmt->bind_param("i", $recipeID);
            $nutritionalStmt->execute();
            $nutritionalResult = $nutritionalStmt->get_result();

            if ($nutritionalResult->num_rows > 0) {
                echo '<h2>Nutritional Facts</h2>';
                echo '<ul>';
                while ($nutritionalRow = $nutritionalResult->fetch_assoc()) {
                    $userMeasurement = $nutritionalRow['user_measurement'];
                    $adjustedProteins = $nutritionalRow['proteins'] * ($userMeasurement / $nutritionalRow['measure']);
                    $adjustedCarbs = $nutritionalRow['carbs'] * ($userMeasurement / $nutritionalRow['measure']);
                    $adjustedFats = $nutritionalRow['fats'] * ($userMeasurement / $nutritionalRow['measure']);
                
                    // Accumulate the values
                    $totalProteins += $adjustedProteins;
                    $totalCarbs += $adjustedCarbs;
                    $totalFats += $adjustedFats;
                    }

                    $servings = $row['servings'];
                    $totalCalories = (($totalCarbs * 4) + ($totalProteins * 4) + ($totalFats * 9)) / $servings;

                    // Display the total nutritional facts
                    echo '<li>Calories: ' . $totalCalories . 'kcal (Per Serving)</li>';
                    echo '<li>Proteins: ' . $totalProteins . 'g</li>';
                    echo '<li>Carbohydrates: ' . $totalCarbs . 'g</li>';
                    echo '<li>Fats: ' . $totalFats . 'g</li>';

                echo '</ul>';
            } else {
                echo 'No nutritional facts found for this recipe.';
            }

        } else {
            echo 'Recipe not found.';
        }
        $stmt->close();
        $ingredientStmt->close();
    } else {
        echo 'Invalid recipe ID.';
    }
    ?>

</main>

<footer class="bg-info text-white mt-3 py-3 text-center ">
    &copy; 2023 Keep Me Healthy
</footer>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
