-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2023-11-23 06:19:56.173

-- tables
-- Table: adminVerify
CREATE TABLE adminVerify (
    adminCode int  NOT NULL,
    CONSTRAINT adminVerify_pk PRIMARY KEY (adminCode)
);

CREATE TABLE request (
    requestID INT NOT NULL AUTO_INCREMENT,
    request NVARCHAR(255) NOT NULL,
    PRIMARY KEY (requestID)
);

-- Table: allergenAndpreference
CREATE TABLE allergenAndpreference (
    ingredientID int  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT allergenAndpreference_pk PRIMARY KEY (userID,ingredientID)
);

-- Table: diet
CREATE TABLE diet (
    dietID int  NOT NULL AUTO_INCREMENT,
    diet nvarchar(100)  NOT NULL,
    CONSTRAINT diet_pk PRIMARY KEY (dietID)
);

-- Table: feedback
CREATE TABLE feedback (
    feedbackID int  NOT NULL AUTO_INCREMENT,
    feedback nvarchar(10)  NOT NULL,
    userID int  NOT NULL,
    recipeID int  NOT NULL,
    CONSTRAINT feedback_pk PRIMARY KEY (feedbackID)
);

-- Table: goal
CREATE TABLE goal (
    goalID int  NOT NULL AUTO_INCREMENT,
    goal nvarchar(100)  NOT NULL,
    CONSTRAINT goal_pk PRIMARY KEY (goalID)
);

-- Table: ingredient
CREATE TABLE ingredient (
    ingredientID int  NOT NULL AUTO_INCREMENT,
    type nvarchar(100)  NOT NULL,
    ingredient nvarchar(255)  NOT NULL,
    image blob  NULL,
    CONSTRAINT ingredient_pk PRIMARY KEY (ingredientID)
);

-- Table: instruction
CREATE TABLE instruction (
    instructionID int  NOT NULL AUTO_INCREMENT,
    step nvarchar(500)  NOT NULL,
    recipeID int  NOT NULL,
    CONSTRAINT instruction_pk PRIMARY KEY (instructionID)
);

-- Table: nutritionalFacts
CREATE TABLE nutritionalFacts (
    nutritionalFactsID int  NOT NULL AUTO_INCREMENT,
    measure int  NOT NULL,
    proteins float(10,2)  NOT NULL,
    carbs float(10,2)  NOT NULL,
    fats float(10,2)  NOT NULL,
    ingredientID int  NOT NULL,
    CONSTRAINT nutritionalFacts_pk PRIMARY KEY (nutritionalFactsID)
);

-- Table: recipe
CREATE TABLE recipe (
    recipeID int  NOT NULL AUTO_INCREMENT,
    title nvarchar(100)  NOT NULL,
    description nvarchar(500)  NOT NULL,
    time int  NOT NULL,
    servings int  NOT NULL,
    image blob  NULL,
    goalID int  NOT NULL,
    userID int  NOT NULL,
    dietID int  NOT NULL,
    CONSTRAINT recipe_pk PRIMARY KEY (recipeID)
);

-- Table: recipeIngredient
CREATE TABLE recipeIngredient (
    recipeID int  NOT NULL,
    ingredientID int  NOT NULL,
    measurement int NOT NULL,
    CONSTRAINT recipeIngredient_pk PRIMARY KEY (recipeID,ingredientID)
);

-- Table: user
CREATE TABLE user (
    userID int  NOT NULL AUTO_INCREMENT,
    accountType nvarchar(100)  NOT NULL,
    firstName nvarchar(100)  NOT NULL,
    lastName nvarchar(100)  NOT NULL,
    username nvarchar(100)  NOT NULL,
    password nvarchar(100)  NOT NULL,
    email nvarchar(100)  NOT NULL,
    .
    phoneNumber bigint  NULL,
    CONSTRAINT user_pk PRIMARY KEY (userID)
);

-- Table: userGoals
CREATE TABLE userGoals (
    goalID int  NOT NULL,
    userID int  NOT NULL,
    CONSTRAINT userGoals_pk PRIMARY KEY (goalID,userID)
);

-- foreign keys
-- Reference: Allergen_User (table: allergenAndpreference)
ALTER TABLE allergenAndpreference ADD CONSTRAINT Allergen_User FOREIGN KEY Allergen_User (userID)
    REFERENCES user (userID);

-- Reference: favorite_recipe (table: feedback)
ALTER TABLE feedback ADD CONSTRAINT favorite_recipe FOREIGN KEY favorite_recipe (recipeID)
    REFERENCES recipe (recipeID);

-- Reference: favorite_user (table: feedback)
ALTER TABLE feedback ADD CONSTRAINT favorite_user FOREIGN KEY favorite_user (userID)
    REFERENCES user (userID);

-- Reference: ingredient_Allergen (table: allergenAndpreference)
ALTER TABLE allergenAndpreference ADD CONSTRAINT ingredient_Allergen FOREIGN KEY ingredient_Allergen (ingredientID)
    REFERENCES ingredient (ingredientID);

-- Reference: ingredient_recipeIndgredient (table: recipeIngredient)
ALTER TABLE recipeIngredient ADD CONSTRAINT ingredient_recipeIndgredient FOREIGN KEY ingredient_recipeIndgredient (ingredientID)
    REFERENCES ingredient (ingredientID);

-- Reference: instruction_recipe (table: instruction)
ALTER TABLE instruction ADD CONSTRAINT instruction_recipe FOREIGN KEY instruction_recipe (recipeID)
    REFERENCES recipe (recipeID);

-- Reference: nutritionalFacts_ingredient (table: nutritionalFacts)
ALTER TABLE nutritionalFacts ADD CONSTRAINT nutritionalFacts_ingredient FOREIGN KEY nutritionalFacts_ingredient (ingredientID)
    REFERENCES ingredient (ingredientID);

-- Reference: recipeIndgredient_recipe (table: recipeIngredient)
ALTER TABLE recipeIngredient ADD CONSTRAINT recipeIndgredient_recipe FOREIGN KEY recipeIndgredient_recipe (recipeID)
    REFERENCES recipe (recipeID);

-- Reference: recipe_diet (table: recipe)
ALTER TABLE recipe ADD CONSTRAINT recipe_diet FOREIGN KEY recipe_diet (dietID)
    REFERENCES diet (dietID);

-- Reference: recipe_goal (table: recipe)
ALTER TABLE recipe ADD CONSTRAINT recipe_goal FOREIGN KEY recipe_goal (goalID)
    REFERENCES goal (goalID);

-- Reference: recipe_user (table: recipe)
ALTER TABLE recipe ADD CONSTRAINT recipe_user FOREIGN KEY recipe_user (userID)
    REFERENCES user (userID);

-- Reference: userGoals_goal (table: userGoals)
ALTER TABLE userGoals ADD CONSTRAINT userGoals_goal FOREIGN KEY userGoals_goal (goalID)
    REFERENCES goal (goalID);

-- Reference: userGoals_user (table: userGoals)
ALTER TABLE userGoals ADD CONSTRAINT userGoals_user FOREIGN KEY userGoals_user (userID)
    REFERENCES user (userID);

-- End of file.

