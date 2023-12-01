<?php
include 'connection.php';

//path to ingredients data
$csvFile = '/home/rnguyen2/final_data.csv';

if (($handle = fopen($csvFile, 'r')) !== false) {
    fgetcsv($handle); 

    //insert into ingredients tabls and nutritional facts
    while (($data = fgetcsv($handle)) !== false) {
        $ingredientSQL = "INSERT INTO ingredient (type, ingredient) VALUES (?, ?)";
        
        $ingredientStmt = $dbconnect->prepare($ingredientSQL);
        $ingredientStmt->bind_param('ss', $data[2], $data[0]); 
        $ingredientStmt->execute();
        
        $ingredientID = $dbconnect->insert_id;

        $nutritionalFactsSQL = "INSERT INTO nutritionalFacts (measure, proteins, carbs, fats, ingredientID) 
        VALUES (?, ?, ?, ?, ?)";
        
        $nutritionalFactsStmt = $dbconnect->prepare($nutritionalFactsSQL);
        $nutritionalFactsStmt->bind_param('ddddd', $data[1], $data[3], $data[5], $data[4], $ingredientID); 
        $nutritionalFactsStmt->execute();
    }

    fclose($handle);
    echo "Data inserted successfully.";
} else {
    echo "Error reading the CSV file.";
}
?>


