<?php

require 'connection.php';

// Retrieve the desired number of rows from the database
$rowCount = isset($_GET['rowCount']) ? (int)$_GET['rowCount'] : 0;

$ingredientQuery = "SELECT i.ingredient, i.type, nf.measure, nf.proteins, nf.carbs, nf.fats 
                    FROM ingredient i
                    JOIN nutritionalFacts nf ON i.ingredientID = nf.ingredientID
                    LIMIT $rowCount, 10";
$ingredientResult = mysqli_query($dbconnect, $ingredientQuery);

if ($ingredientResult && mysqli_num_rows($ingredientResult) > 0) {
    while ($ingredientRow = mysqli_fetch_assoc($ingredientResult)) {
        echo '<tr>';
        echo '<td>' . $ingredientRow['ingredient'] . '</td>';
        echo '<td>' . $ingredientRow['type'] . '</td>';
        echo '<td>' . $ingredientRow['measure'] . '</td>';
        echo '<td>' . $ingredientRow['proteins'] . '</td>';
        echo '<td>' . $ingredientRow['carbs'] . '</td>';
        echo '<td>' . $ingredientRow['fats'] . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5">No more ingredients found.</td></tr>';
}


mysqli_close($dbconnect);
?>
