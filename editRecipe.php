<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#ingredients").on("input", function() {
                var input = $(this).val();
                if (input.length >= 3) {
                    $.ajax({
                        type: "GET",
                        url: "getIngredients.php",
                        data: { q: input },
                        success: function(data) {
                            $("#ingredientSuggestions").html(data);
                        }
                    });
                } else {
                    $("#ingredientSuggestions").empty(); // Clear suggestions if input is less than 3 characters
                }
            });

            $("#addInstructionBtn").on("click", function() {
                var instructionItem = $("<div class='instruction-item form-group'>" +
                "<div class='d-flex'>" +
                "<input type='text' class='instruction-input form-control' name='instructions[]'>" +
                "<button type='button' class='remove-instruction btn btn-danger ml-2'>Remove</button>" +
                "</div>" +
                "</div>");
                instructionItem.appendTo("#instructionContainer");

                $(".remove-instruction").on("click", function() {
                    $(this).parent().remove();
                });
            });

            function addIngredient(ingredient) {
                var measurement = $("#measurements").val();
                var ingredientDiv = $("<div class='ingredient-item'>" + ingredient + 
                "<input type='number' class='measurement-input form-control' name='measurements[]' placeholder='Measurement (grams)' value='" + measurement + "'>" +
        "<span class='remove-ingredient font-weight-bold text-danger ml-1'> X</span>" +
        "</div>");
                ingredientDiv.appendTo("#selectedIngredients");
                $("#ingredients").val(''); // Clear the search bar after adding ingredient
                $("#measurements").val('');
                

                $(".remove-ingredient").click(function(){
                    $(this).parent().remove();
                });

                $("#ingredientSuggestions").empty(); // Clear all suggestions after adding ingredient
                updateSelectedIngredientsInput();
            }

            $("#ingredientSuggestions").on("click", ".ingredient-option", function() {
                var ingredient = $(this).text();
                addIngredient(ingredient);
            });

            function updateSelectedIngredientsInput() {
                var selectedIngredients = $(".ingredient-item").map(function () {
                    return $(this).text().slice(0, -1); // Remove the 'X' at the end
                }).get().join(';');
                $("#selectedIngredientsInput").val(selectedIngredients);
            }

            $(".remove-ingredient").click(function () {
                $(this).parent().remove();
                updateSelectedIngredientsInput();
            });
        });
    </script>
</head>

<body class="bg-light">
    <?php
    session_start();
    include 'connection.php';

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

    // Check if the recipeID is set in the URL
    if (isset($_GET['recipeID'])) {
        $recipeID = $_GET['recipeID'];

        // Retrieve existing data from the database
        $getRecipeStmt = $dbconnect->prepare("SELECT * FROM recipe WHERE recipeID = ?");
        $getRecipeStmt->bind_param("i", $recipeID);
        $getRecipeStmt->execute();
        $result = $getRecipeStmt->get_result();

        // Check if the recipeID is valid
        if ($result->num_rows > 0) {
            $recipeData = $result->fetch_assoc();
        } else {
            echo "Invalid recipeID.";
            die;
        }
    } else {
        echo "recipeID not set in the URL.";
        die;
    }
    ?>
    <!--nav bar, home, profile, logout -->
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
        <form action="updateRecipe.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">


            <label for="title">Recipe Title:</label>
<input type="text" id="title" name="title" class="form-control" value="<?php echo $recipeData['title']; ?>" required><br>

<label for="description">Description:</label>
<textarea id="description" name="description" rows="4" class="form-control" required><?php echo $recipeData['description']; ?></textarea><br>

<label for="time">Time (minutes):</label>
<input type="number" id="time" name="time" class="form-control" value="<?php echo $recipeData['time']; ?>" required><br>

<label for="servings">Servings:</label>
<input type="number" id="servings" name="servings" class="form-control" value="<?php echo $recipeData['servings']; ?>" required><br>

<h4>Existing Ingredients</h4>
    <ul>
        <?php
        // Fetch and display ingredients
        $ingredientQuery = "SELECT ingredient.ingredient, recipeIngredient.measurement
                            FROM ingredient
                            JOIN recipeIngredient ON ingredient.ingredientID = recipeIngredient.ingredientID
                            WHERE recipeIngredient.recipeID = ?";
        $ingredientStmt = $dbconnect->prepare($ingredientQuery);
        $ingredientStmt->bind_param("i", $recipeID);
        $ingredientStmt->execute();
        $ingredientResult = $ingredientStmt->get_result();

        while ($ingredientRow = $ingredientResult->fetch_assoc()) {
            echo '<li>' . $ingredientRow['measurement'] . 'g ' . $ingredientRow['ingredient'] . '</li>';
        }
        ?>
    </ul>

    <div class="mb-3">
                <label for="ingredients">Search for ingredients:</label>
                <input type="text" id="ingredients" name="ingredients" placeholder="Search for ingredients" class="form-control">
                <div id="ingredientSuggestions"></div>
            </div>

            <h6 class="mb-3">New List of Ingredients (include existing instruction if wanted):</h6>
            <div id="selectedIngredients"></div>

            <input type="hidden" id="selectedIngredientsInput" name="selectedIngredients">

    <h4>Existing Instructions</h4>
    <ul>
        <?php
        // Fetch and display instructions
        $instructionQuery = "SELECT step FROM instruction WHERE recipeID = ?";
        $instructionStmt = $dbconnect->prepare($instructionQuery);
        $instructionStmt->bind_param("i", $recipeID);
        $instructionStmt->execute();
        $instructionResult = $instructionStmt->get_result();

        while ($instructionRow = $instructionResult->fetch_assoc()) {
            echo '<li>' . $instructionRow['step'] . '</li>';
        }
        ?>
    </ul>

    <h6 class="mb-3">New Set of Instructions (include existing instruction if wanted):</h6>
            <div id="instructionContainer">
            <div class="instruction-item form-group">
                <div class="d-flex">
                    <input type="text" class="instruction-input form-control" name="instructions[]">
                    <button type="button" class="remove-instruction btn btn-danger ml-2">Remove</button>
                </div>
            </div>
        </div>
            <button type="button" id="addInstructionBtn" class="btn btn-secondary mb-3">Add Instruction</button>

<!--drop down for diet type -->
            <div class="mb-3">
                <label for="dietType">Diet Type:</label>
                <select id="dietType" name="dietType" class="form-control">
                    <option value="">Select Diet</option>
                    <?php
                    $sql_diet = "SELECT dietID, diet FROM diet";
                    $result_diet = $dbconnect->query($sql_diet);
                    while ($row_diet = $result_diet->fetch_assoc()) {
                        $selected = ($row_diet['dietID'] == $recipeData['dietID']) ? "selected" : "";
                        echo "<option value='" . $row_diet['dietID'] . "' $selected>" . $row_diet['diet'] . "</option>";
                    }
                    ?>
                </select>
            </div>
<!--drop down for goals-->

<label for="goalType">Goal Type:</label>
<select id="goalType" name="goalType" class="form-control">
    <option value="">Select Goal</option>
    <?php
    $sql_goal = "SELECT goalID, goal FROM goal";
    $result_goal = $dbconnect->query($sql_goal);
    while ($row_goal = $result_goal->fetch_assoc()) {
        $selected = ($row_goal['goalID'] == $recipeData['goalID']) ? "selected" : "";
        echo "<option value='" . $row_goal['goalID'] . "' $selected>" . $row_goal['goal'] . "</option>";
    }
    ?>
</select><br>

<!-- image & check if image path is null-->

<label for="image">Recipe Image (JPG or PNG only):</label>
<input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" class="form-control"><br>

<?php
    if (!empty($recipeData['image'])) {
        echo "<p>Current Image: " . basename($recipeData['image']) . "</p>";
    } else {
        echo "<p>Current Image: No Recipe Image</p>";
    }
?>


            <button type="submit" name="submit" class="btn btn-primary">Update Recipe</button>
        </form>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center">
        &copy; 2023 Keep Me Healthy
    </footer>
</body>

</html>

