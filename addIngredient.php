<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add An Ingredient</title>
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
    ?>

<!--nav bar, home, profile, logout -->
    <nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white" >Keep Me Healthy</a>
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
        <h2>Add An Ingredient</h2>
        <form action="insertIngredient.php" method="post" enctype="multipart/form-data">
            <!--  ingredient input fields  -->
            <label for="type">Food Category:</label>
            <input type="text" id="type" name="type" class="form-control" required><br>

            <label for="ingredient">Ingredient Name:</label>
            <input type="text" id="ingredient" name="ingredient" class="form-control" required><br>


            <h3>Nutritional Facts</h3>
            <label for="measure">Portion (grams):</label>
            <input type="number" id="measure" name="measure" class="form-control" required><br>

            <label for="proteins">Proteins (grams):</label>
            <input type="text" id="proteins" name="proteins" class="form-control" required step="any"><br>

            <label for="fats">Fats (grams):</label>
            <input type="text" id="fats" name="fats" class="form-control" required step="any"><br>

            <label for="carbs">Carbohydrates (grams):</label>
            <input type="text" id="carbs" name="carbs" class="form-control" required step="any"><br>



            <button type="submit" name="submit" class="btn btn-primary">Add Ingredient</button>
        </form>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center ">
        &copy; 2023 Keep Me Healthy
    </footer>
</body>

</html>