<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="tableandbuttons.css">
    <title>Поръчки</title>
</head>
<body>
<style type="text/css">
  
#formDishes {
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

#formDishes option {
    padding: 10px; 
}

#formDishes:focus {
    border-color: #007BFF; 
    outline: none;
}

/* Стил за селекта при невалиден статус */
#formDishes:invalid {
    border-color: red; /* Червена граница при невалиден статус */
}
</style>
<?php
include "manu.php";
?>
<h2 style="text-align: center;">Списък с поръчки</h2>
<button class="button_edit" onclick="openAddForm()">Добави поръчка</button>

<?php
include "config.php";

echo "<div class=\"table_container\">";
echo "<table>";
echo "<tr><th>Номер</th><th>Клиент</th><th>Дата</th><th>Ястия</th><th>Действия</th></tr>";

$query = "SELECT o.id, o.customer_name, o.order_date, 
                 GROUP_CONCAT(CONCAT(d.name, ' (', od.quantity, ')') SEPARATOR ', ') AS dishes
          FROM orders o
          LEFT JOIN order_dishes od ON o.id = od.order_id
          LEFT JOIN dishes d ON od.dish_id = d.id
          GROUP BY o.id";

$result = mysqli_query($dbConn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['order_date']}</td>
                <td>{$row['dishes']}</td>
                <td>
                    <button class=\"button_edit\" onclick=\"editRow({$row['id']}, '{$row['customer_name']}', '{$row['order_date']}', '{$row['dishes']}')\">Редактирай</button>
                    <button class=\"button_edit\" onclick=\"deleteOrder({$row['id']})\">Изтрий</button>
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
    <h2 id="formTitle">Добавяне на нова поръчка</h2>
    <form id="form" onsubmit="return handleFormSubmit();">
        <input type="hidden" id="formId">
        <label for="customerName">Име на клиент:</label>
        <input type="text" id="formCustomerName" required>
        <label for="dishes">Ястия:</label>
        <select id="formDishes" multiple required>
            <?php
            include "config.php";
            $dishes = mysqli_query($dbConn, "SELECT id, name FROM dishes");
            while ($dish = mysqli_fetch_assoc($dishes)) {
                echo "<option value=\"{$dish['id']}\">{$dish['name']}</option>";
            }
            mysqli_close($dbConn);
            ?>
        </select>
        <label for="quantities">Количество:</label>
        <input type="text" id="formQuantities" placeholder="Пример: 2,1,3 (за съответните ястия)" required>
        <button type="submit" class="button_edit" id="formSubmitButton">Запази</button>
        <button type="button" class="button_edit" onclick="closeForm()">Отказ</button>
    </form>
</div>

<script>
    // Отваряне на формата за добавяне на нова поръчка
    function openAddForm() {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Добавяне на нова поръчка';
        document.getElementById('formId').value = '';
        document.getElementById('formCustomerName').value = '';
        document.getElementById('formDishes').selectedIndex = -1;
        document.getElementById('formQuantities').value = '';
    }

    // Отваряне на формата за редактиране
    function editRow(orderId, customerName, orderDate, dishes) {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Редакция на поръчка';
        document.getElementById('formId').value = orderId;
        document.getElementById('formCustomerName').value = customerName;
        document.getElementById('formSubmitButton').textContent = 'Обнови';

        // Предпълване на ястията (текстово)
        const dishList = dishes.split(', ');
        const quantities = dishList.map(dish => dish.match(/\\((.*?)\\)/)[1]);
        document.getElementById('formQuantities').value = quantities.join(',');

        // Избиране на ястия
        Array.from(document.getElementById('formDishes').options).forEach(opt => {
            opt.selected = dishList.some(dish => dish.startsWith(opt.textContent));
        });
    }

    function deleteOrder(id) {
        if (confirm("Сигурни ли сте, че искате да изтриете тази поръчка?")) {
            fetch('delete_order.php', {
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

    // Обработка на формуляра за поръчка
    function handleFormSubmit() {
        const orderId = document.getElementById('formId').value;
        const customerName = document.getElementById('formCustomerName').value;
        const dishIds = Array.from(document.getElementById('formDishes').selectedOptions).map(opt => opt.value);
        const quantities = document.getElementById('formQuantities').value.split(',');

        if (dishIds.length !== quantities.length) {
            alert('Броят на ястията и количествата трябва да съвпадат!');
            return false;
        }

        const data = new URLSearchParams();
        data.append('order_id', orderId);
        data.append('customer_name', customerName);
        data.append('dishes', JSON.stringify(dishIds));
        data.append('quantities', JSON.stringify(quantities));

        const url = orderId ? 'update_order.php' : 'add_order.php';

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

    // Затваряне на формуляра
    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }
</script>

</body>
</html>
