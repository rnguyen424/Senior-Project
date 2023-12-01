<!DOCTYPE html>
<html lang="en">

<head>
    <title>Homepage</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>

        main {
            margin-top: 20px;
        }

        <style>
        /* Add custom styles for card and image */
        .card {
        margin-bottom: 20px;
        border: 1px solid #ddd; /* Add border styling */
        border-radius: 10px; /* Add border radius for rounded corners */
        }

        .like-count,
        .dislike-count {
        margin-left: 5px; /* Adjust the margin as needed */
        margin-right: 5px;
        }

        .see-more-btn {
            background-color: #17a2b8; /* Match the navbar and footer color */
            color: white;
            border: none;
            margin: 20px auto; /* Center the button */
            display: block;
        }

        .see-more-btn:hover {
            background-color: #138496; /* Darker shade on hover */
        }

    </style>
</head>

<body>
    <!--nav bar, home-->
<nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white" onclick="window.location.href='index.php'">Keep Me Healthy</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="form-inline ml-auto" method="post" action="login.php">
                <div class="login">
                    <input type="text" name="username" placeholder="Username" required class="form-control form-control-sm">
                    <input type="password" name="password" placeholder="Password" required class="form-control form-control-sm">
                    <button type="submit" name="submit" class="btn btn-primary btn-sm">Login</button>
                    <button type="button" onclick="location.href='register.php'" class="btn btn-success btn-sm">Register</button>

                    <div class="small text-right mt-2">
                    Forgot Password? <a href="contactUs.php">Contact Us</a>
                </div>
                </div>
            </form>
        </div>
    </nav>

    <main class="container-fluid">

        <h2>Recipes</h2>
        <div class="row">
        <?php

            require 'connection.php';
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
                    echo '<img src="data:image/jpeg;base64,' . base64_encode(file_get_contents($row['image'])) . '" class="card-img-top" alt="Recipe Image">';
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
        <div class="index text-center mb-3">
        <button id="see-more-btn" onclick="window.location.href='homepage.php'" class="btn btn-info btn-sm" >See More Recipes</button>
        </div>   
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center fixed-bottom">
        &copy; 2023 Keep Me Healthy
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
