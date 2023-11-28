-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2023-09-11 02:30:43.532

DROP TABLE IF EXISTS allergenAndpreference;
DROP TABLE IF EXISTS diet;
DROP TABLE IF EXISTS feedback;
DROP TABLE IF EXISTS nutritionalFacts;
DROP TABLE IF EXISTS recipeIngredient;
DROP TABLE IF EXISTS ingredient;
DROP TABLE IF EXISTS userDetails;
DROP TABLE IF EXISTS recipe;
DROP TABLE IF EXISTS goal;
DROP TABLE IF EXISTS user;

-- tables
-- Table: allergenAndpreference
CREATE TABLE allergenAndpreference (
    ingredientID int  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT allergenAndpreference_pk PRIMARY KEY (userID,ingredientID)
);

-- Table: diet
CREATE TABLE diet (
    userDietID int  NOT NULL AUTO_INCREMENT,
    diet nvarchar(100)  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT diet_pk PRIMARY KEY (userDietID)
);

-- Table: feedback
CREATE TABLE feedback (
    feedbackID int  NOT NULL AUTO_INCREMENT,
    feedback nvarchar(5)  NOT NULL,
    recipeID int  NOT NULL,
    CONSTRAINT feedback_pk PRIMARY KEY (feedbackID)
);

-- Table: goal
CREATE TABLE goal (
    goalID int  NOT NULL AUTO_INCREMENT,
    goal float  NOT NULL,
    weeklyGoal float  NOT NULL,
    goalDate date  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT goal_pk PRIMARY KEY (goalID)
);

-- Table: ingredient
CREATE TABLE ingredient (
    ingredientID int  NOT NULL AUTO_INCREMENT,
    category NVARCHAR(100) NOT NULL,
    description NVARCHAR(500) NOT NULL,
    CONSTRAINT ingredient_pk PRIMARY KEY (ingredientID)
);

-- Table: nutritionalFacts
CREATE TABLE nutritionalFacts (
    nutritionalFactsID int  NOT NULL AUTO_INCREMENT,
    ingredientID INT NOT NULL,
    carbohydrate DECIMAL(10, 2),
    cholesterol DECIMAL(10, 2),
    fiber DECIMAL(10, 2),
    protein DECIMAL(10, 2),
    sugar_total DECIMAL(10, 2),
    water DECIMAL(10, 2),
    monosaturated_fat DECIMAL(10, 2),
    polysaturated_fat DECIMAL(10, 2),
    saturated_fat DECIMAL(10, 2),
    total_fat DECIMAL(10, 2),
    calcium DECIMAL(10, 2),
    iron DECIMAL(10, 2),
    potassium DECIMAL(10, 2),
    sodium DECIMAL(10, 2),
    vitamin_a DECIMAL(10, 2),
    vitamin_b12 DECIMAL(10, 2),
    vitamin_b6 DECIMAL(10, 2),
    vitamin_c DECIMAL(10, 2),
    vitamin_e DECIMAL(10, 2),
    vitamin_k DECIMAL(10, 2),
    CONSTRAINT nutritionalFacts_pk PRIMARY KEY (nutritionalFactsID)
);

-- Table: recipe
CREATE TABLE recipe (
    recipeID int  NOT NULL AUTO_INCREMENT,
    title nvarchar(100)  NOT NULL,
    description nvarchar(500)  NOT NULL,
    time int  NOT NULL,
    servings int  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT recipe_pk PRIMARY KEY (recipeID)
);

-- Table: recipeIngredient
CREATE TABLE recipeIngredient (
    recipeID int  NOT NULL,
    ingredientID int  NOT NULL,
    CONSTRAINT recipeIngredient_pk PRIMARY KEY (recipeID,ingredientID)
);

-- Table: user
CREATE TABLE user (
    userID int  NOT NULL AUTO_INCREMENT,
    firstName nvarchar(100)  NOT NULL,
    lastName nvarchar(100)  NOT NULL,
    username nvarchar(100)  NOT NULL,
    password nvarchar(100)  NOT NULL,
    email nvarchar(100)  NOT NULL,
    phoneNumber bigint  NULL,
    CONSTRAINT user_pk PRIMARY KEY (userID)
);

-- Table: userDetails
CREATE TABLE userDetails (
    userDetailID int  NOT NULL AUTO_INCREMENT,
    dateOfbirth date  NOT NULL,
    weight float  NOT NULL,
    height float  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT userDetails_pk PRIMARY KEY (userDetailID)
);

-- foreign keys
-- Reference: Allergen_User (table: allergenAndpreference)
ALTER TABLE allergenAndpreference ADD CONSTRAINT Allergen_User FOREIGN KEY Allergen_User (userID)
    REFERENCES user (userID);

-- Reference: Goal_User (table: goal)
ALTER TABLE goal ADD CONSTRAINT Goal_User FOREIGN KEY Goal_User (userID)
    REFERENCES user (userID);

-- Reference: diet_User (table: diet)
ALTER TABLE diet ADD CONSTRAINT diet_User FOREIGN KEY diet_User (userID)
    REFERENCES user (userID);

-- Reference: feedback_recipe (table: feedback)
ALTER TABLE feedback ADD CONSTRAINT feedback_recipe FOREIGN KEY feedback_recipe (recipeID)
    REFERENCES recipe (recipeID);

-- Reference: ingredient_Allergen (table: allergenAndpreference)
ALTER TABLE allergenAndpreference ADD CONSTRAINT ingredient_Allergen FOREIGN KEY ingredient_Allergen (ingredientID)
    REFERENCES ingredient (ingredientID);

-- Reference: ingredient_recipeIndgredient (table: recipeIndgredient)
ALTER TABLE recipeIngredient ADD CONSTRAINT ingredient_recipeIngredient FOREIGN KEY ingredient_recipeIngredient (ingredientID)
    REFERENCES ingredient (ingredientID);

-- Reference: nutritionalFacts_ingredient (table: nutritionalFacts)
ALTER TABLE nutritionalFacts ADD CONSTRAINT nutritionalFacts_ingredient FOREIGN KEY nutritionalFacts_ingredient (ingredientID)
    REFERENCES ingredient (ingredientID);

-- Reference: recipeIndgredient_recipe (table: recipeIndgredient)
ALTER TABLE recipeIndgredient ADD CONSTRAINT recipeIndgredient_recipe FOREIGN KEY recipeIndgredient_recipe (recipeID)
    REFERENCES recipe (recipeID);

-- Reference: recipe_User (table: recipe)
ALTER TABLE recipe ADD CONSTRAINT recipe_User FOREIGN KEY recipe_User (userID)
    REFERENCES user (userID);

-- Reference: userDetails_User (table: userDetails)
ALTER TABLE userDetails ADD CONSTRAINT userDetails_User FOREIGN KEY userDetails_User (userID)
    REFERENCES user (userID);

-- End of file.
