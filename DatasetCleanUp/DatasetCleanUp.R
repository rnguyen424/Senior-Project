install.packages("pacman")

pacman::p_load(tidyverse)

library(tidyverse)
library(dplyr)


# Read CSV files
food <- read.csv("food.csv", header = TRUE)
food_nutrient <- read.csv("food_nutrient.csv", header = TRUE)
food_category <- read.csv("food_category.csv", header = TRUE)
nutrient_conversion_factor <- read.csv("food_nutrient_conversion_factor.csv", header = TRUE)
calorie_conversion_factor <- read.csv("food_calorie_conversion_factor.csv", header = TRUE)
measure_unit <- read.csv("measure_unit.csv", header = TRUE)
food_portion <- read.csv("food_portion.csv", header = TRUE)
nutrient <- read.csv("nutrient.csv", header = TRUE)

# Print column names of each dataset to identify correct column names
print(colnames(food))
print(colnames(food_nutrient))
print(colnames(food_category))
print(colnames(food_nutrient_conversion_factor))
print(colnames(measure_unit))
print(colnames(food_portion))
print(colnames(nutrient))

filtered_food <- food %>%
  filter(data_type == "foundation_food") %>%
  select(-publication_date)

filtered_food_with_category <- merge(filtered_food, food_category, 
                                     by = "food_category_id", all.x = TRUE)

filtered_food_with_category <- filtered_food_with_category %>%
  select(fdc_id, data_type, description.x, description.y) %>%
  rename(food_category_description = description.y)

merged_factors <- merge(nutrient_conversion_factor, calorie_conversion_factor, 
                        by = "food_nutrient_conversion_factor_id", all.x = TRUE)


desired_nutrients <- nutrient %>%
  filter(nutrient_id %in% c(1003, 1004, 1005))


merged_data <- merge(food_nutrient, desired_nutrients, by.x = "nutrient_id", by.y = "nutrient_id", all.x = TRUE)


merged_data <- merged_data %>%
  mutate(nutrient_name = ifelse(nutrient_id == 1003, "Proteins",
                                ifelse(nutrient_id == 1004, "Fats",
                                       ifelse(nutrient_id == 1005, "Carbohydrates", NA))))

merged_data <-merged_data %>%
filter(nutrient_id %in% c(1003, 1004, 1005))


selected_data_nutrients <- merged_data %>%
  select(fdc_id, nutrient_name, amount) %>%
  rename(nutrient_amount = amount)


new_merged_data <- merge(filtered_food_with_category, selected_data_nutrients, by = "fdc_id", all.x = TRUE)


new_merged_data <- new_merged_data %>%
  spread(key = nutrient_name, value = nutrient_amount, fill = 0)


final_data <- new_merged_data %>%
  select(description.x, food_category_description, Proteins, Fats, Carbohydrates)

final_data <- final_data %>%
  mutate(Portion = 100)

filtered_final_data <- final_data %>%
  distinct(description.x, .keep_all = TRUE)

filtered_final_data <- filtered_final_data %>%
  rename(Description = description.x, 
         `Food Category` = food_category_description)

filtered_final_data <- filtered_final_data %>%
  select(Description, Portion, `Food Category`, Proteins, Fats, Carbohydrates)

write.csv(filtered_final_data, "final_data.csv", row.names = FALSE)
