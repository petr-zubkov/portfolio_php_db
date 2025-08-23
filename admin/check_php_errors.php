<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Проверка PHP ошибок</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #d1ecf1; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Проверка PHP ошибок и настроек</h1>';

// Проверяем версию PHP
echo '<div class="info"><strong>Версия PHP:</strong> ' . phpversion() . '</div>';

// Проверяем расширения
$required_extensions = ['mysqli', 'json', 'session'];
echo '<div class="info"><strong>Расширения PHP:</strong></div>';
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext) ? '✓' : '✗';
    echo "<div>$loaded $ext</div>";
}

// Проверяем настройки PHP
echo '<div class="info"><strong>Настройки PHP:</strong></div>';
$php_settings = [
    'display_errors' => ini_get('display_errors'),
    'error_reporting' => ini_get('error_reporting'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize')
];

foreach ($php_settings as $key => $value) {
    echo "<div>$key: $value</div>";
}

// Проверяем подключение к БД
echo '<div class="info"><strong>Подключение к базе данных:</strong></div>';
try {
    require_once '../config.php';
    
    if ($conn && !$conn->connect_error) {
        echo '<div>✓ Подключение успешно</div>';
        
        // Проверяем таблицу
        $table_check = $conn->query("SHOW TABLES LIKE 'settings'");
        if ($table_check && $table_check->num_rows > 0) {
            echo '<div>✓ Таблица settings существует</div>';
            
            // Проверяем данные
            $data_check = $conn->query("SELECT COUNT(*) as count FROM settings");
            $count = $data_check->fetch_assoc()['count'];
            echo "<div>Записей в таблице: $count</div>";
            
            // Пробуем простой запрос
            $test_query = "UPDATE settings SET site_title = 'Test " . time() . "' WHERE id = 1";
            $test_result = $conn->query($test_query);
            
            if ($test_result) {
                echo '<div>✓ Тестовый UPDATE выполнен</div>';
                echo '<div>Затронуто строк: ' . $conn->affected_rows . '</div>';
            } else {
                echo '<div class="error">✗ Ошибка UPDATE: ' . $conn->error . '</div>';
            }
        } else {
            echo '<div class="error">✗ Таблица settings не существует</div>';
        }
    } else {
        echo '<div class="error">✗ Ошибка подключения: ' . ($conn->connect_error ?? 'Unknown error') . '</div>';
    }
} catch (Exception $e) {
    echo '<div class="error">✗ Исключение: ' . $e->getMessage() . '</div>';
}

// Проверяем права на запись
echo '<div class="info"><strong>Права на запись:</strong></div>';
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    if (mkdir($log_dir, 0755, true)) {
        echo '<div>✓ Папка logs создана</div>';
    } else {
        echo '<div class="error">✗ Не удалось создать папку logs</div>';
    }
} else {
    echo '<div>✓ Папка logs существует</div>';
}

$log_file = $log_dir . '/test.log';
if (file_put_contents($log_file, "Test\n")) {
    echo '<div>✓ Запись в файл успешна</div>';
    unlink($log_file);
} else {
    echo '<div class="error">✗ Ошибка записи в файл</div>';
}

echo '<br><a href="index.php">Вернуться в админ-панель</a>';
echo '</body></html>';
?>