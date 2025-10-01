<!DOCTYPE html>
<html>
<head>
    <title>Тест HTML</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Тест HTML страницы</h1>
    <p>Это статическая HTML страница для проверки работы сервера.</p>
    <p>Если вы видите эту страницу, веб-сервер работает.</p>
    <p>Текущее время будет показано ниже, если PHP работает:</p>
    <?php
    echo "<p><strong>Текущее время (PHP): " . date('Y-m-d H:i:s') . "</strong></p>";
    echo "<p><strong>Версия PHP: " . phpversion() . "</strong></p>";
    ?>
    <hr>
    <p><a href="text_test.php">Текстовый тест</a></p>
    <p><a href="minimal_test.php">Минимальный тест</a></p>
    <p><a href="function_test.php">Тест функций</a></p>
</body>
</html>