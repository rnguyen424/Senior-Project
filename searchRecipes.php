<?php
session_start();
require 'connection.php'; 

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if search_query is set in the URL parameters
if (isset($_GET['search_query'])) {
    $searchQuery = mysqli_real_escape_string($dbconnect, $_GET['search_query']);
    $sortOption = mysqli_real_escape_string($dbconnect, $_GET['sort']);
    $dietType = mysqli_real_escape_string($dbconnect, $_GET['diet_type']);
    $goalType = mysqli_real_escape_string($dbconnect, $_GET['goal_type']);

    // Modify the SQL query based on user input
    $query = "SELECT recipeID, title, description, image
              FROM recipe 
              WHERE title LIKE '%$searchQuery%'";

    // Add diet type filter to the SQL query
    if (!empty($dietType)) {
        $query .= " AND dietID = '$dietType'";
    }

    // Add goal type filter to the SQL query
    if (!empty($goalType)) {
        $query .= " AND goalID = '$goalType'";
    }

    // Add sorting logic if a sort option is selected
    if (!empty($sortOption)) {
        if ($sortOption == "3") {
            // Sort A-Z
            $query .= " ORDER BY title ASC";
        } else if ($sortOption == "2") {
            // Sort Most Recent
            $query .= " ORDER BY recipeID DESC";
        } else if ($sortOption == "1") {
            // Sort Most Liked
            $query .= " ORDER BY (
                SELECT COUNT(*) 
                FROM feedback 
                WHERE feedback.recipeID = recipe.recipeID 
                AND feedback.feedback = 'like'
            ) DESC";
        }
        // Add more sorting options as needed
    }

    $result = mysqli_query($dbconnect, $query);

    // Start HTML output
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<title>Search Results</title>';
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">';
    echo '<style>';
    echo '.row.recipe-row {';
    echo '    margin-top: 20px; /* Add margin between rows */';
    echo '}';
    echo '.card {';
    echo '    border: 1px solid #ddd;';
    echo '    border-radius: 10px;';
    echo '}';
    echo '.card-body {';
    echo '    text-align: center;';
    echo '}';
    echo '.like-count,';
    echo '.dislike-count {';
    echo '    margin-left: 5px;';
    echo '    margin-right: 5px;';
    echo '}';
    echo '</style>';
    echo '</head>';
    echo '<body class="bg-light">';

    // Navbar
    echo '<nav class="navbar navbar-expand-lg navbar-light bg-info">';
    echo '<a class="navbar-brand text-white">Keep Me Healthy</a>';
    echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">';
    echo '<span class="navbar-toggler-icon"></span>';
    echo '</button>';
    echo '<div class="collapse navbar-collapse" id="navbarNav">';
    echo '<form class="form-inline mx-auto" method="get" action="searchRecipes.php">';
    echo '<div class="input-group">';
    echo '<select name="sort" class="form-control mr-2 btn-sm">';
    echo '<option value="">All Categories</option>';
    echo '<option value="1">Most Liked</option>';
    echo '<option value="2">Most Recent</option>';
    echo '<option value="3">A-Z</option>';
    echo '</select>';
    // Fetch diet options from the database
    $sql_diet = "SELECT dietID, diet FROM diet";
    $result_diet = $dbconnect->query($sql_diet);
    echo '<select name="diet_type" class="form-control mr-2 btn-sm">';
    echo '<option value="">Any Diet Type</option>';
    while ($row_diet = $result_diet->fetch_assoc()) {
        echo "<option value='" . $row_diet['dietID'] . "'>" . $row_diet['diet'] . "</option>";
    }
    echo '</select>';
    // Fetch goal options from the database
    $sql_goal = "SELECT goalID, goal FROM goal";
    $result_goal = $dbconnect->query($sql_goal);
    echo '<select name="goal_type" class="form-control mr-2 btn-sm">';
    echo '<option value="">Any Goal</option>';
    while ($row_goal = $result_goal->fetch_assoc()) {
        echo "<option value='" . $row_goal['goalID'] . "'>" . $row_goal['goal'] . "</option>";
    }
    echo '</select>';
    echo '<input type="text" name="search_query" class="form-control mr-2 btn-sm" placeholder="Search">';
    echo '<button type="submit" class="btn btn-primary btn-sm">Search</button>';
    echo '</div>';
    echo '</form>';
    echo '<ul class="navbar-nav ml-auto">';
    echo '<li class="nav-item mr-2">';
    echo '<button onclick="window.location.href=\'homepage.php\'" class="btn btn-secondary btn-sm">Home</button>';
    echo '</li>';
    $user = $_SESSION['username'];
    $stmtProfile = $dbconnect->prepare("SELECT accountType FROM user WHERE username = ?");
    $stmtProfile->bind_param("s", $user);
    $stmtProfile->execute();
    $resultProfile = $stmtProfile->get_result();
    $userDataProfile = $resultProfile->fetch_assoc();
    $accountTypeProfile = $userDataProfile['accountType'];
    $profileRedirectURL = ($accountTypeProfile === 'Admin') ? 'adminProfile.php' : 'userprofile.php';
    echo '<li class="nav-item mr-2">';
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
    echo '<h2>Search Results</h2>';

    // Handle search results
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="row recipe-row">';
        while ($row = mysqli_fetch_assoc($result)) {
            // Display search results as cards
            echo '<div class="col-md-2">';
            echo '<div class="card h-100">';

        
            // Display image
            if (isset($row['image']) && !empty($row['image'])) {
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
            echo '<button type="submit" name="like" class="btn btn-success btn-sm">Like</button>';
            echo '<span class="like-count">' . getFeedbackCount($row['recipeID'], 'like') . '</span>';
            echo '<button type="submit" name="dislike" class="btn btn-danger btn-sm">Dislike</button>';
            echo '<span class="dislike-count">' . getFeedbackCount($row['recipeID'], 'dislike') . '</span>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    } else {
        echo 'No matching recipes found.';
    }

    echo '</main>';

    // Footer
    echo '<footer class="bg-info text-white mt-3 py-3 text-center">';
    echo '&copy; 2023 Keep Me Healthy';
    echo '</footer>';

    // Include necessary scripts
    echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>';
    echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>';

    echo '</body>';
    echo '</html>';

    mysqli_close($dbconnect);
} else {
    echo 'Invalid search query.';
}

function getFeedbackCount($recipeID, $feedbackType)
{
    global $dbconnect;

    $countQuery = "SELECT COUNT(*) AS count FROM feedback WHERE recipeID = $recipeID AND feedback = '$feedbackType'";
    $countResult = mysqli_query($dbconnect, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);

    return $countRow['count'];
}
?>
