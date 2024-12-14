<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $customer_name = $_POST['customer_name'];
    $dishes = json_decode($_POST['dishes']);
    $quantities = json_decode($_POST['quantities']);

    if (!$order_id || !$customer_name || empty($dishes) || empty($quantities)) {
        echo "Всички полета са задължителни!";
        exit;
    }

    $dbConn->autocommit(false);

    // Обновяване на името на клиента
    $query = "UPDATE orders SET customer_name = ? WHERE id = ?";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("si", $customer_name, $order_id);

    if (!$stmt->execute()) {
        $dbConn->rollback();
        echo "Грешка при обновяването на клиента: " . $stmt->error;
        exit;
    }

    // Изтриване на съществуващите ястия за поръчката
    $query = "DELETE FROM order_dishes WHERE order_id = ?";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("i", $order_id);

    if (!$stmt->execute()) {
        $dbConn->rollback();
        echo "Грешка при изтриването на старите ястия: " . $stmt->error;
        exit;
    }

    // Добавяне на новите ястия към поръчката
    $query = "INSERT INTO order_dishes (order_id, dish_id, quantity) VALUES (?, ?, ?)";
    $stmt = $dbConn->prepare($query);

    foreach ($dishes as $index => $dishId) {
        $quantity = $quantities[$index];
        $stmt->bind_param("iid", $order_id, $dishId, $quantity);

        if (!$stmt->execute()) {
            $dbConn->rollback();
            echo "Грешка при добавянето на ястията: " . $stmt->error;
            exit;
        }
    }

    $dbConn->commit();
    echo "Поръчката е успешно обновена!";
    
    $stmt->close();
    $dbConn->close();
}
?>
