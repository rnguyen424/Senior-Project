<?php
session_start();
include 'connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the default values for sorting and pagination
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : '';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$resultsPerPage = 20;
$offset = ($currentPage - 1) * $resultsPerPage;

// Get the search term
if (isset($_GET['search_query'])) {
    $searchTerm = mysqli_real_escape_string($dbconnect, $_GET['search_query']);

    // Modify your SQL query based on the selected sort option
    switch ($sortOption) {
        case '1': // Most Recently Added
            $orderBy = 'ORDER BY ingredient.ingredientID DESC';
            break;
        case '2': // By Type
            $orderBy = 'ORDER BY ingredient.type';
            break;
        case '3': // A-Z
            $orderBy = 'ORDER BY ingredient.ingredient';
            break;
        default:
            // Default sorting
            $orderBy = '';
    }

    $sql = "SELECT ingredient.*, nutritionalFacts.* FROM ingredient
            LEFT JOIN nutritionalFacts ON ingredient.ingredientID = nutritionalFacts.ingredientID
            WHERE ingredient.ingredient LIKE '%$searchTerm%'
            $orderBy
            LIMIT $offset, $resultsPerPage";

    $result = mysqli_query($dbconnect, $sql);

    // Header
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<title>Search Results</title>';
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">';
    echo '</head>';
    echo '<body class="bg-light">';

echo '<nav class="navbar navbar-expand-lg navbar-light bg-info">';
echo '<a class="navbar-brand text-white" href="homepage.php">Keep Me Healthy</a>';
echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">';
echo '<span class="navbar-toggler-icon"></span>';
echo '</button>';
echo '<div class="collapse navbar-collapse" id="navbarNav">';
echo '<form class="form-inline ml-auto" method="get" action="searchIngredients.php">';
echo '<div class="input-group">';
echo '<select name="sort" class="form-control mr-2 btn-sm">';
echo '<option value="">All Categories</option>';
echo '<option value="1">Most Recently Added</option>';
echo '<option value="2">By Type</option>';
echo '<option value="3">A-Z</option>';
echo '</select>';
echo '<input type="text" name="search_query" class="form-control mr-2 btn-sm" placeholder="Search">';
echo '<button type="submit" class="btn btn-primary btn-sm">Search</button>';
echo '</div>';
echo '</form>';

echo '<ul class="navbar-nav ml-auto">';
echo '<li class="nav-item mr-2">';
echo '<a href="homepage.php" class="btn btn-secondary btn-sm">Home</a>';
echo '</li>';
echo '<li class="nav-item mr-2">';
$user = $_SESSION['username'];
$stmtProfile = $dbconnect->prepare("SELECT accountType FROM user WHERE username = ?");
$stmtProfile->bind_param("s", $user);
$stmtProfile->execute();
$resultProfile = $stmtProfile->get_result();
$userDataProfile = $resultProfile->fetch_assoc();
$accountTypeProfile = $userDataProfile['accountType'];
$profileRedirectURL = ($accountTypeProfile === 'Admin') ? 'adminProfile.php' : 'userprofile.php';
echo '<form action="' . $profileRedirectURL . '" method="post">';
echo '<button type="submit" name="submit" class="btn btn-secondary btn-sm">My Profile</button>';
echo '</form>';
echo '</li>';
echo '<li class="nav-item">';
echo '<form action="logout.php" method="post">';
echo '<button type="submit" name="logout" class="btn btn-danger btn-sm">Logout</button>';
echo '</form>';
echo '</li>';
echo '</ul>';
echo '</div>';
echo '</nav>';

    echo '<main class="container-fluid mt-3">';
    echo '<div class="row mt-4">';
    echo '<div class="col-md-12">';
    echo '<table id="ingredientsTable" class="table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Ingredient</th>';
    echo '<th>Type</th>';
    echo '<th>Portion (g)</th>';
    echo '<th>Proteins (g)</th>';
    echo '<th>Carbs (g)</th>';
    echo '<th>Fats (g)</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $accountTypeProfile = '';

    if (isset($_SESSION['username'])) {
        $user = $_SESSION['username'];
        $stmtProfile = $dbconnect->prepare("SELECT accountType FROM user WHERE username = ?");
        $stmtProfile->bind_param("s", $user);
        $stmtProfile->execute();
        $resultProfile = $stmtProfile->get_result();
        $userDataProfile = $resultProfile->fetch_assoc();
        $accountTypeProfile = isset($userDataProfile['accountType']) ? $userDataProfile['accountType'] : '';
    }

    if ($result && mysqli_num_rows($result) > 0) {
        // Include a function to generate table rows
        function generateTableRow($ingredientRow)
        {

            global $accountTypeProfile;
            echo '<tr>';
            echo '<td>' . $ingredientRow['ingredient'] . '</td>';
            echo '<td>' . $ingredientRow['type'] . '</td>';
            echo '<td>' . $ingredientRow['measure'] . '</td>';
            echo '<td>' . $ingredientRow['proteins'] . '</td>';
            echo '<td>' . $ingredientRow['carbs'] . '</td>';
            echo '<td>' . $ingredientRow['fats'] . '</td>';

            if ($accountTypeProfile === 'Admin') {
                // Add buttons or any other actions for the admin
                echo '<td>';
                echo '<button onclick="window.location.href=\'editIngredient.php?ingredientID=' . $ingredientRow['ingredientID'] . '\'" class="btn btn-warning btn-sm">Edit</button>&nbsp;';
                echo '<a href="deleteIngredient.php?ingredientID=' . $ingredientRow['ingredientID'] . '" class="btn btn-danger btn-sm">Delete</a>';
                echo '</td>';
            }
            
            echo '</tr>';
        }

        // Output the search results using the function
        while ($ingredientRow = mysqli_fetch_assoc($result)) {
            generateTableRow($ingredientRow);
        }
    } else {
        echo '<tr><td colspan="5">No ingredients found.</td></tr>';

    }

    echo '</tbody>';
    echo '</table>';

    // Pagination controls
    echo '<div class="pagination mt-3">';
$prevPage = $currentPage - 1;
if ($prevPage > 0) {
    echo '<a href="searchIngredients.php?page=' . $prevPage . '&sort=' . $sortOption . '&search_query=' . $searchTerm . '" class="btn btn-secondary">Previous</a>';
}
$nextPage = $currentPage + 1;
echo '<a href="searchIngredients.php?page=' . $nextPage . '&sort=' . $sortOption . '&search_query=' . $searchTerm . '" class="btn btn-secondary ml-2">Next</a>';
echo '</div>';

echo '<div class="text-center mt-3">';
echo 'Don\'t see an ingredient you want? <a href="requests.php" class="text-primary">Send a request</a>';
echo '</div>';

    echo '</div>';
    echo '</div>';
    echo '</main>';

    // Footer
    echo '<footer class="bg-info text-white mt-3 py-3 text-center">';
    echo '&copy; 2023 Keep Me Healthy';
    echo '</footer>';

    echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>';
    echo '</body>';
    echo '</html>';
}
?>