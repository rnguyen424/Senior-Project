<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create A Recipe</title>
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
                "<input type='text' class='instruction-input form-control' name='instructions[]' required>" +
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
    // Check if the user is logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        
    } else {
        // User is not logged in, show a message and redirect to the login page after 3 seconds
        echo "Please log in first to see this page.";
        echo "<meta http-equiv='refresh' content='3;url=index.php'>";
        die;
    }
    ?>

<!--nav bar home, profile, logout -->
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

    <!-- form to fill out for creating a recipe-->
    <main class="container mt-3">
        <form action="insertRecipe.php" method="post" enctype="multipart/form-data">
            <label for="title">Recipe Title:</label>
            <input type="text" id="title" name="title" class="form-control" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" class="form-control" required></textarea><br>

            <label for="time">Preparation Time (minutes):</label>
            <input type="number" id="time" name="time" class="form-control" required><br>

            <label for="servings">Servings:</label>
            <input type="number" id="servings" name="servings" class="form-control" required><br>

            <label for="ingredients">Search for ingredients:</label>
            <input type="text" id="ingredients" name="ingredients" placeholder="Search for ingredients" class="form-control">
            <div id="ingredientSuggestions"></div>
            <label for="ingredients">Ingredients:</label>
            <div id="selectedIngredients"></div>

            <input type="hidden" id="selectedIngredientsInput" name="selectedIngredients">

            <label for="instructions">Instructions:</label>
            <div id="instructionContainer">
            <div class="instruction-item form-group">
                <div class="d-flex">
                    <input type="text" class="instruction-input form-control" name="instructions[]" required>
                    <button type="button" class="remove-instruction btn btn-danger ml-2">Remove</button>
                </div>
            </div>
        </div>
            <button type="button" id="addInstructionBtn" class="btn btn-secondary">Add Instruction</button>

            <div class="mt-3"> <!-- Add some margin at the top for separation -->
            <label for="dietType">Diet Type:</label>
            <select id="dietType" name="dietType" class="form-control">
                <option value="">Select Diet</option>
                <?php
                $sql_diet = "SELECT dietID, diet FROM diet";
                $result_diet = $dbconnect->query($sql_diet);
                while ($row_diet = $result_diet->fetch_assoc()) {
                    echo "<option value='" . $row_diet['dietID'] . "'>" . $row_diet['diet'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="mt-3">
            <label for="goalType">Goal Type:</label>
            <select id="goalType" name="goalType" class="form-control">
                <option value="">Select Goal</option>
                <?php
                $sql_goal = "SELECT goalID, goal FROM goal";
                $result_goal = $dbconnect->query($sql_goal);
                while ($row_goal = $result_goal->fetch_assoc()) {
                    echo "<option value='" . $row_goal['goalID'] . "'>" . $row_goal['goal'] . "</option>";
                }
                ?>
            </select><br>
            </div>


            <label for="image">Recipe Image (JPG or PNG only):</label>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" class="form-control"><br>

            <button type="submit" name="submit" class="btn btn-primary">Create Recipe</button>
        </form>
    </main>

    <footer class="bg-info text-white mt-3 py-3 text-center">
        &copy; 2023 Keep Me Healthy
    </footer>
</body>

</html>
