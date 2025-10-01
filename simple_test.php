<?php
// Самый простой тест для проверки работы PHP
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Простой тест PHP</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Простой тест PHP</h1>
    <p>Если вы видите эту страницу, PHP работает корректно.</p>
    <p>Текущее время: <?php echo date('Y-m-d H:i:s'); ?></p>
    <p>Версия PHP: <?php echo phpversion(); ?></p>
    <p>Сервер: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
    
    <h2>Проверка функций</h2>
    <p>Функция date(): <?php echo date('Y-m-d H:i:s'); ?></p>
    <p>Функция phpversion(): <?php echo phpversion(); ?></p>
    
    <h2>Проверка переменных сервера</h2>
    <p>REQUEST_METHOD: <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
    <p>SCRIPT_NAME: <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
    <p>PHP_SELF: <?php echo $_SERVER['PHP_SELF']; ?></p>
    
    <h2>Ссылки</h2>
    <ul>
        <li><a href="diagnostic.php">Полная диагностика</a></li>
        <li><a href="test_basic.php">Базовый тест</a></li>
        <li><a href="test_db_connection.php">Тест базы данных</a></li>
        <li><a href="index.php">Главная страница</a></li>
    </ul>
</body>
</html>