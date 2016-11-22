# Wild Code School

## Slugify existing data 

+ place yourself in the 20160524.sql directory
+ mysql -u _username_ -p < 20160524.sql
+ place yourself back into FoodandYou directory
+ php app/console doctrine:schema:update
+ php app/console app:slugify:existantData