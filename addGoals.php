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

<!-- form to fill out for adding goals to the user -->
<!DOCTYPE html>
<html>
<head>
    <title>Add Your Goals</title>
    <link rel="stylesheet" type="text/css" href="register.css">
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
    <form action="insertGoals.php" method="post">
            <div class="container">
                <label for="goal"><b>Goal</b></label>
                <input type="number" name="goal" step="0.01" required>

                <label for="weeklyGoal"><b>Weekly Goal</b></label>
                <input type="number" name="weeklyGoal" step="0.01" required>

                <label for="goalDate"><b>Goal Date</b></label>
                <input type="date" name="goalDate" required>

                <input type="submit" name="submit" value="Add Goal">
            </div>
        </form>
</main>
<footer>
        &copy; 2023 Keep Me Healthy 
    </footer>
</body>
</html>