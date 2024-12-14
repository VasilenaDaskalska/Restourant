<?php
$host= 'localhost'; 
$dbUser= 'root'; 
$dbPass= ''; 
$host= 'localhost'; 
$dbUser= 'root'; 
$dbPass= ''; 
if(!$dbConn=mysqli_connect($host, $dbUser, $dbPass)) {
die('Не може да се осъществи връзка със сървъра:');
}
echo "Връзката е успешна. <br>";
$sql = 'CREATE Database IF NOT EXISTS RestaurantDB';
if ($queryResource=mysqli_query($dbConn,$sql))
{
echo "Базата данни е създадена. <br>";
}
else
{
echo "Грешка при създаване на базата данни: " ;
}
include "config.php";

$dishes = "CREATE TABLE IF NOT EXISTS `dishes` (
`id` int(4) NOT NULL auto_increment,
`name` varchar(65) NOT NULL default '',
`description` varchar(65) NOT NULL default '',
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8";

$unit_type = "CREATE TABLE IF NOT EXISTS `unit_type` (
`id` int(4) NOT NULL auto_increment,
`name` varchar(65) NOT NULL default '',
`short_name` varchar(5) NOT NULL default '',
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8";

$ingredients = "CREATE TABLE IF NOT EXISTS `ingredients` (
`id` int(4) NOT NULL auto_increment,
`name` varchar(65) NOT NULL default '',
`quantity` float(10) NOT NULL,
`unit_type_id` int(3) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8";

$alterTableIngredients="ALTER TABLE ingredients ADD CONSTRAINT FOREIGN KEY (unit_type_id) REFERENCES unit_type(ID) ON DELETE  CASCADE ";

$dish_ingredients = "CREATE TABLE IF NOT EXISTS `dish_ingredients` (
`id` int(4) NOT NULL auto_increment,
`dish_id` int(4) NOT NULL,
`ingredient_id` int(4) NOT NULL,
`quantity` float(10) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8";

$alterTableDishIngredients1="ALTER TABLE dish_ingredients ADD CONSTRAINT FOREIGN KEY (dish_id) REFERENCES dishes(ID) ON DELETE  CASCADE ";

$alterTableDishIngredients2="ALTER TABLE dish_ingredients ADD CONSTRAINT FOREIGN KEY (ingredient_id) REFERENCES ingredients(ID) ON DELETE  CASCADE ";

$orders = "CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(255) NOT NULL,
    `order_date` DATE NOT NULL DEFAULT CURRENT_DATE
) ENGINE=INNODB DEFAULT CHARSET=utf8;";

$dish_orders = "CREATE TABLE IF NOT EXISTS `order_dishes` (
    `order_id` INT NOT NULL,
    `dish_id` INT NOT NULL,
    `quantity` FLOAT(10) NOT NULL,
    PRIMARY KEY (`order_id`, `dish_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dish_id`) REFERENCES `dishes`(`id`) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;";

$dishesResult = mysqli_query($dbConn,$dishes);
$unit_typeResult = mysqli_query($dbConn, $unit_type);
$ingredientsesult = mysqli_query($dbConn, $ingredients);
$result=mysqli_query($dbConn,$alterTableIngredients);
$dish_ingredientsResult = mysqli_query($dbConn, $dish_ingredients);
$result1=mysqli_query($dbConn,$alterTableDishIngredients1);
$result2=mysqli_query($dbConn,$alterTableDishIngredients2);
$ordersResult = mysqli_query($dbConn, $orders);
$dish_ordersResults = mysqli_query($dbConn,$dish_orders);

if(!$dishesResult || !$unit_typeResult || !$ingredientsesult || !$dish_ingredientsResult || !$orders ||!$dish_orders ||!$result || !$result1 || !$result2)
{
	die('Грешка при създаване на таблицата: ' . mysql_error());
}
?>