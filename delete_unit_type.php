<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        $query = "DELETE FROM unit_type WHERE id = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Успешно изтриване!";
        } else {
            echo "Грешка при изтриването.";
        }
    } else {
        echo "Невалидно ID.";
    }
}
?>
