<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ingredients</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body class="bg-light">
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

<?php
    if (isset($_SESSION['ingredient_removed']) && $_SESSION['ingredient_removed'] === true) {
        echo '<script>';
        echo 'alert("Ingredient successfully removed!");';
        echo '</script>';

        // Reset the session variable to avoid displaying the message on subsequent page loads
        $_SESSION['ingredient_removed'] = false;
    }

    if (isset($_SESSION['ingredient_updated']) && $_SESSION['ingredient_updated']) {
        echo '<script>alert("Ingredient updated successfully!");</script>';
        // Reset the session variable to avoid showing the message on page refresh
        $_SESSION['ingredient_updated'] = false;
    }


    ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white">Keep Me Healthy</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="form-inline ml-auto" method="get" action="searchIngredients.php">
                <div class="input-group">
                    <select name="sort" class="form-control mr-2 btn-sm">
                        <option value="">All Categories</option>
                        <option value="1">Most Recently Added</option>
                        <option value="2">By Type</option>
                        <option value="3">A-Z</option>
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

    <h2>Ingredients</h2>

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
                    // Ensure the database connection is still open
                    require 'connection.php';

                    // Variables for pagination
                    $batchSize = 20;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $batchSize;

                    // Retrieve ingredients from the database in batches
                    $ingredientQuery = "SELECT i.ingredient, i.ingredientID, i.type, nf.measure, nf.proteins, nf.carbs, nf.fats 
                                        FROM ingredient i
                                        JOIN nutritionalFacts nf ON i.ingredientID = nf.ingredientID
                                        LIMIT $batchSize OFFSET $offset";
                    $ingredientResult = mysqli_query($dbconnect, $ingredientQuery);

                    $isUserAdmin = $accountTypeProfile === 'Admin';


                    if ($ingredientResult && mysqli_num_rows($ingredientResult) > 0) {
                        while ($ingredientRow = mysqli_fetch_assoc($ingredientResult)) {
                            echo '<tr>';
                            echo '<td>' . $ingredientRow['ingredient'] . '</td>';
                            echo '<td>' . $ingredientRow['type'] . '</td>';
                            echo '<td>' . $ingredientRow['measure'] . '</td>';
                            echo '<td>' . $ingredientRow['proteins'] . '</td>';
                            echo '<td>' . $ingredientRow['carbs'] . '</td>';
                            echo '<td>' . $ingredientRow['fats'] . '</td>';

                            if ($isUserAdmin) {
                                // Add buttons or any other actions for the admin
                                echo '<td>';
                                echo '<button onclick="window.location.href=\'editIngredient.php?ingredientID=' . $ingredientRow['ingredientID'] . '\'" class="btn btn-warning btn-sm">Edit</button>&nbsp;';
                                echo '<a href="deleteIngredient.php?ingredientID=' . $ingredientRow['ingredientID'] . '" class="btn btn-danger btn-sm">Delete</a>';
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5">No ingredients found.</td></tr>';
                    }

                    // Close the database connection
                    mysqli_close($dbconnect);
                    ?>
                </tbody>
            </table>

            <!-- Pagination controls -->
            <div class="pagination mt-3">
                <?php
                // Previous button
                $prevPage = $currentPage - 1;
                if ($prevPage > 0) {
                    echo '<a href="?page=' . $prevPage . '" class="btn btn-secondary">Previous</a>';
                }

                // Next button
                $nextPage = $currentPage + 1;
                echo '<a href="?page=' . $nextPage . '" class="btn btn-secondary ml-2">Next</a>';
                ?>
            </div>
        </div>
    </div>
</main>

    <footer class="bg-info text-white mt-3 py-3 text-center">
        &copy; 2023 Keep Me Healthy
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
