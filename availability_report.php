<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="tableandbuttons.css">
    <title =>Справка за съставките</title>
</head>
<body>
<?php
include "manu.php";
include "config.php";

// Зареждаме всички съставки
$query = "SELECT id, name, quantity FROM ingredients";
$result = mysqli_query($dbConn, $query);
$ingredients = [];

while ($row = mysqli_fetch_assoc($result)) {
    $ingredients[$row['id']] = [
        'name' => $row['name'],
        'quantity' => $row['quantity'],
    ];
}

// Взимаме информация за съставките, използвани в поръчките
$query = "
    SELECT 
        i.id AS ingredient_id, 
        i.name, 
        i.quantity AS total_quantity, 
        SUM(od.quantity * di.quantity) AS total_used
    FROM 
        ingredients i
    JOIN 
        dish_ingredients di ON i.id = di.ingredient_id
    JOIN 
        order_dishes od ON di.dish_id = od.dish_id
    GROUP BY 
        i.id
";

$result = mysqli_query($dbConn, $query);

$needed_ingredients = [];

while ($row = mysqli_fetch_assoc($result)) {
    $ingredientId = $row['ingredient_id'];
    $needed_ingredients[$ingredientId] = [
        'total_used' => $row['total_used'],
        'remaining' => isset($ingredients[$ingredientId]) ? $ingredients[$ingredientId]['quantity'] - $row['total_used'] : 0,
    ];
}

// Показваме резултатите
echo "<h2 style=\"text-align: center;\">Справка за съставките</h2>";
echo "<table border='1'>";
echo "<tr><th>Съставка</th><th>Необходими количества</th><th>Оставащи количества</th><th>Необходими за покупка</th></tr>";

foreach ($needed_ingredients as $ingredientId => $data) {
    $ingredientName = $ingredients[$ingredientId]['name'];
    $totalUsed = $data['total_used'];
    $remaining = $data['remaining'];
    $needed = $remaining < 0 ? abs($remaining) : 0; // Ако количеството е отрицателно, значи трябва да купим

    echo "<tr>
            <td>$ingredientName</td>
            <td>$totalUsed</td>
            <td>" . ($remaining >= 0 ? $remaining : 0) . "</td>
            <td>$needed</td>
          </tr>";
}

echo "</table>";

mysqli_close($dbConn);
?>
</body>
</html>

