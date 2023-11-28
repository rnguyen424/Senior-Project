<?php
require 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


$batchSize = 6; // Adjust the batch size to match the one in your main file
$offset = isset($_GET['recipeCount']) ? (int)$_GET['recipeCount'] : 0;

$query = "SELECT recipeID, title, description, image FROM recipe ORDER BY recipeID LIMIT $batchSize OFFSET $offset";
$result = mysqli_query($dbconnect, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Display each recipe as a Bootstrap card with an image
        echo '<div class="col-md-2">';
        echo '<div class="card h-100">';
        
        if (!empty($row['image'])) {
            echo '<img src="data:image/jpeg;base64,' . base64_encode(file_get_contents($row['image'])) . '" class="card-img-top" alt="Recipe Image">';
        } else {
            // Display the default image
            echo '<img src="uploads/default.png" class="card-img-top" alt="Default Image">';
        }

        echo '<div class="card-body d-flex flex-column">';
        echo '<h5 class="card-title">' . $row['title'] . '</h5>';
        echo '<p class="card-text flex-grow-1">' . $row['description'] . '</p>';
        echo '<a href="viewRecipe.php?recipeID=' . $row['recipeID'] . '" class="btn btn-primary mt-auto btn-sm">View Recipe</a>';
        echo '</div>';
        echo '<div class="card-footer">';
        echo '<form method="post" action="feedbackRecipe.php">';
        echo '<input type="hidden" name="recipeID" value="' . $row['recipeID'] . '">';
        // Like and Dislike buttons
        echo '<button type="submit" name="like" class="btn btn-success btn-sm">Like</button>';
        echo '<span class="like-count">' . getFeedbackCount($row['recipeID'], 'like') . '</span>';
        echo '<button type="submit" name="dislike" class="btn btn-danger btn-sm">Dislike</button>';
        echo '<span class="dislike-count">' . getFeedbackCount($row['recipeID'], 'dislike') . '</span>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        $cardCounter++;

        // Start a new row after displaying the specified number of cards per row
        if ($cardCounter % $cardsPerRow === 0) {
            echo '</div><div class="row mt-4">';
        }
    }
} else {
    echo 'No recipes found.';
}

function getFeedbackCount($recipeID, $feedbackType)
{
    global $dbconnect;

    $countQuery = "SELECT COUNT(*) AS count FROM feedback WHERE recipeID = $recipeID AND feedback = '$feedbackType'";
    $countResult = mysqli_query($dbconnect, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);

    return $countRow['count'];
}
mysqli_close($dbconnect);
?>
