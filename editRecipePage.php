<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="homepage.css">
</head>
<body>
    <header>
    <h1 onclick="window.location.href='homepage.php'" style="cursor: pointer;">Keep Me Healthy</h1>
    </header>
    <main>
        <h2>Edit Recipe</h2>
        <form id="editRecipeForm">
            <input type="hidden" id="recipeID" name="recipeID">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br>
            <label for="time">Time (minutes):</label>
            <input type="number" id="time" name="time" required><br>
            <label for="servings">Servings:</label>
            <input type="number" id="servings" name="servings" required><br>
            <label for="instructions">Instructions:</label>
            <textarea id="instructions" name="instructions" required></textarea><br>
            <label for="dietType">Diet Type:</label>
            <input type="text" id="dietType" name="dietType"><br>
            <button type="submit">Update Recipe</button>
        </form>
    </main>
    <footer>
        &copy; 2023 Keep Me Healthy 
    </footer>

    <script>
        // Fetch recipe details and populate the form fields
        const recipeID = <?php echo $_GET['id']; ?>;
        fetch(`editRecipe.php?id=${recipeID}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('recipeID').value = data.recipeID;
                document.getElementById('title').value = data.title;
                document.getElementById('description').value = data.description;
                document.getElementById('time').value = data.time;
                document.getElementById('servings').value = data.servings;
                document.getElementById('instructions').value = data.instructions;
                document.getElementById('dietType').value = data.dietType;
            })
            .catch(error => console.error('Error:', error));

        // Handle form submission 
        document.getElementById('editRecipeForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Perform update operation using JavaScript fetch or submit the form to updateRecipe.php
        });
    </script>
</body>
</html>

