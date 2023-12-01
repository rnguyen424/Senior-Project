<?php
session_start();
require 'connection.php';

//Get ID from username
$user = $_SESSION['username'];
$stmt = $dbconnect->prepare("SELECT userID FROM user WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$ID = $stmt->get_result()->fetch_assoc();
$userID = $ID['userID'];


if (isset($_GET['feedback_removed']) && $_GET['feedback_removed'] == 'true') {
    // Display a script message
    echo '<script>alert("Feedback removed successfully!");</script>';
}

if (isset($_GET['recipe_deleted']) && $_GET['recipe_deleted'] == 'true') {
    // Display a script message for recipe deletion
    echo '<script>alert("Recipe deleted successfully!");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .sidebar {
            background-color: #f0f0f0; /* Light gray background color */
            padding: 20px;
        }


        #v-pills-recipes {
            padding-bottom: 100px; 
        }
    </style>
</head>

<body class="bg-light">
    <header>
        <!--nav bar, home button, profile, logout-->
        <nav class="navbar navbar-expand-lg navbar-light bg-info">
            <a class="navbar-brand text-white" >Keep Me Healthy</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item mr-2">
                        <form action="homepage.php" method="post">
                            <button type="submit" name="submit" class="btn btn-secondary btn-sm">Home</button>
                        </form>
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
    </header>

    <!--tabs for profile, view recipes and feedback-->
    <main class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-recipes-tab" data-toggle="pill" href="#v-pills-recipes"
                        role="tab" aria-controls="v-pills-recipes" aria-selected="true">My Recipes</a>
                    <a class="nav-link" id="v-pills-feedback-tab" data-toggle="pill" href="#v-pills-feedback" role="tab"
                        aria-controls="v-pills-feedback" aria-selected="false">My Feedback</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-recipes" role="tabpanel"
                        aria-labelledby="v-pills-recipes-tab">
                        <!--view user recipes-->
                        <h2>My Recipes</h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $dbconnect->prepare("SELECT * FROM recipe WHERE userID = ?");
                                $stmt->bind_param("i", $userID);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        
                                        <td><?php echo $row['title']; ?></td>
                                        <td><?php echo $row['description']; ?></td>
                                        <td>
                                        <button onclick="window.location.href='editRecipe.php?recipeID=<?php echo $row['recipeID']; ?>'" class="btn btn-secondary btn-sm">Edit</button>
                                        <form action="deleteRecipe.php" method="post" style="display: inline-block;">
                                        <input type="hidden" name="recipeID" value="<?php echo $row['recipeID']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form         
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="add">
                        <button onclick="window.location.href='createRecipe.php'" class="btn btn-primary btn-sm">Create Recipe</button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-feedback" role="tabpanel"
                        aria-labelledby="v-pills-feedback-tab">
                        <h2>My Feedback</h2>
    <!-- Liked Recipes Table -->
    <h3>Liked Recipes</h3>
    <table class="table">
    
    <tbody>
        <?php
        $likedStmt = $dbconnect->prepare("SELECT feedback.feedbackID, recipe.title, recipe.description, recipe.recipeID FROM feedback 
        JOIN recipe ON feedback.recipeID = recipe.recipeID 
        WHERE feedback.userID = ? AND feedback.feedback = 'like'");
        $likedStmt->bind_param("i", $userID);
        $likedStmt->execute();
        $likedResult = $likedStmt->get_result();
        while ($row = $likedResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <form action="removeFeedback.php" method="post" style="display: inline-block;">
                        <input type="hidden" name="feedbackID" value="<?php echo $row['feedbackID']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Remove Like</button>
                    </form>
                </td>
                <td>
                        <a href="https://turing.cs.olemiss.edu/~rnguyen2/CS487/viewRecipe.php?recipeID=<?php echo $row['recipeID']; ?>" class="btn btn-info btn-sm">View Recipe</a>
                    </td>
            </tr>
        <?php } ?>
    </tbody>
</table>


    <!-- Disliked Recipes Table -->
    <h3>Disliked Recipes</h3>
    <table class="table">
        <thead>
        </thead>
        <tbody>
            <?php
            $dislikedStmt = $dbconnect->prepare("SELECT recipe.title, recipe.description, recipe.recipeID FROM feedback 
                                                JOIN recipe ON feedback.recipeID = recipe.recipeID 
                                                WHERE feedback.userID = ? AND feedback.feedback = 'dislike'");
            $dislikedStmt->bind_param("i", $userID);
            $dislikedStmt->execute();
            $dislikedResult = $dislikedStmt->get_result();
            while ($row = $dislikedResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <form action="removeFeedback.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="feedbackID" value="<?php echo $row['recipeID']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove Dislike</button>
                        </form>
                    </td>
                    <td>
                        <a href="https://turing.cs.olemiss.edu/~rnguyen2/CS487/viewRecipe.php?recipeID=<?php echo $row['recipeID']; ?>" class="btn btn-info btn-sm">View Recipe</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center ">
        &copy; 2023 Keep Me Healthy
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>
