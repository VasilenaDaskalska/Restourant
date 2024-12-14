<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаване на данни от POST заявката
    $id = $_POST['id'];  // ID на ястието
    $name = $_POST['name'];
    $description = $_POST['description'];
    $ingredients = explode(',', $_POST['ingredients']); // Съставки като масив
    $quantities = json_decode($_POST['quantities'], true); // Декодиране на JSON със стойностите на количествата

    // Актуализиране на съществуващото ястие
    $query = "UPDATE dishes SET name = ?, description = ? WHERE id = ?";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("ssi", $name, $description, $id);
    if ($stmt->execute()) {
        // Премахваме старите съставки за това ястие
        $query = "DELETE FROM dish_ingredients WHERE dish_id = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Добавяне на новите съставки и количества
        foreach ($ingredients as $ingredientId) {
            if (isset($quantities[$ingredientId])) {
                $quantity = $quantities[$ingredientId];
                // Вмъкване на съставките и количествата
                $query = "INSERT INTO dish_ingredients (dish_id, ingredient_id, quantity) VALUES (?, ?, ?)";
                $stmt = $dbConn->prepare($query);
                $stmt->bind_param("iis", $id, $ingredientId, $quantity);
                $stmt->execute();
            }
        }

        echo "Ястието беше успешно редактирано!";
    } else {
        echo "Грешка при редактиране на ястието.";
    }
    exit;
}
?>
<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаване на данни от POST заявката
    $id = $_POST['id'];  // ID на ястието
    $name = $_POST['name'];
    $description = $_POST['description'];
    $ingredients = explode(',', $_POST['ingredients']); // Съставки като масив
    $quantities = json_decode($_POST['quantities'], true); // Декодиране на JSON със стойностите на количествата

    // Актуализиране на съществуващото ястие
    $query = "UPDATE dishes SET name = ?, description = ? WHERE id = ?";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("ssi", $name, $description, $id);
    if ($stmt->execute()) {
        // Премахваме старите съставки за това ястие
        $query = "DELETE FROM dish_ingredients WHERE dish_id = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Добавяне на новите съставки и количества
        foreach ($ingredients as $ingredientId) {
            if (isset($quantities[$ingredientId])) {
                $quantity = $quantities[$ingredientId];
                // Вмъкване на съставките и количествата
                $query = "INSERT INTO dish_ingredients (dish_id, ingredient_id, quantity) VALUES (?, ?, ?)";
                $stmt = $dbConn->prepare($query);
                $stmt->bind_param("iis", $id, $ingredientId, $quantity);
                $stmt->execute();
            }
        }

        echo "Ястието беше успешно редактирано!";
    } else {
        echo "Грешка при редактиране на ястието.";
    }
    exit;
}
?>
