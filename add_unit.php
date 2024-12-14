<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $short_name = $_POST['short_name'];

    if (!$name || !$short_name) {
        echo "Всички полета са задължителни!";
        exit;
    }

    $query = "INSERT INTO unit_type (name, short_name) VALUES (?, ?)";
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("ss", $name, $short_name);

    if ($stmt->execute()) {
        echo "Новата мерна единица е успешно добавена!";
    } else {
        echo "Грешка при добавянето: " . $stmt->error;
    }

    $stmt->close();
    $dbConn->close();
}