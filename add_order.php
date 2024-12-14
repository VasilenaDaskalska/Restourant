<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $dishes = json_decode($_POST['dishes']);
    $quantities = json_decode($_POST['quantities']);

    if (!$customer_name || empty($dishes) || empty($quantities)) {
        echo "Всички полета са задължителни!";
        exit;
    }

    $dbConn->autocommit(false);

    $query = "INSERT INTO orders (customer_name, order_date) VALUES (?, CURDATE())";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("s", $customer_name);

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;

        $query = "INSERT INTO order_dishes (order_id, dish_id, quantity) VALUES (?, ?, ?)";
        $stmt = $dbConn->prepare($query);

        foreach ($dishes as $index => $dishId) {
            $quantity = $quantities[$index];
            $stmt->bind_param("iid", $orderId, $dishId, $quantity);

            if (!$stmt->execute()) {
                $dbConn->rollback();
                echo "Грешка при добавянето: " . $stmt->error;
                exit;
            }
        }

        $dbConn->commit();
        echo "Поръчката е успешно добавена!";
    } else {
        echo "Грешка при добавянето на поръчката: " . $stmt->error;
    }

    $stmt->close();
    $dbConn->close();
}
?>
