<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="tableandbuttons.css">
    <title>Списък с мерни единици</title>
</head>
<body>

<?php
include "manu.php";
?>
<h2 style="text-align: center;">Списък с мерни единици</h2>
<!-- Бутон за добавяне -->
<button class="button_edit" onclick="openAddForm()">Добави мерна единица</button>
<!-- Таблицата -->
<?php
include "config.php";

echo "<div class=\"table_container\">";
echo "<table>";
echo "<tr><th>Номер</th><th>Име на мерна единица</th><th>Съкратено име</th><th>Действия</th></tr>";

$result = mysqli_query($dbConn, "SELECT * FROM unit_type");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['short_name']}</td>
                <td>
                    <button class=\"button_edit\" onclick=\"editRow({$row['id']}, '{$row['name']}', '{$row['short_name']}')\">Редактирай</button>
                    <button class=\"button_edit\" onclick=\"deleteUnitType({$row['id']})\">Изтрий</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>Няма намерени записи.</td></tr>";
}
echo "</table>";
echo "</div>";
mysqli_close($dbConn);
?>



<!-- Формата за добавяне/редакция -->
<div id="overlay"></div>
<div id="formContainer" class="form-container">
    <h2 id="formTitle">Добавяне на нова мерна единица</h2>
    <form id="form" onsubmit="return handleFormSubmit();">
        <input type="hidden" id="formId">
        <label for="name">Име на мерна единица:</label>
        <input type="text" id="formName" required>
        <label for="short_name">Съкратено име:</label>
        <input type="text" id="formShortName" required>
        <button type="submit" class="button_edit" id="formSubmitButton">Запази</button>
        <button type="button" class="button_edit" onclick="closeForm()">Отказ</button>
    </form>
</div>

<script>
    // Отваряне на формата за добавяне
    function openAddForm() {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Добавяне на нова мерна единица';
        document.getElementById('formId').value = '';
        document.getElementById('formName').value = '';
        document.getElementById('formShortName').value = '';
    }

    // Отваряне на формата за редакция
    function editRow(id, name, shortName) {
        document.getElementById('formContainer').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('formTitle').textContent = 'Редакция на мерна единица';
        document.getElementById('formId').value = id;
        document.getElementById('formName').value = name;
        document.getElementById('formShortName').value = shortName;
    }

    // Затваряне на формата
    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    function deleteUnitType(id) {
        if (confirm("Сигурни ли сте, че искате да изтриете този запис?")) {
            fetch('delete_unit_type.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id: id }).toString()
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Показване на резултат
                location.reload(); // Презареждане на страницата
            })
            .catch(error => {
                console.error('Грешка:', error);
                alert('Грешка при изтриването.');
            });
        }
    }

    // Обработка на формата
    function handleFormSubmit() {
        const id = document.getElementById('formId').value;
        const name = document.getElementById('formName').value;
        const shortName = document.getElementById('formShortName').value;

        const url = id ? 'update_unit.php' : 'add_unit.php'; // Проверка дали е добавяне или редакция
        const data = id ? `id=${id}&name=${name}&short_name=${shortName}` : `name=${name}&short_name=${shortName}`;

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

        return false; // Предотвратява презареждане на формата
    }
</script>
</body>
</html>
