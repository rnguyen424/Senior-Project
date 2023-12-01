<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Ingredient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
</head>

<body class="bg-light">
    <?php
    session_start();
    include 'connection.php';

    // Check if the user is logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        
    } else {
        // User is not logged in, show a message and redirect to the login page after 3 seconds
        echo "Please log in first to see this page.";
        echo "<meta http-equiv='refresh' content='3;url=index.php'>";
        die;
    }


    // Check if the ingredientID is set in the URL
    if (isset($_GET['ingredientID'])) {
        $ingredientID = $_GET['ingredientID'];

        // Retrieve existing data from the database
        $getIngredientStmt = $dbconnect->prepare("SELECT i.ingredient, i.ingredientID, i.type, nf.measure, nf.proteins, nf.fats, nf.carbs
                                                FROM ingredient i
                                                JOIN nutritionalFacts nf ON i.ingredientID = nf.ingredientID
                                                WHERE i.ingredientID = ?");
        $getIngredientStmt->bind_param("i", $ingredientID);
        $getIngredientStmt->execute();
        $result = $getIngredientStmt->get_result();

        // Check if the ingredientID is valid
        if ($result->num_rows > 0) {
            $ingredientData = $result->fetch_assoc();
        } else {
            echo "Invalid ingredientID.";
            die;
        }
    } else {
        echo "ingredientID not set in the URL.";
        die;
    }
    ?>
<!--nav bar, home, profile, logout -->
<nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white">Keep Me Healthy</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

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
        <h2>Edit Ingredient</h2>
        <form action="updateIngredient.php" method="post" enctype="multipart/form-data">
            <!-- Populate form fields with existing data -->
            <label for="type">Food Category:</label>
            <input type="text" id="type" name="type" class="form-control" value="<?php echo $ingredientData['type']; ?>" required><br>

            <label for="ingredient">Ingredient Name:</label>
            <input type="text" id="ingredient" name="ingredient" class="form-control" value="<?php echo $ingredientData['ingredient']; ?>" required><br>

            <h3>Nutritional Facts</h3>
            <label for="measure">Portion (grams):</label>
            <input type="number" id="measure" name="measure" class="form-control" value="<?php echo $ingredientData['measure']; ?>" required><br>

            <label for="proteins">Proteins (grams):</label>
            <input type="number" id="proteins" name="proteins" class="form-control" value="<?php echo $ingredientData['proteins']; ?>" required><br>

            <label for="fats">Fats (grams):</label>
            <input type="number" id="fats" name="fats" class="form-control" value="<?php echo $ingredientData['fats']; ?>" required><br>

            <label for="carbs">Carbohydrates (grams):</label>
            <input type="number" id="carbs" name="carbs" class="form-control" value="<?php echo $ingredientData['carbs']; ?>" required><br>

            <input type="hidden" name="ingredientID" value="<?php echo $ingredientData['ingredientID']; ?>">

            <button type="submit" name="submit" class="btn btn-primary">Update Ingredient</button>
        </form>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center">
        &copy; 2023 Keep Me Healthy
    </footer>
</body>

</html>
