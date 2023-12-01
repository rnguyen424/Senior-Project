<!DOCTYPE html>
<html lang="en">

<head>
    <title>Homepage</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Add custom styles for card and image */
        .card {
        margin-bottom: 20px; 
        border: 1px solid #ddd; /* Add border styling */
        border-radius: 10px; /* Add border radius for rounded corners */
        }

        /* Center the text in the card body */
        .card-body {
            text-align: center;
        }
        /*
        .navbar-brand {
        font-size: 32px; /* Adjust the font size as needed 
        }
        */
        .like-count,
        .dislike-count {
        margin-left: 5px; /* Adjust the margin as needed */
        margin-right: 5px;
        }
    </style>

</head>

<body class="bg-light">
    
    <?php
    session_start();
    require 'connection.php';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check if the user is logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        
    } else {
        // User is not logged in, show a message and redirect to the login page after 3 seconds
        echo "Please log in first to see this page.";
        echo "<meta http-equiv='refresh' content='3;url=index.php'>";
        die;
    }
    ?>

<nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white">Keep Me Healthy</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="form-inline ml-auto" method="get" action="searchRecipes.php">
                <div class="input-group">
                    <select name="sort" class="form-control mr-2 btn-sm">
                        <option value="">All Categories</option>
                        <option value="1">Most Liked</option>
                        <option value="2">Most Recent</option>
                        <option value="3">A-Z</option>
                    </select>

                    <?php
        // Fetch diet options from the database
        $sql_diet = "SELECT dietID, diet FROM diet";
        $result_diet = $dbconnect->query($sql_diet);
        ?>
        <select name="diet_type" class="form-control mr-2 btn-sm">
            <option value="">Any Diet Type</option>
            <?php
            while ($row_diet = $result_diet->fetch_assoc()) {
                echo "<option value='" . $row_diet['dietID'] . "'>" . $row_diet['diet'] . "</option>";
            }
            ?>
        </select>

        <?php
        // Fetch goal options from the database
        $sql_goal = "SELECT goalID, goal FROM goal";
        $result_goal = $dbconnect->query($sql_goal);
        ?>
        <select name="goal_type" class="form-control mr-2 btn-sm">
            <option value="">Any Goal</option>
            <?php
            while ($row_goal = $result_goal->fetch_assoc()) {
                echo "<option value='" . $row_goal['goalID'] . "'>" . $row_goal['goal'] . "</option>";
            }
            ?>
        </select>

                    <input type="text" name="search_query" class="form-control mr-2 btn-sm" placeholder="Search">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </div>
            </form>

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

    <main class="container-fluid mt-3">

        <h2>Recipes</h2>

        <div class="createrecipe mb-4">
            <button onclick="window.location.href='createRecipe.php'" class="btn btn-success btn-sm">Create Recipe</button>
        </div>

        <div class="row">
        <?php
            // Retrieve latest recipes from the database
            $query = "SELECT recipeID, title, description, image FROM recipe ORDER BY recipeID"; // Adjust the query as needed
            $result = mysqli_query($dbconnect, $query);

            $cardCounter = 0;
            $cardsPerRow = 6;

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
                    if ($cardCounter === 6) {
                        break;
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

        </div>

        

        <div class="ingredients text-center mb-3">
    <button onclick="window.location.href='recipes.php'" class="btn btn-info btn-sm">See All Recipes</button>
</div>

        <h2>Ingredients</h2>

<!-- New section for displaying ingredients in a table -->
<div class="row mt-4">
    <div class="col-md-12">
        <table id="ingredientsTable" class="table">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Type</th>
                    <th>Portion (g)</th>
                    <th>Proteins (g)</th>
                    <th>Carbs (g)</th>
                    <th>Fats (g)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                require 'connection.php';

                // Variables for pagination
                $batchSize = 10;
                $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

                // Retrieve ingredients from the database in batches
                $ingredientQuery = "SELECT i.ingredient, i.type, nf.measure, nf.proteins, nf.carbs, nf.fats 
                                    FROM ingredient i
                                    JOIN nutritionalFacts nf ON i.ingredientID = nf.ingredientID
                                    LIMIT $batchSize OFFSET $offset";
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
                    echo '<tr><td colspan="5">No ingredients found.</td></tr>';
                }

                
                mysqli_close($dbconnect);
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="ingredients text-center mb-3">
    <button onclick="window.location.href='ingredients.php'" class="btn btn-info btn-sm">See All Ingredients</button>
    <button id="seeMoreIngredients" class="btn btn-info btn-sm ml-2">See More</button>
</div>

<script>
    // JavaScript for handling "See More" button click
    document.getElementById('seeMoreIngredients').addEventListener('click', function () {
        // Retrieve the current number of displayed rows
        var currentRowCount = document.getElementById('ingredientsTable').rows.length;

        // Calculate the new number of rows to fetch
        var newRowCount = currentRowCount + 10;

        // Retrieve additional ingredients from the server using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Append the new ingredients to the table body
                document.getElementById('ingredientsTable').getElementsByTagName('tbody')[0].innerHTML += xhr.responseText;
            }
        };
        xhr.open('GET', 'getMoreIngredients.php?rowCount=' + newRowCount, true);
        xhr.send();
    });
</script>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center">
    &copy; 2023 Keep Me Healthy
    </footer>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
