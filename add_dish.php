<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаване на данни от POST заявката
    $name = $_POST['name'];
    $description = $_POST['description'];
    $ingredients = explode(',', $_POST['ingredients']); // Съставки като масив
    $quantities = json_decode($_POST['quantities'], true); // Декодиране на JSON със стойностите на количествата

    // Вмъкване на новото ястие в базата данни
    $query = "INSERT INTO dishes (name, description) VALUES (?, ?)";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("ss", $name, $description);
    if ($stmt->execute()) {
        $dishId = $stmt->insert_id; // Вземаме ID-то на новото ястие

        // Добавяне на съставките и количествата
        foreach ($ingredients as $ingredientId) {
            if (isset($quantities[$ingredientId])) {
                $quantity = $quantities[$ingredientId];
                // Вмъкване на съставките и количествата
                $query = "INSERT INTO dish_ingredients (dish_id, ingredient_id, quantity) VALUES (?, ?, ?)";
                $stmt = $dbConn->prepare($query);
                $stmt->bind_param("iis", $dishId, $ingredientId, $quantity);
                $stmt->execute();
            }
        }

        echo "Ястието беше успешно добавено!";
    } else {
        echo "Грешка при добавяне на ястието.";
    }
    exit;
}
?>
<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаване на данни от POST заявката
    $name = $_POST['name'];
    $description = $_POST['description'];
    $ingredients = explode(',', $_POST['ingredients']); // Съставки като масив
    $quantities = json_decode($_POST['quantities'], true); // Декодиране на JSON със стойностите на количествата

    // Вмъкване на новото ястие в базата данни
    $query = "INSERT INTO dishes (name, description) VALUES (?, ?)";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("ss", $name, $description);
    if ($stmt->execute()) {
        $dishId = $stmt->insert_id; // Вземаме ID-то на новото ястие

        // Добавяне на съставките и количествата
        foreach ($ingredients as $ingredientId) {
            if (isset($quantities[$ingredientId])) {
                $quantity = $quantities[$ingredientId];
                // Вмъкване на съставките и количествата
                $query = "INSERT INTO dish_ingredients (dish_id, ingredient_id, quantity) VALUES (?, ?, ?)";
                $stmt = $dbConn->prepare($query);
                $stmt->bind_param("iis", $dishId, $ingredientId, $quantity);
                $stmt->execute();
            }
        }

        echo "Ястието беше успешно добавено!";
    } else {
        echo "Грешка при добавяне на ястието.";
    }
    exit;
}
?>
