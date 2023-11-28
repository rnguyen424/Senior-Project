<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create an Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <style>
        main {
            padding: 20px;
        }

        label {
            margin-top: 10px;
        }

        /* Add styles for spacing between elements */
        .form-group {
            margin-bottom: 20px;
        }

        /* Add styles for the header */
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white">Keep Me Healthy</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-2">
                    <button onclick="window.location.href='index.php'" class="btn btn-secondary btn-sm">Home</button>
                </li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <h2>Register An Account</h2>
        <form action="accountCreate.php" method="post">

            <div class="form-group">
                <label for="firstName"><b>First Name</b></label>
                <input type="text" name="firstName" placeholder="Enter First Name" id="firstName"
                    class="form-control" required>
            
                <label for="lastName"><b>Last Name</b></label>
                <input type="text" name="lastName" placeholder="Enter Last Name" id="lastName"
                    class="form-control" required>
            
                <label for="email"><b>Email</b></label>
                <input type="email" name="email" placeholder="Enter Email" id="email" class="form-control"
                    required>
    
            
                <label for="phoneNumber"><b>Phone Number</b></label>
                <input type="number" name="phoneNumber" placeholder="Enter Phone Number" id="phoneNumber"
                    class="form-control" required>
            
                <label for="username"><b>Username</b></label>
                <input type="text" name="username" placeholder="Enter Username" id="un" class="form-control"
                    required>
            
                <label for="password"><b>Password</b></label>
                <input type="password" name="password" placeholder="Enter Password" id="pw" class="form-control"
                    required>
            
                <label for="pas-repeat"><b>Repeat Password</b></label>
                <input type="password" name="passwordrpt" placeholder="Re-Enter Password" id="pw-repeat"
                    class="form-control" required>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="Register" class="btn btn-primary">
            </div>

            <p>Already have an account? <a href="index.php">Sign in here</a></p>
            <p>Register as an Admin? <a href="adminRegister.php">Click here</a></p>
        </form>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center">
        &copy; 2023 Keep Me Healthy
    </footer>

</body>
</html>
