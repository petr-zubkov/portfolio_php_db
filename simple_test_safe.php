<?php
// Самый простой тест для проверки работы PHP (безопасная версия)
header('Content-Type: text/html; charset=utf-8');

// Проверяем базовые функции
$php_working = true;
$errors = [];

try {
    $date_test = date('Y-m-d H:i:s');
    $version_test = phpversion();
    $server_test = $_SERVER['SERVER_SOFTWARE'] ?? 'неизвестно';
    $method_test = $_SERVER['REQUEST_METHOD'] ?? 'неизвестно';
} catch (Exception $e) {
    $php_working = false;
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Простой тест PHP</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Простой тест PHP</h1>
    
    <?php if ($php_working): ?>
        <p class="success">✓ PHP работает корректно.</p>
        <p><strong>Текущее время:</strong> <?php echo htmlspecialchars($date_test); ?></p>
        <p><strong>Версия PHP:</strong> <?php echo htmlspecialchars($version_test); ?></p>
        <p><strong>Сервер:</strong> <?php echo htmlspecialchars($server_test); ?></p>
        <p><strong>Метод запроса:</strong> <?php echo htmlspecialchars($method_test); ?></p>
    <?php else: ?>
        <p class="error">✗ Обнаружены ошибки в работе PHP:</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li class="error"><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <h2>Проверка функций</h2>
    <?php
    $functions_to_test = [
        'date' => date('Y-m-d H:i:s'),
        'phpversion' => phpversion(),
        'time' => time(),
        'htmlspecialchars' => htmlspecialchars('test')
    ];
    
    foreach ($functions_to_test as $func_name => $result): ?>
        <p><strong>Функция <?php echo htmlspecialchars($func_name); ?>():</strong> 
           <?php echo $result !== false ? '<span class="success">✓ работает</span>' : '<span class="error">✗ не работает</span>'; ?>
        </p>
    <?php endforeach; ?>
    
    <h2>Проверка переменных сервера</h2>
    <?php
    $server_vars = ['SERVER_SOFTWARE', 'REQUEST_METHOD', 'SCRIPT_NAME', 'PHP_SELF', 'DOCUMENT_ROOT'];
    foreach ($server_vars as $var): ?>
        <p><strong><?php echo htmlspecialchars($var); ?>:</strong> 
           <?php echo isset($_SERVER[$var]) ? htmlspecialchars($_SERVER[$var]) : '<span class="warning">не установлена</span>'; ?>
        </p>
    <?php endforeach; ?>
    
    <h2>Ссылки для тестирования</h2>
    <ul>
        <li><a href="test_basic.php">Базовый тест PHP</a></li>
        <li><a href="test_db_connection.php">Тест подключения к базе данных</a></li>
        <li><a href="test.php">Обновленный тест PHP</a></li>
        <li><a href="diagnostic_safe.php">Безопасная диагностика</a></li>
        <li><a href="index_safe.php">Безопасная версия главной страницы</a></li>
        <li><a href="update_database.php">Обновление базы данных</a></li>
    </ul>
</body>
</html>