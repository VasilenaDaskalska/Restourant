<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $unit_type_id = $_POST['unit_type_id'];

    if (!$name || !$quantity || !$unit_type_id) {
        echo "Всички полета са задължителни!";
        exit;
    }

    $query = "INSERT INTO ingredients (name, quantity, unit_type_id) VALUES (?, ?, ?)";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("sdi", $name, $quantity, $unit_type_id);

    if ($stmt->execute()) {
        echo "Съставката е успешно добавена!";
    } else {
        echo "Грешка при добавянето: " . $stmt->error;
    }

    $stmt->close();
    $dbConn->close();
}
?>
