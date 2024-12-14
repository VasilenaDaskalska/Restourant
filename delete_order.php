<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка дали е предоставено ID на поръчката
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $orderId = intval($_POST['id']);

        // Подготовка на заявката за изтриване
        $query = "DELETE FROM orders WHERE id = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bind_param("i", $orderId);

        if ($stmt->execute()) {
            echo "Поръчката и свързаните ястия бяха успешно изтрити.";
        } else {
            echo "Грешка при изтриване на поръчката: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Невалидно ID.";
    }
}

$dbConn->close();
?>
