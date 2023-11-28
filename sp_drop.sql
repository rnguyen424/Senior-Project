-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2023-11-23 06:19:56.173

-- foreign keys
ALTER TABLE allergenAndpreference
    DROP FOREIGN KEY Allergen_User;

ALTER TABLE feedback
    DROP FOREIGN KEY favorite_recipe;

ALTER TABLE feedback
    DROP FOREIGN KEY favorite_user;

ALTER TABLE allergenAndpreference
    DROP FOREIGN KEY ingredient_Allergen;

ALTER TABLE recipeIngredient
    DROP FOREIGN KEY ingredient_recipeIndgredient;

ALTER TABLE instruction
    DROP FOREIGN KEY instruction_recipe;

ALTER TABLE nutritionalFacts
    DROP FOREIGN KEY nutritionalFacts_ingredient;

ALTER TABLE recipeIngredient
    DROP FOREIGN KEY recipeIndgredient_recipe;

ALTER TABLE recipe
    DROP FOREIGN KEY recipe_diet;

ALTER TABLE recipe
    DROP FOREIGN KEY recipe_goal;

ALTER TABLE recipe
    DROP FOREIGN KEY recipe_user;

ALTER TABLE userGoals
    DROP FOREIGN KEY userGoals_goal;

ALTER TABLE userGoals
    DROP FOREIGN KEY userGoals_user;

-- tables
DROP TABLE adminVerify;

DROP TABLE allergenAndpreference;

DROP TABLE diet;

DROP TABLE feedback;

DROP TABLE goal;

DROP TABLE ingredient;

DROP TABLE instruction;

DROP TABLE nutritionalFacts;

DROP TABLE recipe;

DROP TABLE recipeIngredient;

DROP TABLE user;

DROP TABLE userGoals;

-- End of file.

