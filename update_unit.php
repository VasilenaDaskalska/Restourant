<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $short_name = $_POST['short_name'];

    if (!$id || !$name || !$short_name) {
        echo "Всички полета са задължителни!";
        exit;
    }

    $query = "UPDATE unit_type SET name = ?, short_name = ? WHERE id = ?";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("ssi", $name, $short_name, $id);

    if ($stmt->execute()) {
        echo "Записът е успешно обновен!";
    } else {
        echo "Грешка при обновяването: " . $stmt->error;
    }

    $stmt->close();
    $dbConn->close();
}
?>