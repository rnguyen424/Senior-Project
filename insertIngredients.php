<?php
include 'connection.php';


$api_key = 'f719a2ae2bc8d7ac9e443d59cb679ab1';
$app_id = '1d9266c7';

// Function to make an API request to Nutritionix
function fetchIngredientInfo($query) {
    global $api_key, $app_id;

    $url = 'https://api.nutritionix.com/v1_1/item';
    $params = [
        'appId' => $app_id,
        'appKey' => $api_key,
        'query' => $query,
    ];

    $response = file_get_contents($url . '?' . http_build_query($params));

    if ($response) {
        return json_decode($response, true);
    }

    return null;
}

// Example ingredient query
$ingredient_name = 'chicken breast';

$ingredient_info = fetchIngredientInfo($ingredient_name);

if ($ingredient_info) {
    // Extract relevant data from $ingredient_info (e.g., calories, protein, carbohydrates, fat)
    $name = $ingredient_info['item_name'];
    $calories = $ingredient_info['nf_calories'];
    $protein = $ingredient_info['nf_protein'];
    $carbohydrates = $ingredient_info['nf_total_carbohydrate'];
    $fat = $ingredient_info['nf_total_fat'];

    
    $insert_query = "INSERT INTO ingredients (name, calories, protein, carbohydrates, fat)
                     VALUES ('$name', $calories, $protein, $carbohydrates, $fat)";

    if (mysqli_query($connection, $insert_query)) {
        echo "Ingredient inserted successfully.";
    } else {
        echo "Error inserting ingredient: " . mysqli_error($connection);
    }
} else {
    echo "Ingredient not found in Nutritionix.";
}

// Close your database connection
mysqli_close($connection);
?>
