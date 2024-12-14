<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $unit_type_id = $_POST['unit_type_id'];

    if (!$id || !$name || !$quantity || !$unit_type_id) {
        echo "Всички полета са задължителни!";
        exit;
    }

    $query = "UPDATE ingredients SET name = ?, quantity = ?, unit_type_id = ? WHERE id = ?";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("sdii", $name, $quantity, $unit_type_id, $id);

    if ($stmt->execute()) {
        echo "Съставката е успешно обновена!";
    } else {
        echo "Грешка при обновяването: " . $stmt->error;
    }

    $stmt->close();
    $dbConn->close();
}
?>
