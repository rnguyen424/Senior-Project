<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    
} else {
    // User is not logged in, redirect to the login page
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if (isset($_POST['generateRecipe'])) {
    // Get the user input from the form
    $dietType = $_POST['dietType'];
    $goalType = $_POST['goalType'];
    $allergens = $_POST['allergens'];
    $preferences = $_POST['preferences'];

    // Construct the prompt for the OpenAI API
    $prompt = "Generate a recipe for a $dietType diet, aiming to $goalType, avoiding $allergens, and with preferences for $preferences. Make sure to show the calories, fats, proteins, and carbohydrates of the recipes as well as the total time, and.";

    // Call the OpenAI API to generate the recipe
    $recipe = generateRecipe($prompt);

    if ($recipe !== false) {
        // Display the generated recipe
        echo "<h2>Generated Recipe</h2>";
        echo "<p>$recipe</p>";
    } else {
        // Handle API error
        echo "Failed to generate the recipe. Please try again later.";
    }
} else {
    // If the form is not submitted, redirect to the form page
    header("Location: homepage.php");
    exit();
}

// Function to make a request to the OpenAI API and generate a recipe
function generateRecipe($prompt) {
    $apiKey = "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"; 
    $apiEndpoint = "https://api.openai.com/v1/engines/davinci-codex/completions";

    // Prepare the data for the API request
    $data = array(
        'prompt' => $prompt,
        'max_tokens' => 150, // Adjust as needed
    );

    // Set up the stream context with HTTP headers
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer $apiKey\r\n",
            'content' => json_encode($data),
        ),
    ));

    // Make the API request using file_get_contents
    $response = @file_get_contents($apiEndpoint, false, $context);

    if ($response === false) {
        // Handle error
        echo 'Failed to fetch data from OpenAI API.';
        return false;
    }

    // Decode the JSON response
    $responseData = json_decode($response, true);

    if (isset($responseData['choices'][0]['text'])) {
        // Extract and return the generated text
        return $responseData['choices'][0]['text'];
    } else {
        // Handle API response error
        echo 'Error in API response';
        return false;
    }
}
?>
