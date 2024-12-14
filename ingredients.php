<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="tableandbuttons.css">
    <title>Списък със съставки</title>
</head>
<body>
<style type="text/css">
  
        /* Стили за формата */
        .form-container {
            display: none; /* Скрито по подразбиране */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.25);
            width: 40%;
        }

        .form-container h2 {
            margin-top: 0;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Допълнителни стилове за селекта */
        select {
            appearance: none; /* За да премахнете стандартния стил на браузъра */
            background: white url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="gray" d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center; /* Стил на стрелката */
            background-size: 12px; /* Размер на стрелката */
            padding-right: 30px; /* Празно пространство за стрелката */
        } 
</style>
<?php
include "manu.php";
?>
<h2 style="text-align: center;">Списък със съставки</h2>
<!-- Бутон за добавяне -->
<button class="button_edit" onclick="openAddForm()">Добави съставка</button>
<!-- Таблицата -->
<?php
include "config.php";

echo "<div class=\"table_container\">";
echo "<table>";
echo "<tr><th>Номер</th><th>Име на съставка</th><th>Количество</th><th>Мерна единица</th><th>Действия</th></tr>";

$result = mysqli_query($dbConn, "SELECT i.id, i.name, i.quantity, u.name AS unit_name 
                                FROM ingredients i 
                                JOIN unit_type u ON i.unit_type_id = u.id");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['unit_name']}</td>
                <td>
                    <button class=\"button_edit\" onclick=\"editRow({$row['id']}, '{$row['name']}', '{$row['quantity']}', '{$row['unit_name']}')\">Редактирай</button>
                     <button class=\"button_edit\" onclick=\"deleteIngredient({$row['id']})\">Изтрий</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>Няма намерени записи.</td></tr>";
}
echo "</table>";
echo "</div>";
mysqli_close($dbConn);
?>

<!-- Формата за добавяне/редакция -->
<div id="overlay"></div>
<div id="formContainer" class="form-container">
    <h2 id="formTitle">Добавяне на нова съставка</h2>
    <form id="form" onsubmit="return handleFormSubmit();">
        <input type="hidden" id="formId">
        <label for="name">Име на съставка:</label>
        <input type="text" id="formName" required>
        <label for="quantity">Количество:</label>
        <input type="number" step="any" id="formQuantity" required>
        <br/>
        <label for="unit">Мерна единица:</label>
        <select id="formUnit" required>
            <?php
            include "config.php";
            $units = mysqli_query($dbConn, "SELECT id, name FROM unit_type");
            while ($unit = mysqli_fetch_assoc($units)) {
                echo "<option value=\"{$unit['id']}\">{$unit['name']}</option>";
            }
            mysqli_close($dbConn);
            ?>
        </select>
        <br/>
        <button type="submit" class="button_edit" id="formSubmitButton">Запази</button>
        <button type="button" class="button_edit" onclick="closeForm()">Отказ</button>
    </form>
</div>

<script>
    function openAddForm() {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Добавяне на нова съставка';
        document.getElementById('formId').value = '';
        document.getElementById('formName').value = '';
        document.getElementById('formQuantity').value = '';
        document.getElementById('formUnit').value = '';
    }

    function editRow(id, name, quantity, unitId) {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Редакция на съставка';
        document.getElementById('formId').value = id;
        document.getElementById('formName').value = name;
        document.getElementById('formQuantity').value = quantity;
        document.getElementById('formUnit').value = unitId;
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    function deleteIngredient(ingredientId) {
        if (!confirm("Сигурни ли сте, че искате да изтриете тази съставка?")) {
            return;
        }

        fetch('delete_ingredient.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id: ingredientId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Премахване на реда от таблицата
                const row = document.querySelector(`tr[data-id='${ingredientId}']`);
                if (row) row.remove();
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Грешка:", error));
    }

    function handleFormSubmit() {
        const id = document.getElementById('formId').value;
        const name = document.getElementById('formName').value;
        const quantity = document.getElementById('formQuantity').value;
        const unitId = document.getElementById('formUnit').value;

        const url = id ? 'update_ingredient.php' : 'add_ingredient.php';
        const data = id 
            ? `id=${id}&name=${name}&quantity=${quantity}&unit_type_id=${unitId}`
            : `name=${name}&quantity=${quantity}&unit_type_id=${unitId}`;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            closeForm();
            location.reload();
        })
        .catch(error => console.error('Error:', error));

        return false;
    }
</script>
</body>
</html>
