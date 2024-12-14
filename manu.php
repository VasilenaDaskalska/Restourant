<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="navbar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>

body {
  font-family: 'Arial', sans-serif;
  background: linear-gradient(to bottom right, #f9f9f9, #e8e8e8);
  margin: 0;
  padding: 0;
  min-height: 100vh;
}
</style>
</head>
<body>

<div class="navbar">
  <div class="dropdown">
    <button class="dropbtn">Ястия
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="dishes.php">Ястия</a>
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Съставки
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="ingredients.php">Съставки</a>
      <a href="availability_report.php">Отчет на съставки</a>
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Мерни единици
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="Measures.php">Мерни единици</a>
    </div>
  </div> 
   <div class="dropdown">
    <button class="dropbtn">Поръчки
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="orders.php">Поръчки</a>
    </div>
  </div> 
</div>

</body>
</html>
