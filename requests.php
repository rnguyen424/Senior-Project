<?php
    session_start();
    include 'connection.php';
    
    // Check if the user is logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        // User is logged in, you can display the protected content here
    } else {
        // User is not logged in, show a message and redirect to the login page after 3 seconds
        echo "Please log in first to see this page.";
        echo "<meta http-equiv='refresh' content='3;url=index.php'>";
        die;
    }

    // Handle form submission
    if (isset($_POST['submitRequest'])) {
        // Get user-submitted data
        $request = mysqli_real_escape_string($dbconnect, $_POST['request']);

        // Insert the request into the database
        $stmt = $dbconnect->prepare("INSERT INTO request (request) VALUES (?)");
        $stmt->bind_param("s", $request);
        $stmt->execute();
        $stmt->close();

        // Redirect to a confirmation page or perform other actions
        header("Location: ingredients.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ingredient Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Add other necessary scripts and stylesheets -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- Add your custom scripts if needed -->
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white" href="homepage.php">Keep Me Healthy</a>
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
        <h2>Ingredient Request</h2>
        <form action="requests.php" method="post">
            <label for="request">Requested Ingredient:</label>
            <input type="text" id="request" name="request" class="form-control" required><br>

            <button type="submit" name="submitRequest" class="btn btn-primary">Submit Request</button>
        </form>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center fixed-bottom">
        &copy; 2023 Keep Me Healthy
    </footer>
</body>

</html>
