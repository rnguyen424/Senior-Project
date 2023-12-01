<?php
//get ingredients for search when creating recipe.
session_start();
include 'connection.php';

if (isset($_GET['q'])) {
    $search = mysqli_real_escape_string($dbconnect, $_GET['q']);
    $query = "SELECT ingredient FROM ingredient WHERE ingredient LIKE '%$search%'";
    $result = mysqli_query($dbconnect, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='ingredient-option'>{$row['ingredient']}</div>";
    }
}
?>

