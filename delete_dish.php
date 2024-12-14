<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка дали е предоставено ID на ястието
    if (isset($_POST['id']) && is_numeric($_POST['id'])) 
    {
        $dishId = intval($_POST['id']);

        if ($dishId) 
        {
        // Проверка дали съставката се използва в други таблици
        $checkQuery = "SELECT COUNT(*) as count FROM order_dishes WHERE dish_id = ?";
        $stmt = $dbConn->prepare($checkQuery);
        $stmt->bind_param("i", $dishId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

            if ($result['count'] > 0) 
            {
                echo "Ястието не може да бъде изтрито, защото е поръчано.";
            }else
            {
                // Подготовка на заявката за изтриване
                $query = "DELETE FROM dishes WHERE id = ?";
                $stmt = $dbConn->prepare($query);
                $stmt->bind_param("i", $dishId);

                if ($stmt->execute()) {
                    echo "Ястието и свързаните съставки бяха успешно изтрити.";
                } else {
                    echo "Грешка при изтриване на ястието: " . $stmt->error;
                }

                 $stmt->close();
                }
    } else {
        echo "Невалидно ID.";
    }
}
}

$dbConn->close();
?>
