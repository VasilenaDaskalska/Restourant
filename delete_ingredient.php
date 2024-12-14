<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $ingredientId = isset($_POST['id']) ? intval($_POST['id']) : null;

    if ($ingredientId) {
        // Проверка дали съставката се използва в други таблици
        $checkQuery = "SELECT COUNT(*) as count FROM dish_ingredients WHERE ingredient_id = ?";
        $stmt = $dbConn->prepare($checkQuery);
        $stmt->bind_param("i", $ingredientId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result['count'] > 0) {
            echo json_encode(["success" => false, "message" => "Съставката не може да бъде изтрита, защото се използва в рецепти."]);
        } else {
            // Изтриване на съставката
            $deleteQuery = "DELETE FROM ingredients WHERE id = ?";
            $stmt = $dbConn->prepare($deleteQuery);
            $stmt->bind_param("i", $ingredientId);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Съставката беше успешно изтрита."]);
            } else {
                echo json_encode(["success" => false, "message" => "Грешка при изтриване на съставката."]);
            }

            $stmt->close();
        }
    } else {
        echo json_encode(["success" => false, "message" => "Невалидно ID."]);
    }

    $dbConn->close();
}
?>
