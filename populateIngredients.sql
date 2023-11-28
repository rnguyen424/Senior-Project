-- Load data into the ingredient table
LOAD DATA INFILE '/home/rnguyen2/food.csv'
INTO TABLE ingredient
FIELDS TERMINATED BY ',' 
LINES TERMINATED BY '\n'
(ingredientID, category, description);

INSERT INTO nutritionalFacts (ingredientID, carbohydrate, cholesterol, fiber, protein, sugar_total, water, monosaturated_fat, polysaturated_fat, saturated_fat, total_fat, calcium, iron, potassium, sodium, vitamin_a, vitamin_b12, vitamin_b6, vitamin_c, vitamin_e, vitamin_k)
SELECT
   LAST_INSERT_ID(), 
   `Data.Carbohydrate`,
   `Data.Cholesterol`,
   `Data.Fiber`,
   `Data.Protein`,
   `Data.Sugar Total`,
   `Data.Water`,
   `Data.Fat.Monosaturated Fat`,
   `Data.Fat.Polysaturated Fat`,
   `Data.Fat.Saturated Fat`,
   `Data.Fat.Total Lipid`,
   `Data.Major Minerals.Calcium`,
   `Data.Major Minerals.Iron`,
   `Data.Major Minerals.Potassium`,
   `Data.Major Minerals.Sodium`,
   `Data.Vitamins.Vitamin A - RAE`,
   `Data.Vitamins.Vitamin B12`,
   `Data.Vitamins.Vitamin B6`,
   `Data.Vitamins.Vitamin C`,
   `Data.Vitamins.Vitamin E`,
   `Data.Vitamins.Vitamin K`
FROM ingredient
WHERE ingredientID = LAST_INSERT_ID();
