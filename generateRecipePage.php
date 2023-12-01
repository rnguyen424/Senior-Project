<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
} else {
    // User is not logged in, show a message and redirect to the login page after 3 seconds
    echo "Please log in first to see this page.";
    echo "<meta http-equiv='refresh' content='3;url=index.php'>";
    die;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Recipe</title>
    <link rel="stylesheet" type="text/css" href="register.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1 onclick="window.location.href='homepage.php'" style="cursor: pointer;">Keep Me Healthy</h1>
        <form action="logout.php" method="post">
            <div class="login">
                <button type="submit" name="logout">Logout</button>
            </div>
        </form>
    </header>
    <main>
        <!--froms to fill out for generation of a recipe -->
        <form action="generateRecipe.php" method="post">
        <div class="container">
            <label for="dietType">Diet Type:</label>
            <select id="dietType" name="dietType">
            <option value="">Select Diet</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="vegan">Vegan</option>
                <option value="paleo">Paleo</option>
                
            </select>

            <label for="goalType">Goal Type:</label>
            <select id="goalType" name="goalType">
                <option value="">Select Goal</option>
                <option value="maintain_weight">Maintain Weight</option>
                <option value="gain_weight">Gain Weight</option>
                <option value="lose_weight">Lose Weight</option>
                
            </select>

            <label for="allergens">Allergens:</label>
            <input type="text" id="allergens" name="allergens" placeholder="E.g., nuts, dairy">

            <label for="preferences">Preferences:</label>
            <input type="text" id="preferences" name="preferences" placeholder="E.g., high-protien, low fat">

            <button type="submit" name="generateRecipe">Generate Recipe</button>
    </div>
        </form>
    </main>
    <footer>
        &copy; 2023 Keep Me Healthy 
    </footer>
</body>
</html>
