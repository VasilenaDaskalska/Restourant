<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="tableandbuttons.css">
    <title>Списък с ястия</title>
</head>
<body>
<style type="text/css">
    label[for="formIngredients"] {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }

    #formIngredients {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        height: auto;
        min-height: 100px;
        overflow-y: auto;
    }

    #formIngredients option {
        padding: 10px;
    }

    #quantitiesContainer div {
        margin: 10px 0;
    }

    #quantitiesContainer input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>

<?php
include "manu.php";
?>

<h2 style="text-align: center;">Списък с ястия</h2>
<button class="button_edit" onclick="openAddForm()">Добави ястие</button>

<?php
include "config.php";

echo "<div class=\"table_container\">";
echo "<table>";
echo "<tr><th>Номер</th><th>Име</th><th>Описание</th><th>Съставки и количества</th><th>Действия</th></tr>";

$result = mysqli_query($dbConn, "
    SELECT d.id, d.name, d.description, 
           GROUP_CONCAT(CONCAT(i.name, ' (', di.quantity, ' ', u.short_name, ')') SEPARATOR ', ') AS ingredients,
           GROUP_CONCAT(CONCAT(di.ingredient_id, '_', di.quantity) SEPARATOR ', ') AS ingredient_quantities
    FROM dishes d
    LEFT JOIN dish_ingredients di ON d.id = di.dish_id
    LEFT JOIN ingredients i ON di.ingredient_id = i.id
    LEFT JOIN unit_type u ON i.unit_type_id = u.id
    GROUP BY d.id
");

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['description']}</td>
                <td>{$row['ingredients']}</td>
                <td>
                    <button class=\"button_edit\" onclick=\"editRow({$row['id']}, '{$row['name']}', '{$row['description']}', '{$row['ingredients']}', '{$row['ingredient_quantities']}')\">Редактирай</button>
                      <button class=\"button_edit\" onclick=\"deleteDish({$row['id']})\">Изтрий</button>
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

<div id="overlay"></div>
<div id="formContainer" class="form-container">
    <h2 id="formTitle">Добавяне на ново ястие</h2>
    <form id="form" onsubmit="return handleFormSubmit();">
        <input type="hidden" id="formId">
        <label for="name">Име на ястие:</label>
        <input type="text" id="formName" required>
        <label for="description">Описание:</label>
        <input type="text" id="formDescription" required>
        <label for="ingredients">Съставки:</label>
        <select id="formIngredients" multiple required>
            <?php
            include "config.php";
            $ingredients = mysqli_query($dbConn, "SELECT id, name FROM ingredients");
            while ($ingredient = mysqli_fetch_assoc($ingredients)) {
                echo "<option value=\"{$ingredient['id']}\" data-name=\"{$ingredient['name']}\">{$ingredient['name']}</option>";
            }
            mysqli_close($dbConn);
            ?>
        </select>

        <div id="quantitiesContainer"></div>

        <button type="submit" class="button_edit" id="formSubmitButton">Запази</button>
        <button type="button" class="button_edit" onclick="closeForm()">Отказ</button>
    </form>
</div>

<script>
    document.getElementById('formIngredients').addEventListener('change', function() {
        const selectedOptions = Array.from(this.selectedOptions);
        const quantitiesContainer = document.getElementById('quantitiesContainer');
        quantitiesContainer.innerHTML = ''; // Изчистваме предишни полета

        selectedOptions.forEach(option => {
            const ingredientId = option.value;
            const ingredientName = option.getAttribute('data-name');

            // Създаване на поле за количество
            const div = document.createElement('div');
            div.innerHTML = `
                <label for="quantity_${ingredientId}">${ingredientName} (количество):</label>
                <input type="number" id="quantity_${ingredientId}" name="quantity_${ingredientId}" required>
            `;
            quantitiesContainer.appendChild(div);
        });
    });

    // Отваряне на формата за добавяне
    function openAddForm() {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Добавяне на ново ястие';
        document.getElementById('formId').value = '';
        document.getElementById('formName').value = '';
        document.getElementById('formDescription').value = '';
        document.getElementById('formIngredients').selectedIndex = -1;
        document.getElementById('quantitiesContainer').innerHTML = ''; // Изчистване на полетата за количество
    }

    // Редактиране на съществуващо ястие
    function editRow(id, name, description, ingredients, ingredientQuantities) {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Редактирай ястие';
        document.getElementById('formId').value = id;
        document.getElementById('formName').value = name;
        document.getElementById('formDescription').value = description;

        const ingredientOptions = document.getElementById('formIngredients').options;
        const selectedIngredients = ingredients.split(', ');
        const quantities = ingredientQuantities.split(', ');

        for (let i = 0; i < ingredientOptions.length; i++) {
            ingredientOptions[i].selected = selectedIngredients.includes(ingredientOptions[i].text);
        }

        // Генериране на полета за количествата на съществуващите съставки
        const quantitiesContainer = document.getElementById('quantitiesContainer');
        quantitiesContainer.innerHTML = ''; // Изчистваме старите полета
        selectedIngredients.forEach((ingredient, index) => {
            const ingredientId = Array.from(ingredientOptions).find(option => option.text === ingredient).value;
            const quantity = quantities[index].split('_')[1];
            const div = document.createElement('div');
            div.innerHTML = `
                <label for="quantity_${ingredientId}">${ingredient} (количество):</label>
                <input type="number" id="quantity_${ingredientId}" name="quantity_${ingredientId}" value="${quantity}" required>
            `;
            quantitiesContainer.appendChild(div);
        });
    }

    // Затваряне на формата
    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    function deleteDish(id) 
    {
    if (confirm("Сигурни ли сте, че искате да изтриете това ястие?")) 
    {
        fetch('delete_dish.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${id}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Показваме резултата
            location.reload(); // Презареждаме страницата
        })
        .catch(error => console.error('Грешка:', error));
    	}
	}

    // Обработка на изпращането на формата
    function handleFormSubmit() {
        const id = document.getElementById('formId').value;
        const name = document.getElementById('formName').value;
        const description = document.getElementById('formDescription').value;
        const ingredients = Array.from(document.getElementById('formIngredients').selectedOptions).map(opt => opt.value);

        // Проверка дали са избрани съставки
        if (ingredients.length === 0) {
            alert('Моля, изберете поне една съставка.');
            return false;
        }

        // Събиране на количествата
        const quantities = {};
        ingredients.forEach(ingredientId => {
            const quantity = document.getElementById(`quantity_${ingredientId}`).value;
            if (!quantity) {
                alert('Моля, попълнете количествата за всички съставки.');
                return false;
            }
            quantities[ingredientId] = quantity;
        });

        const url = id ? 'update_dish.php' : 'add_dish.php';
        const data = new URLSearchParams();
        data.append('id', id);
        data.append('name', name);
        data.append('description', description);
        data.append('ingredients', ingredients.join(','));
        data.append('quantities', JSON.stringify(quantities)); // Тук добавяме количествата като JSON

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data.toString()
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
